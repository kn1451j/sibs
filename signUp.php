<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Sign Up</title>
  <link href="https://fonts.googleapis.com/css?family=Oswald|Pontano+Sans&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Quicksand&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Raleway&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Parisienne&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="general.css" type="text/css">
    <link rel="stylesheet" href="form.css" type="text/css">
  <link rel="stylesheet" href="sign.css" type="text/css">
    <link rel="stylesheet" href="signUp.css" type="text/css">
    <meta name="description" content="A social network for those who have family with special needs looking for support and resources from others in similar situations. Siblings Helping Other Siblings is a non-profit organization that seeks to connect people and create
      a more aware and knowledgable community. Although the website was specifically made with those who have special needs family members in mind, anyone interested in creating a support network is welcome to join and participate.">
    <meta name="keywords" content="siblings, family, siblings helping other siblings, SHOS, special needs, disabilities, disability, brother, sister, family help, care, family care, community">
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

      $status=1;
      $user="";
      $pass="";
      $formStatus="";
      $counter=0;


      if($_SERVER["REQUEST_METHOD"]=="POST"){
        $email=$_POST["email"];
        $pass=clean($_POST["password"]);
        $conPass=clean($_POST["confirmPassword"]);
        $user=clean($_POST["username"]);
        $first=clean($_POST["firstName"]);
        $last=clean($_POST["lastName"]);
        $time = strtotime($_POST['birthday']);
        if(empty($email) || empty($pass) || empty($conPass) || empty($user) || empty($first) || empty($last) || !isset($_POST['relationship'])){
          $formStatus="Please fill out all fields";}
        else{
          $birthday = strtotime($_POST['birthday']);
          $relationship=$_POST['relationship'];
          if($birthday){
            $birthday = date('Y-m-d', $birthday);
            $email=filter_var($email,FILTER_SANITIZE_EMAIL);
            if(filter_var($email,FILTER_VALIDATE_EMAIL)==false){
              $formStatus="Please enter a valid email address";
            }
            else{
              $emailCopies=mysqli_query($connection,"SELECT email FROM memberContact WHERE email='$email'");
              $number=mysqli_num_rows($emailCopies);
              if($number>0){
                $formStatus = "Account with this email already exists. If you don't remember your login information please reset your password.";
              }
              else{
                if(strlen($pass)<8){$formStatus="Password must be at least 8 characters long";}
                else{
                  if($pass!=$conPass){
                    $formStatus="Please ensure passwords match";
                  }
                  else{
                    $result=$connection->query("SELECT user FROM memberLogin");
                    while($row=$result->fetch_row()){
                      if($row[0]==$user){$counter=1;}
                    }
                    if($counter==1){
                      $formStatus="Username taken";
                    }
                    else{
                        $noSignUp=mysqli_query($connection,"SELECT email FROM noSignUp WHERE email='$email'");
                        $number=mysqli_num_rows($noSignUp);
                        if($number>0){
                          $formStatus="Your email is associated with a banned account. Please contact us.";
                        }
                        else{
                          $pass=password_hash($pass,PASSWORD_DEFAULT);
                          $login = "INSERT INTO memberLogin (user, pass, status) VALUES(?, ?, ?)";
                          $info = "INSERT INTO memberContact (id, first, last, email,birthday,relationship) VALUES(?, ?, ?, ?, ?, ?)";
                          $memLog = $connection->prepare($login);
                          $memLog->bind_param("ssi",$user,$pass,$status);
                          $memLog->execute();

                          $result=mysqli_query($connection,"SELECT id FROM memberLogin WHERE user='$user'");
                          while($row=mysqli_fetch_row($result)){
                            $memLog = $connection->prepare($info);
                            $memLog->bind_param("isssss",$row[0],$first,$last, $email, $birthday, $relationship);
                            $memLog->execute();
                            $memLog->close();
                            if($_POST['check']=="subscribe"){
                              $sameEmail=mysqli_query($connection,"SELECT id FROM subscribers WHERE email='$email'");
                              if(mysqli_num_rows($sameEmail)==0){
                                $newSub="INSERT INTO subscribers (id, email) VALUES (?,?)";
                                $newSub = $connection->prepare($newSub);
                                $newSub->bind_param("is",$row[0],$email);
                                $newSub->execute();
                              }
                            }
                          }

                          echo "<script>
                            window.location.href='members.php'
                            </script>";
                      }
                    }
                  }
                }
              }
            }
          }
          else{
            $formStatus="Invalid date.";
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
  <div class="loginTable"><p class="listTitle">Sign Up</p>
  <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
      <div class="insideForm flexForm"><div class="blockForm">
        <div class="loginRow"><label>First Name: </label>
        <input class="input" type="text" name="firstName"></div>

        <div class="loginRow"><label>Last Name: </label>
        <input class="input" type="text" name="lastName"></div>

        <div class="loginRow"><label>Email: </label>
        <input class="input" type="text" name="email"></div></div><div class="blockForm">

        <div class="loginRow"><label>Username:</label>
        <input class="input" type="text" name="username"></div>

        <div class="loginRow"><label>Password:</label>
        <input class="input" type="password" name="password"></div>

        <div class="loginRow"><label>Confirm Password:</label>
        <input class="input" type="password" name="confirmPassword"></div></div>

        <div class="blockForm">

        <div class="loginRow"><label for="start">Birthday: </label><input class="input" type="date" id="start" name="birthday"
       min="1910-01-01" max="2010-01-01"></div>

       <div class="loginRow">
         <label for="sib">Your relationship to person with special needs: </label>
          <select class="input" id="sib" name="relationship">
            <option value="">Select...</option>
            <option value="sibling">Sibling</option>
            <option value="parent">Parent of</option>
            <option value="offspring">Daughter/son/child of</option>
            <option value="distant">More distant family relationship</option>
          </select>
       </div>

       <div class="loginRow check">
       <input type="checkbox" name="check" value="subscribe"><label> Subscribe to our newsletter</label></div>

        </div>
      </div>

        <?php echo "<p class='statusForm'>". $formStatus . "</p>"; ?>

        <div class="loginRowButton"><input class="submitButton" type="submit" name="submitCon"></div></form>
      </div>
    </div>



<div class="spaceHolder">
<div class="whiteBar"><div class="bottomTxt">
  <p class="contactInfoBottom">Siblings Helping Other Siblings (SHOS)</p><p class="contactInfoBottom"> Email: laurencowell042502@gmail.com</p><p class="contactInfoBottom">Property of Lauren and Aiden Cowell</p>
<p class="copyright">Made by Web Styles FL.</p></div></div></div></div></div></div>
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
