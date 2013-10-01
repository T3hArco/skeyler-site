<?php

function jsonToArray($json)
{
  return json_decode($json, true);
}

function doCurl($act)
{
  $curl = curl_init();
  $goto = 'http://www.mineplaycraft.com/mpc/forums/api.php?act=' . $act;

  curl_setopt_array($curl, array(
      CURLOPT_RETURNTRANSFER => 1,
      CURLOPT_URL => $goto
  ));

  $resp = curl_exec($curl);

  curl_close($curl);    

  return $resp;
}

function parseForums()
{
  $out = "";
  $resp = doCurl("showboards");

  $boardArray = jsonToArray($resp);

  for($i = 0; $i < count($boardArray); $i++)
  {
    for($j = 0; $j < count($boardArray[$i]) / 2; $j++)
    {
      if($j == 0)
      {
        $out .= "<a href='?act=viewforum&id=" . $boardArray[$i][$j] . "'>";
      }
      else if($j == count($boardArray[$i]) / 2)
      {
        $out .= $boardArray[$i][$j] . " ";
      }
      else
      {
        $out .= $boardArray[$i][$j] . "</a>";
      }
    }
    $out .= "<br />";
  }

  return $out;
}

function parsePosts($id)
{
  $out = "";
  $resp = doCurl("showposts&id=" . $id);

  $postArray = jsonToArray($resp);

  for($i = 0; $i < count($postArray); $i++)
  {
    for($j = 0; $j < count($postArray[$i]) / 2; $j++)
    {
      if($j == 0)
      {
        $out .= "<a href='?act=viewpost&id=" . $postArray[$i][$j] . "'>";
      }
      else if($j == count($postArray[$i]) / 2)
      {
        $out .= $postArray[$i][$j] . "&nbsp;";
      }
      else
      {
        $out .= $postArray[$i][$j] . "</a>";
      }
    }
  }

  return $out;
}

if(!isset($_GET['act'])) $_GET['act'] = "";

switch($_GET['act'])
{
  case 'viewforum':

    if(!isset($_GET['id'])) header("Location: index.php");
    $id = mysql_real_escape_string($_GET['id']);

    echo parsePosts($id);

    break;

  case 'home':
  default:
    echo parseForums();
    break;
}

?>