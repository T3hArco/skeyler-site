<?php
class DB extends PDO {
  /**
   * Runs a query
   *
   * @param $query = the query to exec
   * @param #2-INF = args
   * @example
   *   $DB->q('SELECT ?, ?;', 123, 456)->fetch()
   *   $DB->q('SELECT :a, :b;', array('a'=>123, 'b'=>456))->fetch()
   */
  public function q($query) {
    $s = microtime(1);
    $args = func_get_args();
    $query = array_shift($args);
    if(count($args) == 1 && is_array($args[0])) {
      $args = $args[0];
    }
    $prep = $this->prepare($query);
    $prep->setFetchMode(PDO::FETCH_ASSOC);
    $prep->execute($args);
    $errorInfo = $prep->errorInfo();
    if(isset($errorInfo[1])) {
      trigger_error('DB error: ' . var_export($errorInfo, 1), E_USER_ERROR);
    }
    return $prep;
  }
  
  /**
   * Runs a query multiple times with different params
   *
   * @param $query = the query to exec
   * @param $argListArr = an array of argument arrays
   * @example = $DB->mq(
   *   'INSERT INTO posts(author,content) VALUES(?,?);',
   *   array(
   *     array('admin','hello world'),
   *     array('world','hello admin')
   *   )
   * );
   */
  public function mq($query,$argListArr) {
    $prep = $this->prepare($query);
    $s = microtime(1);
    foreach($argListArr as $argList) {
      $s = microtime(1);
      $prep->execute($argList);
      $errorInfo = $prep->errorInfo();
      if(isset($errorInfo[1])) {
        trigger_error('DB error: ' . var_export($errorInfo, 1), E_USER_ERROR);
      }
    }
    return $prep;
  }
}
?>