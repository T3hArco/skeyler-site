<?php
class Steam
{

  public static function fetchAPI($interfaceName, $methodName, $version = 1, $qs = array(), $deepness = '', $postData = null) {
    global $Config;
    if (isset($qs['key'])) {
      $qs['key'] = $Config['steamApiKey'];
    }
    $query = http_build_query($qs);
    $url = 'http://api.steampowered.com/' . $interfaceName . '/' . $methodName . '/v' . $version . '?' . $query;
    if ($postData) {
      $json = curlGet($url, $postData);
    }
    else {
      $json = curlGet($url);
    }
    if (!$json) {
      throw new ErrorException('Steam returned blank content');
    }
    $content = json_decode($json, true);
    if (!$content) {
      var_dump($json);
      throw new ErrorException('Steam json response could not be parsed');
    }
    if ($deepness) {
      $deepnessSplit = explode('.', $deepness);
      for ($i = 0; $i < count($deepnessSplit); $i++) {
        if (!isset($content[$deepnessSplit[$i]])) {
          $deepStr = '';
          for ($j = 0; $j < $i; $j++) {
            $deepStr .= $deepnessSplit[$j] . '.';
          }
          $deepStr .= '-' . $deepnessSplit[$i] . '-';
          throw new ErrorException('Steam json response was not deep enough! We must go deeper! Deepness: ' . $deepStr);
        }
        $content = $content[$deepnessSplit[$i]];
      }
    }
    return $content;
  }

  public static function getUserProfile($steamId64s) {
    $steamId64s = (array) $steamId64s;
    $qs = array(
      'key' => 1,
      'steamIds' => implode(',', $steamId64s),
    );
    return self::fetchAPI('ISteamUser', 'GetPlayerSummaries', 2, $qs, 'response.players');
  }

  public static function isValidSteam64($steam64) {
    return (strlen($steam64) == 17 && substr($steam64, 0, 4) == '7656');
  }

  /**
   * Converts a steam64 to a STEAM_ID
   *
   * @param string $profileId
   * @return string steam id
   */
  public static function steam64ToSTEAM($profileId) {
    $steamPieces = array(0 => '0', 1 => '', 2 => '');
    $steamPieces[1] = substr($profileId, -1) % 2;
    $steamPieces[2] = ((int) bcsub($profileId, '76561197960265728') - $steamPieces[1]) / 2;
    $steamId = implode(':', $steamPieces);
    return $steamId;
  }


}





