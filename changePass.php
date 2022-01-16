<?php
    session_start();?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Change Password</title>
  <link href="https://fonts.googleapis.com/css?family=Oswald|Pontano+Sans&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Quicksand&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Raleway&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Parisienne&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="general.css" type="text/css">
    <link rel="stylesheet" href="form.css" type="text/css">
    <meta name="description" content="A social network for those who have family with special needs looking for support and resources from others in similar situations. Siblings Helping Other Siblings is a non-profit organization that seeks to connect people and create
      a more aware and knowledgable community. Although the website was specifically made with those who have special needs family members in mind, anyone interested in creating a support network is welcome to join and participate.">
    <meta name="keywords" content="siblings, family, siblings helping other siblings, SHOS, special needs, disabilities, disability, brother, sister, family help, care, family care, community">
  <link rel="stylesheet" href="sign.css" type="text/css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" type="image/png" href="images/favicon.ico">
</head>
<body>
  <?php
      $server = "localhost";
      $username = "u640129124_admin";
      $password= "accessDataAdmin";
      $db = "u640129124_LaurenDatabase";

      $connection = new mysqli($server,$username,$password, $db);
      if($connection->connect_error){
        die("Connection Failed");
      }

      $formStatus="";

      if(isset($_POST["submit"])){
        $pass=clean($_POST["password"]);
        $conPass=clean($_POST["confirmPassword"]);
        $curPass=clean($_POST["curPass"]);
        if(empty($pass) || empty($conPass) || empty($curPass)){
          $formStatus="Please fill out all fields";}
        else{
          $user=$_SESSION["user"];
          $accountPass="";
          $result=$connection->query("SELECT pass FROM memberLogin WHERE user='$user'");
            while($row=$result->fetch_row()){
              $accountPass=$row[0];
            }
          if(password_verify($curPass,$accountPass)){
            if(strlen($pass)<8){$formStatus="Password must be at least 8 characters long";}
            else{
              if($pass!=$conPass){
                $formStatus="Please ensure passwords match";
              }
              else{
                $pass=password_hash($pass,PASSWORD_DEFAULT);
                $login = "UPDATE memberLogin SET pass='$pass' WHERE user='$user'";

                if ($connection->query($login) === TRUE) {
                $formStatus= "New records created successfully";
                $connection->close();
                echo "<script>
                  window.location.href='loggedIn.php'
                  </script>";}
                else{
                  $formStatus= "Error";
                }
              }
            }
          }
          else{
            $formStatus= "Incorrect Password";
          }
        }
      }

      function clean($data){
        $data=trim($data);
        $data=stripslashes($data);
        $data=htmlspecialchars($data);
        return $data;
      }

      $connection->close();
  ?>

  <div class="gen">
    <div class="topSect topSectFix"><span class="top topSmall"><div id="left" class="leftFix"><p id="title" class="titleSmall" onclick="window.location.href='index.php'">SHOS</p></div>
    <div id="right"><p class="menu menuSlideFix">&#8595;</p></div></span>
    <div class="nav navFix"><div class="buttonCent">
      <span class="button" onclick="window.location.href='index.php'"><p class="buttonTxt">Home</p></span>
      <span class="button" onclick="window.location.href='about.php'"><p class="buttonTxt">About</p></span>
      <span class="button" onclick="window.location.href='resources.html'"><p class="buttonTxt">Resources</p></span>
    <!--<span class="button" onclick="window.location.href='forumSelect.php'"><p class="buttonTxt">Forum</p></span>-->
      <span class="button" onclick="window.location.href='members.php'"><p class="buttonTxt">Members</p></span>
    </div></div></div>
  <div id="photoCont"><div id="backPhotoMem"></div>
  <div id="intro"><div id="introCont">
  <div class="loginTable"><p class="listTitle">Change Password</p>
  <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
      <div class="insideForm flexForm"><div class="blockForm">
        <div class="loginRow"><label>Current Password: </label>
        <input class="input" type="password" name="curPass"></div>

        <div class="loginRow"><label>Password:</label>
        <input class="input" type="password" name="password"></div>

        <div class="loginRow"><label>Confirm Password:</label>
        <input class="input" type="password" name="confirmPassword"></div></div></div>

        <?php echo "<p class='statusForm'>". $formStatus . "</p>"; ?>

        <div class="loginRowButton"><input class="submitButton" type="submit" name="submit"></div></form>
      </div>
    </div>

  <div class="spaceHolder">
  <div class="whiteBar"><div class="bottomTxt">
    <p class="contactInfoBottom">Siblings Helping Other Siblings (SHOS)</p><p class="contactInfoBottom"> Email: laurencowell042502@gmail.com</p><p class="contactInfoBottom">Property of Lauren and Aiden Cowell</p>
  <p class="copyright">Made by Web Styles FL.</p></div></div></div></div>
</div></div>
</body>
<script>
var arrow=document.getElementsByClassName("menu")[0];
arrow.addEventListener("click", function(){menuSlide()});

function menuSlide(){
    var nav=document.getElementsByClassName("nav")[0];
    if(nav.classList.contains("navTall")){
      nav.classList.remove("navTall");
      arrow.classList.remove("menuFix");
      arrow.classList.add("menuRotateBack");
    }
    else{arrow.classList.remove("menuRotateBack");
      arrow.classList.add("menuFix");
      nav.classList.add("navTall");
    }
}
</script>
</html>
