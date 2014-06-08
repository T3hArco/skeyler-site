<?php
class DB
{
  private static $conn = null;
  protected static $transLevel = 0;

  public static function init($connName = 'main') {
    self::connect($connName);
  }

  public static function connect() {
    global $Config, $isJson;
    if (self::$conn) {
      return true;
    }
    // Connects to DB. If you don't do a try-catch it will literally echo the password on error
    try {
      $dbInfo = $Config['db'];
      $dsn = $dbInfo['lang'] . ':dbname=' . $dbInfo['dbName'] . ';host=' . $dbInfo['host'];
      $conn = new PDO($dsn, $dbInfo['user'], $dbInfo['pass']);
      self::$conn = $conn;
    } catch (Exception $e) {
      header('HTTP/1.1 500 Internal Server Error');
      global $Page;
      if (!isset($Page) || !$isJson) {
        echo 'Failed to connect to database. Oh no!';
      }
      else {
        //        $Page->json['status'] = 'failed';
        Notice::error('Database connection failed. Oh no!');
      }
      exit;
    }
    return true;
  }

  /**
   * Runs a query
   *
   * @param (optional) $tableName = the table to autofix results for
   * @param $query = the query to exec
   * @param #2-INF = args
   * @return PDOStatement | DBStatement
   * @example
   *   $DB->q('SELECT ?, ?;', 123, 456)->fetch()
   *   $DB->q('SELECT :a, :b;', array('a'=>123, 'b'=>456))->fetch()
   *   $DB->q('users', 'SELECT * FROM users WHERE id = 1;')->fetch()
   */
  public static function q($query) {
    global $Config;
    $s = microtime(1);
    $args = func_get_args();
    $query = array_shift($args);

    if (count($args) == 1 && is_array($args[0])) {
      $args = $args[0];
    }
    $prep = self::$conn->prepare($query);
    if (!$prep && $Config['debugErrors']) {
      self::debugQuery('prep is false', $query, $args);
    }
    $prep->execute($args);
    if ($Config['debug']) {
      Notice::debug($query . BR . round((microtime(1) - $s) * 1000) . 'ms');
    }
    $errorInfo = $prep->errorInfo();
    if ($errorInfo[1]) {
      if ($Config['displayErrors']) {
        trigger_error('DB error: ' . var_export(array($errorInfo, $query, $args), 1), E_USER_ERROR);
      }
      else {
        trigger_error('DB error: ' . var_export($errorInfo, 1), E_USER_ERROR);
      }
    }

    return $prep;
  }


  public static function lastInsertId() {
    return self::$conn->lastInsertId();
  }

  /**
   * Don't pass anything other than ids and sids to this. no strings!
   *
   * @param $arr
   * @return string - returns a the $arr of data in a save IN() WHERE clause
   * @throws Exception
   */
  public static function whereIn($arr) {
    if (count($arr) == 0) {
      throw new Exception('Length of whereIn() is 0! Make sure to check for 0 before using whereIn()');
    }
    $arr = array_unique($arr);
    $safeArr = array();
    foreach ($arr as $val) {
      // we cant use mysql_real_escape_string() because that tries to connect to mysql_* and sometimes fails
      //$safeArr[] = '\'' . mysql_real_escape_string($val) . '\'';
      $safeArr[] = '\'' . addcslashes($val, "\\\"'") . '\'';
    }
    return implode(',', $safeArr);
  }

  public static function beginTransaction() {
    if (self::$transLevel == 0) {
      $out = self::$conn->beginTransaction();
    }
    else {
      $out = self::$conn->exec('SAVEPOINT LEVEL' . self::$transLevel);
    }
    self::$transLevel += 1;
    return $out;
  }

  /**
   * @return mixed
   * @throws Exception
   */
  public static function commit() {
    if (self::$transLevel <= 0) {
      throw new Exception('DB::transLevel just hit -1. what the heck did you do');
      exit;
    }
    self::$transLevel -= 1;

    if (self::$transLevel == 0) {
      $out = self::$conn->commit();
    }
    else {
      $out = self::$conn->exec('RELEASE SAVEPOINT LEVEL' . self::$transLevel);
    }
    return $out;
  }

  /**
   * @return mixed
   * @throws Exception
   */
  public static function rollback() {
    if (self::$transLevel <= 0) {
      throw new Exception('DB::transLevel just hit -1. what the heck did you do');
      exit;
    }
    self::$transLevel -= 1;

    if (self::$transLevel == 0) {
      $out = self::$conn->rollback();
    }
    else {
      $out = self::$conn->exec('ROLLBACK TO SAVEPOINT LEVEL' . self::$transLevel);
    }
    return $out;
  }

  /**
   * @param $tableName
   * @param $id
   * @param string $idCol
   * @return mixed
   */
  public static function getId($tableName, $id, $idCol = 'id') {
    $out = self::getIds($tableName, array($id), $idCol);
    if (!$out || count($out) == 0) {
      return null;
    }
    return $out[$id];
  }

  /**
   * @param $tableName
   * @param $ids
   * @param string $idCol
   * @return array
   */
  public static function getIds($tableName, $ids, $idCol = 'id') {
    if (!$ids || count($ids) == 0) {
      return array();
    }
    $query = 'SELECT * FROM ' . $tableName . ' WHERE ' . $idCol . ' IN(' . implode(', ', $ids) . ');';
    return populateIds(DB::q($tableName, $query)->fetchAll(), $idCol);
  }


  /**
   * @param $tableName
   * @param null $wheres
   * @param array $options[limit,offset,sort]
   * @return mixed
   */
  public static function find($tableName, $wheres = null, $options = array()) {
    $whereStr = '';
    $sortStr = '';
    $limitStr = '';
    if ($wheres) {
      $whereStr = ' WHERE ' . implode(' AND ', array_map(function ($name) {
          return $name . ' = :' . $name;
        }, array_keys($wheres)));
    }
    if (isset($options['limit'])) {
      if (isset($options['offset'])) {
        $limitStr = ' LIMIT ' . $options['offset'] . ', ' . $options['limit'];
      }
      else {
        $limitStr = ' LIMIT ' . $options['limit'];
      }
    }
    if (isset($options['sort'])) {
      $sortStr = ' ORDER BY ' . $options['sort'];
    }
    $query = 'SELECT * FROM ' . $tableName . $whereStr . $sortStr . $limitStr . ';';
    if ($wheres) {
      return DB::q($query, $wheres)->fetchAll();
    }
    else {
      return DB::q($query)->fetchAll();
    }
  }

  /**
   * @param $tableName
   * @param $wheres
   * @param array $options
   */
  public static function findOne($tableName, $wheres, $options = array()) {
    $options['limit'] = 1;
    $out = self::find($tableName, $wheres, $options);
    if (!$out) {
      return null;
    }
    return $out[0];
  }

  public static function writeLimit($perPage = 50, $pageId = 1) {
    return ' LIMIT ' . $perPage * ($pageId - 1) . ', ' . $perPage;
  }

}