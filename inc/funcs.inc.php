<?php


/**
 * Shorthand html sanitizing
 *
 * @param string $str
 */
function ent($str)
{
  return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// shorthand for reversing ent()
function unent($str)
{
  return htmlspecialchars_decode($str);
}


/**
 * Redirects the user
 *
 * @param string $page = the page to redir to
 * @param bool $dontExit whether to kill execution after the redir
 * @todo Make this cross-site safe
 */
function redirect($page, $dontExit = false)
{
  header('Location: ' . $page);
  if (!$dontExit) {
    exit;
  }
}


/**
 * Creates a random str that is $len chars long using chars from $alphabet
 *
 * @param int $len = the length of the str to return
 * @param string $alphabet = the list of chars to use
 * @return The random string
 */
function randomStr($length = 32, $alphabet = 'abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789')
{
  $str = '';
  $alphaLength = strlen($alphabet) - 1;
  for ($a = 0; $a < $length; $a++) {
    $n = mt_rand(0, $alphaLength);
    $str .= $alphabet[$n];
  }
  return $str;
}


/**
 * Generates a SHA-256 hash
 *
 * @param string $str
 */
function sha2($str)
{
  return hash('sha256', $str);
}


/**
 * Sets each row's key value as its id field value
 *
 * @param array $data = array of assoc arrays
 * @param string $idField = the name of the field to treat as the id
 * @example: populateIds(array(
 *   array('id'=>3, name='bob'),
 *   array('id'=>7, name='george'),
 * ));
 * returns array(
 *   3 => array('id'=>3, name='bob'),
 *   7 => array('id'=>7, name='george'),
 * );
 */
function populateIds($data, $idField = 'id')
{
  $out = array();
  foreach ($data as $row) {
    $out[$row[$idField]] = $row;
  }
  return $out;
}


/**
 * Grabs the specified field value from each array in $data
 *
 * @param array $data
 * @param string $fieldName
 * @param bool $unique
 */
function eachField($data, $fieldName, $unique = true)
{
  $out = array();
  foreach ($data as $row) {
    $out[] = $row[$fieldName];
  }
  if ($unique) {
    $out = array_unique($out);
  }
  return $out;
}


/**
 * Adds a prefix and a suffix to each string inside $arr
 *
 * @param $arr    = the arr of strings to alter
 * @param $prefix = the str to add before each item
 * @param $suffix = the str to append after each item
 */
function presuf($arr, $prefix = '', $suffix = '')
{
  foreach ($arr as &$str) {
    $str = $prefix . $str . $suffix;
    unset($str);
  }
  return $arr;
}


/**
 * Outputs the singular or plural form based on whether $count is sing/plur
 *
 * @param int $count
 * @param string $sing = what to output on singular
 * @param string $plur = what to output on plural
 */
function singPlur($count, $sing = '', $plur = 's')
{
  return ($count == 1 ? $sing : $plur);
}


/**
 * Loads a single page returns the results
 *
 * @param string $url
 * @return string output
 */
function curlGet($url)
{
  $c = curl_init();
  curl_setopt($c, CURLOPT_URL, $url);
  curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
  $output = curl_exec($c);
  curl_close($c);
  return $output;
}

/**
 * Loads a single page using the POST method
 *
 * @param string $url
 * @param array $postData = array of post data
 * @return string output
 */
function curlPost($url, $postData)
{
  $c = curl_init();
  curl_setopt($c, CURLOPT_URL, $url);
  curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($c, CURLOPT_POST, 1);
  curl_setopt($c, CURLOPT_POSTFIELDS, $postData);
  $output = curl_exec($c);
  curl_close($c);
  return $output;
}


/**
 * Fetches a page of json and returns it as an assoc array
 *
 * @param string $url
 * @return array data
 */
function getJson($url)
{
  $content = curlGet($url);
  return json_decode($content, true);
}


function view($variables)
{
  global $controllerName, $local, $isJson;

  foreach ($variables as $key => $val) {
    $local[$key] = $val;
  }

  // if json, just exit. we don't need a view for it
  if ($isJson) {
    exit;
  }

  $includePath = realpath(ROOT . '/views/' . $controllerName);
  if ($includePath && strpos($includePath, realpath(ROOT . '/views/')) === 0) {
    Notice::writeNotices();
    include $includePath;
  } else {
    // exploit attempt or missing view file
    throw new ErrorException('Failed to include view');
  }
}


/**
 * Writes time length
 *
 * @param int length - amount of seconds
 */
function writeTimeLength($length)
{
  if ($length < 0 || !$length) {
    $length = 0;
  }
  $lengthDisplay = '';
  $amount = 0;

  if ($length < 60) {
    $amount = $length;
    $lengthDisplay = $length . ' second' . singPlur($amount);
  } else if ($length < 60 * 60) {
    $amount = round($length / 60);
    $lengthDisplay = $amount . ' minute' . singPlur($amount);
  } else if ($length < 60 * 60 * 25) {
    $amount = round($length / 60 / 60);
    $lengthDisplay = $amount . ' hour' . singPlur($amount);
  } else if ($length < 60 * 60 * 24 * 50) {
    $amount = round($length / 60 / 60 / 24);
    $lengthDisplay = $amount . ' day' . singPlur($amount);
  } else {
    $amount = round($length / 60 / 60 / 24 / (365 / 12) * 10) / 10;
    $lengthDisplay = $amount . ' month' . singPlur($amount);
  }
  return $lengthDisplay;
}

function writeDate($timestamp)
{
  return date('M d, Y g:ia', $timestamp);
}

function writeDateEng($timestamp) {
  global $now;

  // if > 2 days old, show the full date
  if($now - $timestamp > 60 * 60 * 24 * 2) {
    return writeDate($timestamp);
  } else {
    return writeTimeLength($now - $timestamp) . ' ago';
  }
}


function writePageNav($curPageId, $totalPages, $classes = array())
{
  if($totalPages <= 1) {
    return '';
  }
  $out = '<ul class="pageNav ' . implode(' ', (array)$classes) . '">';
  $out .= '<li><span>Page ' . $curPageId . ' of ' . $totalPages . '</span></li>';

  if ($curPageId > 1) {
    $out .= '<li class="firstPage"><a href="#">First</a></li>';

    if ($curPageId > 2) {
      $out .= '<li class="prevPage"><a href="#">&lt;</a></li>';
    }
  }

  $minPageId = max($curPageId - 3, 1);
  $maxPageId = min($curPageId + 3, $totalPages);

  for ($i = $minPageId; $i <= $maxPageId; $i++) {
    if ($i == $curPageId) {
      $out .= '<li class="curPage"><span>' . $i . '</span></li>';
    } else {
      $out .= '<li class=""><a href="#">' . $i . '</a></li>';
    }
  }

  if ($curPageId < $totalPages) {
    if ($curPageId < $totalPages - 1) {
      $out .= '<li class="nextPage"><a href="#">&gt;</a></li>';
    }
    $out .= '<li class="lastPage"><a href="#">Last</a></li>';
  }

  $out .= '</ul>';
  return $out;


}



