<?php
    session_start();?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Forum</title>
  <link href="https://fonts.googleapis.com/css?family=Oswald|Pontano+Sans&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Quicksand&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Raleway&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Parisienne&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="general.css" type="text/css">
  <link rel="stylesheet" href="form.css" type="text/css">
  <link rel="stylesheet" href="forum.css" type="text/css">
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
      $loginStatus="";

      $_SESSION['connection']= new mysqli($server,$username,$password, $db);

      if($_SESSION['connection']->connect_error){
        die("Connection Failed");
      }

      if($_SESSION["logged"]==FALSE){
        $loginStatus="Please login to view the forum.";
      }
  ?>
<div class="gen">
    <div class="topSect topSectFix"><span class="top topSmall"><div id="left" class="leftFix"><p id="title" class="titleSmall" onclick="window.location.href='index.php'">SHOS</p></div>
    <div id="right"><p class="menu menuSlideFix">&#8595;</p></div></span>
  <div class="nav navFix"><div class="buttonCent">
    <span class="button" onclick="window.location.href='index.php'"><p class="buttonTxt">Home</p></span>
    <span class="button" onclick="window.location.href='about.php'"><p class="buttonTxt">About</p></span>
    <span class="button" onclick="window.location.href='resources.html'"><p class="buttonTxt">Resources</p></span>
  <span class="button" onclick="window.location.href='forumSelect.php'"><p class="buttonTxt">Forum</p></span>
    <span class="button" onclick="window.location.href='members.php'"><p class="buttonTxt">Members</p></span>
  </div></div></div>

  <div class="backPhotoCont"><img class="backPhoto" src="images/reachOut2.jpg"/><div class="titleCont"><p class="title">Siblings Helping Other Siblings Forum</p></div></div>

    <?php
    if($_SESSION["logged"]==FALSE){
    echo "<div class='topicCont'><p id='statusForm'>". $loginStatus . "</p></div>"; }

    ?>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <?php
      $postInfo= mysqli_query($_SESSION['connection'], "SELECT id, name, showName FROM conversations");
      while($row=mysqli_fetch_row($postInfo)){
        $name=$row[1];
        $convoInfo=mysqli_query($_SESSION['connection'],"SELECT message FROM {$name}");
        $number=mysqli_num_rows($convoInfo);
        $id=$row[0];
        $showName=$row[2];
        echo '<div class="topicCont"><input class="topicTitle" type="submit" name="submit" value="' . $showName .'" /><p class="numPosts">Number of Posts: ' . $number .'</p><input class="deleteButton adminOnly" type="submit" name="Delete' . $id .'" value="Delete" /></div>';
      }
    ?>
    <div class="topicCont modOnly">
      <p class="topicTitle">Add Discussion</p>
      <div class="loginRow">
      <textarea name="titleOfDisc" style="width:50%; min-width: 200px; height:auto;"></textarea></div>
      <div class="loginRowButton"><input class="submitButton" type="submit" name="Add" value="Add" /></div></div>
  </form>

<?php
    if($_SESSION["status"]>1){
        echo "<script>
          var modEl=document.getElementsByClassName('modOnly');
          for(var i=0;i<modEl.length;i++){
            modEl[i].classList.add('modOnlymod');
          }
        </script>";
      }
      if($_SESSION["status"]>2){
        echo "<script>
          var adminEl=document.getElementsByClassName('adminOnly');
          for(var i=0;i<adminEl.length;i++){
            adminEl[i].classList.add('adminOnlyadmin');
          }
        </script>";
      }
      $_SESSION["forum"]="";
      if($_SERVER["REQUEST_METHOD"]=="POST"){
        if($_SESSION["logged"]==TRUE){
          $postInfo=mysqli_query($_SESSION['connection'],"SELECT id, name, showName FROM conversations");
          if(isset($_POST["Add"])){
            if($_SESSION["status"]>1){
              $title=$_POST["titleOfDisc"];
              $command = "INSERT INTO conversations(showName, name) VALUES (?,'whatever')";
              $discInsert = $_SESSION['connection']->prepare($command);
              $discInsert->bind_param("s",$title);
              $discInsert->execute();
              $discInsert->close();
              $convoId=$_SESSION['connection']->insert_id;
              $name="discussion{$convoId}";
              $sql="UPDATE conversations SET name='$name' WHERE id='$convoId'";
              $_SESSION['connection']->query($sql);
              $command = "CREATE TABLE {$name} (id INTEGER AUTO_INCREMENT PRIMARY KEY, personId INTEGER, first TEXT, last TEXT, message TEXT, video INTEGER, image INTEGER)";
              $_SESSION['connection']->query($command);
              $forumCommentsName=$name . "comments";
              $command = "CREATE TABLE {$forumCommentsName} (id INTEGER AUTO_INCREMENT PRIMARY KEY, userId INTEGER, postId INTEGER, message TEXT)";
              $_SESSION['connection']->query($command);
              header("location: forumSelect.php");
              exit;
            }
          }
          else{
            while($row=mysqli_fetch_row($postInfo)){
            if($_POST['submit']==$row[2]){
                $_SESSION["forum"]=$row[1];
                echo "<script> window.location.href='forum.php' </script>";
                break;
              }
              $delete="Delete" . $row[0];
              if(isset($_POST["$delete"])){
                if($_SESSION["status"]>2){
                  $convoId=$row[0];
                  $name="discussion{$convoId}";
                  $commentsName="discussion{$convoId}comments";
                  $sql="SELECT id FROM {$name}";
                  $posts=$_SESSION['connection']->query($sql);
                  while($row=$posts->fetch_row()){
                    $postNum=$row[0];
                    $likeTable="{$name}_{$postNum}likes";
                    $sqlInner="DROP TABLE {$likeTable}";
                    $_SESSION['connection']->query($sqlInner);
                  }
                  $sql="DELETE FROM conversations WHERE id='$convoId'";
                  $_SESSION['connection']->query($sql);
                  $sql="DROP TABLE {$name}";
                  $_SESSION['connection']->query($sql);
                  $sql="DROP TABLE {$commentsName}";
                  $_SESSION['connection']->query($sql);
                  header("location: forumSelect.php");
                  exit;
                }
                break;
              }
            }
          }
        }
        else{
          echo "<script>
            var arrow=document.getElementById('statusForm');
            arrow.classList.add('flash');
          </script>";
        }
      }
      ?>

    <!--"' . $id .'"-->


<div class="spaceHolder">
<div class="whiteBar"><div class="bottomTxt">
  <p class="contactInfoBottom">Siblings Helping Other Siblings (SHOS)</p><p class="contactInfoBottom"> Email: laurencowell042502@gmail.com</p><p class="contactInfoBottom">Property of Lauren and Aiden Cowell</p>
<p class="copyright">Made by Web Styles FL.</p></div></div></div></div>
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
