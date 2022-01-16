<?php
    session_start();?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Member Page</title>
  <link href="https://fonts.googleapis.com/css?family=Oswald|Pontano+Sans&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Quicksand&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Raleway&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Parisienne&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="general.css" type="text/css">
  <link rel="stylesheet" href="form.css" type="text/css">
    <link rel="stylesheet" href="sign.css" type="text/css">
    <link rel="stylesheet" href="loggedin.css" type="text/css">
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
    $counter=0;

    $_SESSION['connection'] = new mysqli($server,$username,$password, $db);

    if($_SESSION['connection']->connect_error){
      die("Connection Failed");
    }

    $id=$_SESSION['id'];
    $email=$_SESSION['email'];

    if(isset($_POST["submit"])){
          $_SESSION["admin"]=FALSE;
          $_SESSION["logged"]= FALSE;
          session_unset();
          session_destroy();
          echo "<script>
            window.location.href='members.php'
          </script>";
    }

    if(isset($_POST["password"])){
      echo "<script>
        window.location.href='changePass.php'
      </script>";
    }

      if(isset($_POST["unsub_x"], $_POST["unsub_y"])){
        $sql="DELETE FROM subscribers WHERE id=$id";
        $_SESSION['connection']->query($sql);
        echo "works";
        header("location: loggedIn.php");
        exit;
      }

      if(isset($_POST["subscribe_x"], $_POST["subscribe_y"])){
        $newSub="INSERT INTO subscribers (id, email) VALUES (?,?)";
        $newSub = $_SESSION['connection']->prepare($newSub);
        $newSub->bind_param("is",$id,$email);
        $newSub->execute();
        header("location: loggedIn.php");
        exit;
      }

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
  <div class="loginTable" id="loginTable"><p class="listTitle">Account</p>
  <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
      <div class="insideForm"><div class="blockForm">
        <?php echo "<p class='listItem'>Username: " .$_SESSION['username'] . "  </p>" ?>
        <?php echo "<p class='listItem'>Name: " . $_SESSION['first'] . " " . $_SESSION['last'] . "  </p>" ?>
        <?php echo "<p class='listItem'> Email: " . $_SESSION['email'] . "</p>" ?>
        <?php
        $email=$_SESSION['email'];
        $sameEmail=mysqli_query($_SESSION['connection'],"SELECT id FROM subscribers WHERE id='$id'");
        if(mysqli_num_rows($sameEmail)==0){
          echo '<div class="loginRowButton newsletterCont"><input name="subscribe" class="inputimg" type="image" src="images/mail.png" /><label> Subscribe to our newsletter</label></div>';
        }
        else{
          echo '<div class="loginRowButton newsletterCont"><input name="unsub" class="inputimg" type="image" src="images/mail.png" /><label> Unsubscribe from our newsletter</label></div>';
        }
        ?>
        <div class="loginRowButton changePass"><input class="submitButton buttons passwordButton" type="submit" name="password" value="Change Password"></div>
        <div class="loginRowButton"><input class="submitButton buttons" type="submit" name="submit" value="Log Out"></div>
        <div class="loginRowButton"><p onclick="window.location.href='memberInfo.php'" class="submitButton buttons redButton adminOnly">View Accounts</p></div>
      </div></div></form>
    </div>
  </div>


<?php
if($_SESSION["status"]>2){
  echo "<script>
    var adminEl=document.getElementsByClassName('adminOnly');
    for(var i=0;i<adminEl.length;i++){
      adminEl[i].classList.add('adminOnlyadmin');
    }
  </script>";
}
 ?>

 <div class="spaceHolder">
 <div class="whiteBar"><div class="bottomTxt">
   <p class="contactInfoBottom">Siblings Helping Other Siblings (SHOS)</p><p class="contactInfoBottom"> Email: laurencowell042502@gmail.com</p><p class="contactInfoBottom">Property of Lauren and Aiden Cowell</p>
 <p class="copyright">Made by Web Styles FL.</p></div></div></div></div></div>  </div>
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
