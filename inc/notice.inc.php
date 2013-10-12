<?php
class Notice
{
  public static $classes = array(
    'info' => 'success',
    'error' => 'danger',
    'debug' => 'info',
  );


  public static function add($str, $type)
  {
    global $local, $Page;
    if ($Page && $Page->echoed('header')) {
      self::write($str, $type);
    }
    if (!isset($local['notices'])) {
      $local['notices'] = array();
    }
    if (!isset($local['notices'][$type])) {
      $local['notices'][$type] = array();
    }
    $local['notices'][$type][] = $str;
  }

  public static function message($str)
  {
    self::add($str, 'info');
  }

  public static function success($str)
  {
    self::add($str, 'success');
  }

  public static function error($str)
  {
    self::add($str, 'error');
  }

  public static function debug($str)
  {
    self::add($str, 'debug');
  }

  public static function write($str, $type)
  {
    echo '<div class="alert alert-' . self::getTypeClass($type) . '">' . $str . '</div>';
  }

  public static function getTypeClass($type)
  {
    if (isset(self::$classes[$type])) {
      return self::$classes[$type];
    }
    return $type;
  }

  public static function writeNotices()
  {
    global $local;
    if (isset($local['notices'])) {
      foreach ($local['notices'] as $type => $notices) {
        foreach ($notices as $str) {
          self::write($str, $type);
        }
      }
      unset($local['notices']);
    }

  }


}
