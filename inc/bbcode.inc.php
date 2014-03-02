<?php
class BBCode
{
  public static $basicReplacements = array(
    'b' => 'strong',
    'strong' => 'strong',
    'i' => 'em',
    'em' => 'em',
    'u' => 'u',
    's' => 's',
    'p' => 'p',
    'heading' => 'h3',
    'title' => 'h3',
  );


  public static function parseBasic($str)
  {
    $validReplacements = array();
    foreach (self::$basicReplacements as $k => $v) {
      $validReplacements[] = $k;
    }
    $pattern = '#\[(' . implode('|', $validReplacements) . ')\]((?:.|\n|\r)*?)\[\/\1\]#i';

    return preg_replace_callback($pattern, function ($matches) {
      $tag = BBCode::$basicReplacements[$matches[1]];
      return '<' . $tag . '>' . BBCode::parseBasic($matches[2]) . '</' . $tag . '>';
    }, $str);
  }

  public static function parseUrl($str)
  {

    $pattern = '#\[url=&quot;(https?:\/\/.*?)&quot;\]((?:.|\n|\r)*?)\[\/url\]#i';

    //todo: decide if it needs to unent() before urlencode

    $str = preg_replace_callback($pattern, function ($matches) {
      return '<a href="' . $matches[1] . '">' . $matches[2] . '</a>';
    }, $str);

    $pattern2 = '#\[url\](https?:\/\/.*?)\[\/url\]#i';
    $str = preg_replace_callback($pattern2, function ($matches) {
      return '<a href="' . $matches[1] . '">' . $matches[1] . '</a>';
    }, $str);
    return $str;
  }

  public static function parseImg($str)
  {
    $pattern = '#\[img\](https?:\/\/.*?)\[\/img\]#i';
    return preg_replace_callback($pattern, function ($matches) {
      return '<img src="' . $matches[1] . '" />';
    }, $str);
  }

  public static function parseQuote($str)
  {
    $pattern = '#\[quote=&quot;([^"]+?)&quot; postid=&quot;([0-9]+)&quot;\]((?:.|\n|\r)*?)\[\/quote\]#i';
    return preg_replace_callback($pattern, function ($matches) {
      return '<blockquote><a class="postLink" href="/forums/thread.php?postId=' . $matches[2] . '">' . $matches[1] . ' posted:</a><br/>' . $matches[3] . '</blockquote>';
    }, $str);
  }

  public static function parseList($str)
  {
    $pattern = '#\[list\]((?:.|\n|\r)*?)\[\/list\]#i';
    return preg_replace_callback($pattern, function ($matches) {
      $content = $matches[1];
      $pattern2 = '#\[\*\]([^\n]*?\n)#i';
      $out = preg_replace_callback($pattern2, function ($matches2) {
        return '<li>' . $matches2[1] . '</li>';
      }, $content);
      return '<ul>' . $out . '</ul>';
    }, $str);
  }

  public static function parseYoutube($str)
  {
    $pattern = '#\[youtube\](?:http:\/\/(?:www)?\.youtube\.com\/watch\?v=)?([a-z0-9_-]+)\[\/youtube\]#i';
    return preg_replace_callback($pattern, function ($matches) {
      return '<iframe id="ytplayer" type="text/html" width="640" height="390" src="http://www.youtube.com/embed/' . $matches[1] . '" frameborder="0"></iframe><br/>';
    }, $str);
  }

  public static function parseCodeStart($str, &$codeTags)
  {
    $pattern = '#\[code\]((?:.|\n|\r)*?)\[\/code\]#i';
    return preg_replace_callback($pattern, function ($matches) use (&$codeTags) {
      $codeTags[] = $matches[1];
      return '<code' . count($codeTags) . '>';
    }, $str);
  }

  public static function parseCodeEnd($str, &$codeTags)
  {
    $pattern = '#<code(\d+)>#i';
    return preg_replace_callback($pattern, function ($matches) use (&$codeTags) {
      return '<pre>' . $codeTags[$matches[1] - 1] . '</pre>';
    }, $str);
  }


  public static function parse($bbcode)
  {
    // first ent() to make sure there's no html in there
    $out = ent($bbcode);

    // take out all code tags so we don't do any replacements on their contents

    $codeTags = array();

    $out = self::parseCodeStart($out, $codeTags);


    // then parse the basic (simple) tags
    $out = self::parseBasic($out);

    // then parse urls, img, quote, lists, youtube
    $out = self::parseUrl($out);
    $out = self::parseImg($out);
    $out = self::parseQuote($out);
    $out = self::parseList($out);
    $out = self::parseYoutube($out);


    // next to last, bring back all code tags
    $out = self::parseCodeEnd($out, $codeTags);

    // last, convert new lines to <br />
    $out = nl2br($out);

    return $out;
  }

}