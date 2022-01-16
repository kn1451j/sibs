<?php
    session_start();?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Accounts</title>
  <link href="https://fonts.googleapis.com/css?family=Oswald|Pontano+Sans&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Quicksand&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Raleway&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Parisienne&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="general.css" type="text/css">
  <link rel="stylesheet" href="form.css" type="text/css">
  <link rel="stylesheet" href="sign.css" type="text/css">
  <link rel="stylesheet" href="memberInfo.css" type="text/css">
  <meta name="description" content="A social network for those who have family with special needs looking for support and resources from others in similar situations. Siblings Helping Other Siblings is a non-profit organization that seeks to connect people and create
    a more aware and knowledgable community. Although the website was specifically made with those who have special needs family members in mind, anyone interested in creating a support network is welcome to join and participate.">
  <meta name="keywords" content="siblings, family, siblings helping other siblings, SHOS, special needs, disabilities, disability, brother, sister, family help, care, family care, community">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" type="image/png" href="images/favicon.ico">
</head>
<style>
  .blockForm{
    max-width: 600px;
  }

  .msgRow {
      height: 200px;
      padding: 0px 0px 20px 0px;
  }

  .msgRowIn{
    height:175px;
  }
</style>
<body>
<?php
    $server = "localhost";
    $username = "u640129124_admin";
    $password= "accessDataAdmin";
    $db = "u640129124_LaurenDatabase";

    $_SESSION['connection'] = new mysqli($server,$username,$password, $db);

    if($_SESSION['connection']->connect_error){
      die("Connection Failed");
    }

    if($_SESSION["logged"]!=TRUE || $_SESSION["status"]<3){
      echo "<script> window.location.href='index.php'; </script>";
    }

    if($_SERVER["REQUEST_METHOD"]=="POST"){
      if(isset($_POST["Send"])){
        $msg=clean($_POST["message"]);
        $subject=clean($_POST["subject"]);
        if(empty($subject) || empty($msg)){
          $formStatus="Please fill out all fields";}
        else{
          if($_POST['check']=="onlySubscribed"){
            $sendTo=mysqli_query($_SESSION['connection'],"SELECT email FROM subscribers");
          }
          else{
            $sendTo=mysqli_query($_SESSION['connection'],"SELECT email FROM memberContact");
          }
          while($row=mysqli_fetch_row($sendTo)){
            mail($row[0],$subject,$msg,"From: SHOS<laurencowell042502@gmail.com>");
            $formStatus = "Sent successfully";
          }
        }
      }
      else{
        $memInfo=mysqli_query($_SESSION['connection'],"SELECT memberContact.id, memberContact.email, memberLogin.status FROM memberContact JOIN memberLogin ON memberContact.id=memberLogin.id");
        while($row=mysqli_fetch_row($memInfo)){
          $id=$row[0];
          $email=$row[1];
          $status=$row[2];

          $block="Block" . $id;
          $member="Member" . $id;
          $ban="Ban" . $id;
          $allow="Allow" . $id;
          $unban="Unban" . $id;
          $change="Change" . $id;

          if(isset($_POST["$block"])){
            echo "works3";
            $sql="INSERT INTO noPostList (id, email) VALUES (?,?)";
            $insert = $_SESSION['connection']->prepare($sql);
            $insert->bind_param("is",$id,$email);
            $insert->execute();
            $insert->close();
            header("location: memberInfo.php");
            exit;
          }

          else if(isset($_POST["$ban"])){
            $sql="INSERT INTO noSignUp (email) VALUES (?)";
            $insert = $_SESSION['connection']->prepare($sql);
            $insert->bind_param("s",$email);
            $insert->execute();
            $insert->close();
            header("location: memberInfo.php");
            exit;
          }


          else if(isset($_POST["$allow"])){
            $sql="DELETE FROM noPostList WHERE id={$id}";
            $_SESSION['connection']->query($sql);
            header("location: memberInfo.php");
            exit;
          }


          else if(isset($_POST["$unban"])){
            $sql="DELETE FROM noSignUp WHERE email='$email'";
            $_SESSION['connection']->query($sql);
            header("location: memberInfo.php");
            exit;
          }

          else if(isset($_POST["$member"])){
            $sql="DELETE FROM memberContact WHERE id={$id}";
            $_SESSION['connection']->query($sql);
            $sql="DELETE FROM memberLogin WHERE id={$id}";
            $_SESSION['connection']->query($sql);
            $sql="DELETE FROM subscribers WHERE id={$id}";
            $_SESSION['connection']->query($sql);
            header("location: memberInfo.php");
            exit;
          }


          else if(isset($_POST["$change"])){
            $newStatus=0;
            if($status==3){
              $newStatus=1;
            }
            else{
              $newStatus=$status+1;
            }
            $sql="UPDATE memberLogin SET status={$newStatus} WHERE id={$id}";
            $_SESSION['connection']->query($sql);
            header("location: memberInfo.php");
            exit;
          }
        }
      }
    }

    function clean($data){
      $data=trim($data);
      $data=stripslashes($data);
      $data=htmlspecialchars($data);
      return $data;
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
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <div id="intro"><div id="introCont">
      <div class="loginTable" id="loginTable"><p class="listTitle">Send Email to Members</p>
          <div class="insideForm flexForm"><div class="blockForm">

            <div class="loginRow"><label>Subject:</label>
            <input class="input" type="text" name="subject"></div>

            <div class="loginRow msgRow"><label>Message:</label>
            <textarea class="input msgRowIn" name="message"></textarea></div>

            <div class="loginRow">
            <input type="checkbox" name="check" value="onlySubscribed"><label> Only send to members recieving newsletters</label></div>

            <?php echo "<p class='statusForm'>". $formStatus . "</p>"; ?>

            <div class="loginRowButton"><input class="submitButton" type="submit" name="Send" value="Send" /></div>
          </div></div>
        </div></div></div>
      <div style="overflow:auto;">
    <table style="width:100%;">
      <tr><th>Member ID</th><th>First Name</th><th>Last Name</th><th>Email</th><th>Username</th><th>Relationship</th><th>Birthday</th><th>Status</th><th>Posting</th><th>Sign Up</th><th>Change Status</th><th>Remove member</th></tr>
    <?php
    $memInfo=$_SESSION['connection']->query("SELECT memberContact.id, memberContact.first, memberContact.last, memberContact.email, memberLogin.user, memberContact.relationship,memberContact.birthday,memberLogin.status FROM memberContact JOIN memberLogin ON memberContact.id=memberLogin.id");
    while($row=$memInfo->fetch_row()){
      $id=$row[0];
      $email=$row[3];
      echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] . "</td><td>" . $row[3] . "</td><td>" . $row[4] . "</td><td>" . $row[5] .
       "</td><td>" . $row[6] . "</td><td>" . $row[7] . "</td>";
      $noPoster=mysqli_query($_SESSION['connection'],"SELECT id FROM noPostList WHERE id={$id}");
      if(mysqli_num_rows($noPoster)==0){
          echo '<td><input class="deleteButton reportButton" type="submit" name="Block' . $id . '" value="Block" /></td>';
      }
      else{
        echo '<td><input class="deleteButton reportButton" type="submit" name="Allow' . $id . '" value="Allow" /></td>';
      }
      $noSignUp=mysqli_query($_SESSION['connection'],"SELECT email FROM noSignUp WHERE email='$email'");
      if(mysqli_num_rows($noSignUp)==0){
          echo '<td><input class="deleteButton reportButton" type="submit" name="Ban' . $id . '" value="Ban" /></td>';
      }
      else{
        echo '<td><input class="deleteButton reportButton" type="submit" name="Unban' . $id . '" value="Unban" /></td>';
      }
      echo '<td><input class="deleteButton" type="submit" name="Change' . $id . '" value="Change" /></td>';
      echo '<td><input class="deleteButton" type="submit" name="Member' . $id . '" value="Remove" /></td>';
      echo "</tr>";
    }

    ?>
  </table></div>
  </form>
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
