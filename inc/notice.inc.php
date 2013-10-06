<?php
class Notice
{
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
    $local['notices'][$type] = $str;
  }

  public static function message($str)
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
    echo '<div class="alert alert-' . ent($type) . '">' . ent($str) . '</div>';
  }

  public static function writeNotices()
  {
    global $local;
    if (isset($local['notices'])) {
      foreach ($local['notices'] as $type => $str) {
        self::write($str, $type);
      }
      unset($local['notices']);
    }

  }


}
