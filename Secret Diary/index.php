<?php

  $dburl = "param";
  $dbname = "param";
  $dbpassword = "param";
  $dbuser = "param";

  $link = mysqli_connect($dburl, $dbname, $dbpassword, $dbuser);

  if (mysqli_connect_error()) {
    die("There was an error connecting to the database.");
  }

  session_start();

  if(array_key_exists("user_email", $_COOKIE) || array_key_exists("email", $_SESSION)) {
      header("Location: login.php");
      exit();

  } else if(!array_key_exists("user_email", $_COOKIE)) {
  if($_POST) {

    $email = $_POST["email"];
    $password = $_POST["password"];

    $checkQuery = "SELECT `email`,`diary` FROM `users` WHERE `email` = (?) LIMIT 1";
    $checkStmt = $link->prepare($checkQuery);
    $checkStmt->bind_param("s",$email);
    $checkStmt->execute();

    $checkStmt->store_result();
    $checkStmt->bind_result($userEmail,$userDiary);

      if($checkStmt->fetch()) {
        $_SESSION["email"] = $email;
          if(isset($_POST["check"])) {
           setcookie("user_email",$email,time() + 60*60); 
           header("Location: login.php");
           exit();
          }
         else {
           header("Location: login.php");
           exit();
         }
      } else {
        $query = "INSERT INTO users (email,password) VALUES (?, ?)";
        $stmt = $link->prepare($query);
        $stmt->bind_param("ss",$email,$password);
        $stmt->execute();

        $query2 = "SELECT `id` FROM `users` WHERE `email` = (?) LIMIT 1";
        $stmt2 = $link->prepare($query2);
        $stmt2->bind_param("s",$email);
        $stmt2->execute();

        $stmt2->store_result();
        $stmt2->bind_result($userId);

        while($stmt2->fetch()) {
          $hashId = hash("md5", $userId, false);
          $hashSaltedPassword = hash("md5", $hashId.$password, false);
          $hashQuery = "UPDATE `users` SET `password` = (?) WHERE `id` = (?) LIMIT 1";
          $hashStmt = $link->prepare($hashQuery);
          $hashStmt->bind_param("si",$hashSaltedPassword,$userId);
          $hashStmt->execute();

          header("Location: registered.php");
        }   
        
      }
  } else {
  }

  

  }







?>

<!DOCTYPE html>
<html lang="en">

  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">

    <style>

      .jumbotron {
        height: 100vh;
        background-image: url('1.jpg');
        background-size: 100% 100%;
        margin-bottom: 0px;
      }

      #center-square {
        padding: 40px 30px;
        background-color: rgba(0,0,0,0.6);
        border-radius: 10%;
        margin: 100px auto ;
        height: 500px;
        width: 500px;
      }

      #center-square h1 {
        color: white;
        font-size: 550%;
        text-align: center;
        font-family: "Market", "Monaco", "monospace";
      }

      #center-square p {
        color: white;
        text-align: center;
        font-size: 120%;
        font-family: "Market", "Monaco", "monospace";
      }

      label {
        color: white;
        text-align: center;
        font-size: 120%;
        font-family: "Market", "Monaco", "monospace";
      }

      .wrapper {
        margin: 6px 152px;
      } 

      .wrapper-btn {
        margin: 0 170px;
      }

    </style>


  </head>


  <body>


    <div class="jumbotron jumbotron-fluid">
      <div class="container">
        <div id="center-square">        
        <h1 class="display-3">TextShare</h1>
        <p class="lead">Sign up, log in and start sharing text.</p>
          <form method="POST">
            <div class="form-group">
              <label for="email">Email address</label>
              <input type="text" class="form-control" id="email" name="email" aria-describedby="emailHelp" placeholder="Enter email">
              <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
            </div>
            <div class="form-group">
              <label for="password">Password</label>
              <input type="password" class="form-control" id="password" name="password" placeholder="Password">
            </div>
            <div id="warning-section"></div>
            <div class="form-check wrapper">
              <label class="form-check-label">
                <input type="checkbox" id="save" name="check" class="form-check-input">
                Stay logged in
              </label>
            </div>
            <button type="submit" name="submit" class="btn btn-primary btn-success wrapper-btn">Submit</button>
          </form>
        </div>
      </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
    <script
    src="https://code.jquery.com/jquery-3.2.1.min.js"
    integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
    crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>

    <script>

    function validEmail(email) {
      var regex = /^(([^<>()\[\]\.,;:\s@\"]+(\.[^<>()\[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;

      return regex.test(email);
    }

    function cleanWarnings() {
      $("#center-square").height(420);
      $("#warning-section").attr("class","");
      $("#warning-section").attr("role","");
      $("#warning-section").html("");
    }

    function errorMessage(error,errorCount) {
      $("#warning-section").attr("class","alert alert-danger");
      $("#warning-section").attr("role","alert");
      $("#warning-section").html(error);
      $("#center-square").height($("#center-square").height()+50+(20*errorCount));
    }

    $("form").submit(function(e) {

      cleanWarnings();

      var error = "";
      var errorCount = 0;

      if(!validEmail($("#email").val())) {
        error += "E-mail is not valid.<br>"; 
        errorCount++;
      }

      if($("#email").val() == "") {
        error += "Fill the following field: E-mail<br>";
        errorCount++;
      }

      if($("#password").val() == "") {
        error += "Fill the following field: Password<br>";
        errorCount++;
      }

      if(error != "") {
        e.preventDefault();
        errorMessage(error,errorCount);
        errorCount = 0;
      }
    });


    </script>



  </body>
</html>