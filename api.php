<?php

session_start();

header('Content-Type: application/json');

error_reporting(E_ALL & ~E_NOTICE);

$con = mysql_connect("localhost", "root", "") or die("Failed to connect to server.");
$db = mysql_select_db("mpc") or die("Couldn't select database");

function randomString($length = 32)
{
  $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
  $pass = array();
  $alphaLength = strlen($alphabet) - 1;
  for ($i = 0; $i < $length; $i++) {
    $n = rand(0, $alphaLength);
    $pass[] = $alphabet[$n];
  }
  return implode($pass);
}


if (!isset($_GET['act'])) $_GET['act'] = null;
if (!isset($_SESSION['registered'])) $_SESSION['registered'] = false;
if (!isset($_SESSION['authenticated'])) $_SESSION['authenticated'] = false;

switch ($_GET['act']) {
  case 'authenticate':

    if ($_SESSION['authenticated'] == true) {
      $jsonify = array("Error" => "Already authenticated");

      echo json_encode($jsonify);
    } else {
      $username = mysql_real_escape_string($_GET['username']);
      $password = mysql_real_escape_string(sha1(md5($_GET['password'])));

      $query = mysql_query("SELECT * FROM users WHERE (username = '$username' AND password = '$password');");

      if (mysql_num_rows($query) == 1) {
        $token = mysql_fetch_row($query);
        $token = $token[6];

        $_SESSION['username'] = $username;
        $_SESSION['token'] = $token;
        $_SESSION['authenticated'] = true;

        $jsonify = array("Success" => "Authenticated", "Token" => $token);

        echo json_encode($jsonify);
      } else {
        $jsonify = array("Error" => "Invalid username/password");

        echo json_encode($jsonify);
      }
    }

    break;


  case 'showboards':
    $query = mysql_query("SELECT * FROM forums;");
    $jsonify = array();

    while ($row = mysql_fetch_array($query)) {
      array_push($jsonify, $row);
    }

    echo json_encode($jsonify);

    break;

  case 'showposts':
    $id = mysql_real_escape_string($_GET['id']);

    $query = mysql_query("SELECT * FROM posts WHERE forum = '$id';");
    $jsonify = array();

    while ($row = mysql_fetch_array($query)) {
      array_push($jsonify, $row);
    }

    echo json_encode($jsonify);

    break;

  case 'makethread':

    if ($_SESSION['authenticated'] == true) {
      $title = mysql_real_escape_string(strip_tags($_GET['title']));
      $body = mysql_real_escape_string(strip_tags($_GET['body']));
      $forumid = mysql_real_escape_string($_GET['forumid']);

      if (!is_numeric($forumid)) {
        $jsonify = array("Error" => "Not an ID");

        echo json_encode($jsonify);
        die();
      }

      $query = mysql_query("SELECT id FROM forums WHERE id = $forumid;");

      if (mysql_fetch_row($query) == 0) {
        $jsonify = array("Error" => "Forum does not exist");

        echo json_encode($jsonify);
        die();
      }

      $token = $_SESSION['token'];

      $query = mysql_query("SELECT users.id, users.user_token FROM users INNER JOIN posts ON users.id = posts.owner WHERE users.user_token = '$token';");
      $result = mysql_fetch_row($query);

      $userid = $result[0];

      $query = mysql_query("INSERT INTO posts (title, body, owner, childof, forum) VALUES ('$title', '$body', $userid, 0, $forumid);");

      $jsonify = array("Success" => "Topic created", "Title" => $title, "Body" => $body, "Forum ID" => $forumid);

      echo json_encode($jsonify);

    } else {
      $jsonify = array("Error" => "You need to be authenticated to do that!");

      echo json_encode($jsonify);
    }

    break;

  case 'deauthenticate':

    if ($_SESSION['authenticated'] == true) {
      session_destroy();

      $jsonify = array("Success" => "Deauthenticated");
      echo json_encode($jsonify);
    } else {
      $jsonify = array("Error" => "Not authenticated");
      echo json_encode($jsonify);
    }

    break;

  case 'register':

    if (!$_SESSION['registered']) {
      $email = mysql_real_escape_string($_GET['email']);
      $username = mysql_real_escape_string($_GET['username']);
      $password = mysql_real_escape_string(sha1(md5($_GET['password'])));
      $apikey = mysql_real_escape_string($_GET['apikey']);
      $ip = $_SERVER['REMOTE_ADDR'];

      if (!isset($_GET['email']) || !isset($_GET['username']) || !isset($_GET['password']) || !isset($_GET['apikey'])) {
        $result = array("Error" => "Missing fields");
        echo json_encode($result);

        die();
      }

      $query = mysql_query("SELECT * FROM api WHERE (`key` = '$apikey' AND `ip` = '$ip');");

      print(mysql_error());

      if (mysql_num_rows($query) == 0) {
        $result = array("Error" => "Invalid credentials");

        echo json_encode($result);
      } else {
        $query = mysql_query("SELECT username FROM users WHERE username = '$username';");

        if (mysql_num_rows($query) >= 1) {
          $result = array("Error" => "User already exists");
          echo json_encode($result);

          die();
        }

        $query = mysql_query("SELECT email FROM users WHERE email = '$email';");

        if (mysql_num_rows($query) >= 1) {
          $result = array("Error" => "Email already exists");
          echo json_encode($result);

          die();
        }

        $query = mysql_query("SELECT reg_ip FROM users where reg_ip = '$ip';");

        if (mysql_num_rows($query) >= 1) {
          $result = array("Error" => "Already registered");
          echo json_encode($result);

          die();
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
          $result = array("Error" => "Email invalid");
          echo json_encode($result);

          die();
        }

        $token = randomString();

        if ($query = mysql_query("INSERT INTO `users` (`username`, `email`, `password`, `reg_ip`, `user_token`) VALUES ('$username', '$email', '$password', '$ip', '$token');")) {
          $_SESSION['registered'] = true;

          $result = array("Success" => "Account registered");
          echo json_encode($result);
        } else {
          $result = array("Error" => "System error");
          echo json_encode($result);
        }
      }
    } else {
      $result = array("Error" => "Already registered");

      echo json_encode($result);
    }

    break;

  default:
    $result = array("Error" => "Invalid API");

    echo json_encode($result);
    break;

    mysql_close();
}