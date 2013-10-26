<?php
/**
 * Saves/loads cache json data to a file in /cache/
 *
 * Note: If this ever breaks during a server move or whatever, it's because the
 * /cache/ directory needs to be running on the same user as PHP.
 * The easy way to do that is delete the /cache/ folder if it exists, then
 * in PHP use mkdir('path/to/forums/cache/'); ask obstipator about it if that doesn't work
 */
class Cache {

  /**
   * Converts a name to a valid file name
   *
   * @param string $name
   * @return string
   */
  private static function buildFileName($name) {
    return ROOT . '/cache/' . self::sanitizeName($name) . '.json';
  }

  /**
   * Sanitizes a file name
   *
   * @param string $name
   * @return string
   */
  private static function sanitizeName($name) {
    return preg_replace('#[^a-z0-9_\-/]#i', '', $name);
  }

  /**
   * Loads the data from a cache file
   *
   * @param string $name
   * @return mixed
   */
  public static function load($name) {
    $fileName = self::buildFileName($name);
    if(!file_exists($fileName)) {
      return false;
    }
    $contents = file_get_contents($fileName);
    $data = json_decode($contents, true);
    return $data;
  }

  /**
   * Saves data to cache
   *
   * @param string $name
   * @param mixed $data
   * @return bool
   */
  public static function save($name, $data) {
    $fileName = self::buildFileName($name);
    $contents = json_encode($data);
    file_put_contents($fileName, $contents);
    return true;
  }

  /**
   * Checks if a cache is fresh (true) or unfresh (false)
   *
   * @param string name
   * @param int $freshTime - amount of time a cache remains fresh
   * @return bool - true if fresh; false if needs to be recached
   */
  public static function isFresh($name, $freshTime = 86400) {
    $fileName = self::buildFileName($name);
    if(!file_exists($fileName)) {
      return false;
    }
    $lastModifiedTime = filemtime($fileName);
    $expirationTimestamp = $lastModifiedTime + $freshTime;
    $now = time();
    if($now > $expirationTimestamp) {
      return false;
    }
    return true;
  }

}