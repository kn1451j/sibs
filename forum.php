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
    <link rel="stylesheet" href="sign.css" type="text/css">
  <link rel="stylesheet" href="forum.css" type="text/css">
  <meta name="description" content="A social network for those who have family with special needs looking for support and resources from others in similar situations. Siblings Helping Other Siblings is a non-profit organization that seeks to connect people and create
    a more aware and knowledgable community. Although the website was specifically made with those who have special needs family members in mind, anyone interested in creating a support network is welcome to join and participate.">
  <meta name="keywords" content="siblings, family, siblings helping other siblings, SHOS, special needs, disabilities, disability, brother, sister, family help, care, family care, community">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" type="image/png" href="images/favicon.ico">
</head>
<style>
  .msgRowIn{
    height:175px;
  }

  .blockForm{
    max-width: 450px;
  }

  .loginRow{
    padding: 5px;
  }
</style>
<body>
<?php
    $server = "localhost";
    $username = "u640129124_admin";
    $password= "accessDataAdmin";
    $db = "u640129124_LaurenDatabase";
    $loginStatus="";
    if($_SESSION["logged"]==FALSE){
    $loginStatus="Please log in to make a post.";}
    else{
      $loginStatus="";
    }

    $_SESSION['connection']= new mysqli($server,$username,$password, $db);

    if($_SESSION['connection']->connect_error){
      die("Connection Failed");
    }

    $forumName=$_SESSION["forum"];

    $number=0;
    if($_SESSION["logged"]==TRUE){
      $loginStatus="";
      $id=$_SESSION['id'];
      $email=$_SESSION['email'];
      $noPost=mysqli_query($_SESSION['connection'],"SELECT id FROM noPostList WHERE id=$id || email='$email'");
      $number=mysqli_num_rows($noPost);
      if($number>0){
        $loginStatus="You are banned from posting.";
      }
    }

    $forumName=$_SESSION["forum"];
    if($_SERVER["REQUEST_METHOD"]=="POST" && $_SESSION["logged"]==TRUE && $number==0){
      if($_POST['submit']=="Post"){
        $id=$_SESSION['id'];
        $msg=clean($_POST["message"]);
        if(strlen($msg)>2000 || strlen($msg)<20){
          $formStatus="Message must be between 20 and 2000 characters.";
        }
        else{
          if($_FILES['fileToUpload']['size'] > 0){
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
            $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
            if ($_FILES['fileToUpload']['size']  > 600000000) {
                $formStatus="File too large";
            }
            else{
              if($imageFileType=="jpg"){
                $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
                if($check !== false) {
                    $newPost = "INSERT INTO {$forumName} (personId, first, last, message,video, image) VALUES(?, ?, ?, ?,0,1)";
                    $postInsert = $_SESSION['connection']->prepare($newPost);
                    $postInsert->bind_param("isss",$id,$_SESSION["first"],$_SESSION["last"],$msg);
                    $postInsert->execute();
                    $postInsert->close();
                    $uploadId=$_SESSION['connection']->insert_id;
                    $target_file=$target_dir . "img{$forumName}_{$uploadId}.jpg";
                    move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file);
                    $postId = $_SESSION['connection']->insert_id;
                    $tableName="{$forumName}_{$postId}likes";
                    $command = "CREATE TABLE {$tableName} (id INTEGER)";
                    $_SESSION['connection']->query($command);
                    header("location: forum.php");
                    exit;
                }
                else{
                  $formStatus="Not a real image";
                }
              }
              else if($imageFileType=="mp4"){
                $newPost = "INSERT INTO {$forumName} (personId, first, last, message,video, image) VALUES(?, ?, ?, ?,1,0)";
                $postInsert = $_SESSION['connection']->prepare($newPost);
                $postInsert->bind_param("isss",$id,$_SESSION["first"],$_SESSION["last"],$msg);
                $postInsert->execute();
                $postInsert->close();
                $uploadId=$_SESSION['connection']->insert_id;
                $target_file=$target_dir . "vid{$forumName}_{$uploadId}.mp4";
                move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file);
                $postId = $_SESSION['connection']->insert_id;
                $tableName="{$forumName}_{$postId}likes";
                $command = "CREATE TABLE {$tableName} (id INTEGER)";
                $_SESSION['connection']->query($command);
                header("location: forum.php");
                exit;
              }
              else{
                $formStatus="Only mp4 or jpg files allowed.";
              }
            }
          }
          else{
            $newPost = "INSERT INTO {$forumName} (personId, first, last, message,video, image) VALUES(?, ?, ?, ?,0,0)";
            $postInsert = $_SESSION['connection']->prepare($newPost);
            $postInsert->bind_param("isss",$id,$_SESSION["first"],$_SESSION["last"],$msg);
            $postInsert->execute();
            $postInsert->close();
            $postId = $_SESSION['connection']->insert_id;
            $tableName="{$forumName}_{$postId}likes";
            $command = "CREATE TABLE {$tableName} (id INTEGER)";
            $_SESSION['connection']->query($command);
            header("location: forum.php");
            exit;
          }
        }
      }
      else{
        $postInfo=$_SESSION['connection']->query("SELECT id, first, last FROM {$forumName}");
        while($row=$postInfo->fetch_row()){
          $convoId=$row[0];
          $report="Report" . $row[1];
          $deleteMsg="DeleteMsg" . $convoId;
          $comment="CommentOn" . $convoId;
          $like="like" . $convoId;
          $unlike="unlike" . $convoId;
          $userId=$_SESSION['id'];

          if(isset($_POST["$report"])){
            $reportedName= $row[1] . " " . $row[2];
            mail("laurencowell042502@gmail.com","Reported","Message #{$convoId} in {$forumName} from {$reportedName} was reported.","From: SHOS<laurencowell042502@gmail.com>");
            header("location: forum.php");
            exit;
          }
          else if(isset($_POST["$deleteMsg"])){
            if($_SESSION["status"]>2){
              $sql="DELETE FROM {$forumName} WHERE id={$convoId}";
              $_SESSION['connection']->query($sql);
              $commentTable=$forumName . "comments";
              $sql="DELETE FROM {$commentTable} WHERE postId={$convoId}";
              $_SESSION['connection']->query($sql);
              $likesTable="{$forumName}_{$convoId}likes";
              $sql="DROP TABLE {$likesTable}";
              $_SESSION['connection']->query($sql);
              header("location: forum.php");
              exit;
            }
          }
          else if(isset($_POST["$comment"])){
            $commentTable=$forumName . "comments";
            $messageName="message" . $convoId;
            $message=$_POST["$messageName"];
            $newPost = "INSERT INTO {$commentTable} (userId, postId, message) VALUES(?, ?, ?)";
            $postInsert = $_SESSION['connection']->prepare($newPost);
            $postInsert->bind_param("iis",$userId,$convoId,$message);
            $postInsert->execute();
            $postInsert->close();
            header("location: forum.php");
            exit;
          }
          else if(isset($_POST["{$like}_x"], $_POST["{$like}_y"])){
            $tableName="{$forumName}_{$convoId}likes";
            $sql="INSERT INTO {$tableName} (id) VALUES (?)";
            $postInsert = $_SESSION['connection']->prepare($sql);
            $postInsert->bind_param("i",$userId);
            $postInsert->execute();
            $postInsert->close();
            header("location: forum.php");
            exit;
          }
          else if(isset($_POST["{$unlike}_x"], $_POST["{$unlike}_y"])){
            $tableName="{$forumName}_{$convoId}likes";
            $sql="DELETE FROM {$tableName} WHERE id={$userId}";
            $_SESSION['connection']->query($sql);
            header("location: forum.php");
            exit;
          }
        }
        $commentTable=$forumName . "comments";
        $sql="SELECT {$commentTable}.id, memberContact.first, memberContact.last FROM memberContact JOIN {$commentTable} ON memberContact.id={$commentTable}.userId";
        $commentsInfo=mysqli_query($_SESSION['connection'],$sql);
        while($innerRow=mysqli_fetch_row($commentsInfo)){
          $commentId=$innerRow[0];
          $reportComment="ReportComment" . $innerRow[1];
          $deleteComment="DeleteComment" . $commentId;
          if(isset($_POST["$reportComment"])){
            $reportedName= $innerRow[1] . " " . $innerRow[2];
            mail("laurencowell042502@gmail.com","Reported","Comment #{$commentId} in {$forumName} from {$reportedName} was reported.","From: SHOS<laurencowell042502@gmail.com>");
          }
          else if(isset($_POST["$deleteComment"])){
            if($_SESSION["status"]>2){
              $sql="DELETE FROM {$commentTable} WHERE id={$commentId}";
              $_SESSION['connection']->query($sql);
              header("location: forum.php");
              exit;
            }
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
    <span class="button" onclick="window.location.href='forumSelect.php'"><p class="buttonTxt">Forum</p></span>
    <span class="button" onclick="window.location.href='members.php'"><p class="buttonTxt">Members</p></span>
  </div></div></div>
  <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data">
  <?php
  $forumName=$_SESSION["forum"];
  $postInfo=mysqli_query($_SESSION['connection'],"SELECT id, first, message, video, image FROM {$forumName}");
  $count=0;
  while($row=mysqli_fetch_row($postInfo)){
    $id=$row[0];
    echo '<div class="topicCont"><p class="topicTitle">' . $row[1] .'</p>';
    if($row[3]==1){
      $vidName="uploads/vid{$forumName}_{$id}.mp4";
      echo '<div class="forumImageCont"><div class="forumImage"><video class="actForumImage" controls>
        <source src="' . $vidName . '" type="video/mp4">
      </video></div></div>';
    }
    if($row[4]==1){
      $imgName="uploads/img{$forumName}_{$id}.jpg";
      echo '<div class="forumImageCont"><div class="forumImage"><img class="actForumImage" src="' . $imgName . '" alt="uploaded image"/></div></div>';
    }
    echo '<p class="numPosts">' . $row[2] .'</p><div class="reportButton"><input class="deleteButton actReport" type="submit" name="Report' . $row[1] . '" value="Report" /><input class="actDelete adminOnly" type="submit" name="DeleteMsg' . $id . '" value="Delete" /></div><p class="deleteButton commentButton" onclick="activateComment(' . $count . ')">Comment</p>';
    $likesTable="{$forumName}_{$id}likes";
    $myId=$_SESSION['id'];
    $allLikes=mysqli_query($_SESSION['connection'],"SELECT id FROM {$likesTable}");
    $numLikes=mysqli_num_rows($allLikes);
    $hasLiked=mysqli_query($_SESSION['connection'],"SELECT id FROM {$likesTable} WHERE id={$myId}");
    if(mysqli_num_rows($hasLiked)==0){
      echo '<div class="deleteButton likeCont"><p class="likeCount">' . $numLikes . '</p><input name="like' . $id . '" class="inputimg" type="image" src="images/like.png" />';
    }
    else{
      echo '<div class="deleteButton likeCont"><p class="likeCount">' . $numLikes . '</p><input name="unlike' . $id . '" class="inputimg" type="image" src="images/liked.png" />';
    }
    echo '</div></div>';
    $count=$count+1;
    $commentTable=$forumName . "comments";
    $sql="SELECT {$commentTable}.id, memberContact.first, {$commentTable}.message, {$commentTable}.postId FROM memberContact JOIN {$commentTable} ON memberContact.id={$commentTable}.userId";
    $commentsInfo=mysqli_query($_SESSION['connection'],$sql);
    while($innerRow=mysqli_fetch_row($commentsInfo)){
      $commentId=$innerRow[0];
      $commenter=$innerRow[1];
      if($innerRow[3]==$id){
        echo '<div class="topicCont commentCont"><div class="commentBox"><p class="topicTitle commentTitle">' . $innerRow[1] .'</p><p class="numPosts commentText">' . $innerRow[2] .'</p><input class="deleteButton reportButton" type="submit" name="ReportComment' . $commenter . '" value="Report" /><input class="deleteButton adminOnly" type="submit" name="DeleteComment' . $commentId . '" value="Delete" /></div></div>';
      }
    }
    echo '<div class="topicCont commentCont commentActivate"><div class="commentBox"><div class="loginRow"><label>Comment:</label><textarea name="message' . $id .'" style="width:100%; height:100%;"></textarea></div><div class="loginRowButton"><input class="submitButton commentSubmitButton" type="submit" name="CommentOn' . $id .'" value="Comment" /></div></div></div>';
  }

  ?>

  <script>
  function activateComment(commentNum){
    var commentBox=document.getElementsByClassName("commentActivate")[commentNum];
    if(commentBox.classList.contains("commentVisible")){
      commentBox.classList.remove("commentVisible");
    }
    else{
    commentBox.classList.add("commentVisible");}
  }
  </script>

  <div class="topicCont">
      <?php echo "<p class='statusForm'>". $loginStatus . "</p>"; ?>
    <div class="notLogged">
       <div class="blockForm">

          <div class="loginRow"><label>Message:</label>
          <textarea name="message" class="msgRowIn" style="width:100%;"></textarea></div>

          <div class="loginRow"><label>File Upload:</label>
            <input type="file" name="fileToUpload" id="fileToUpload">
          </div>

          <?php echo "<p class='statusForm'>". $formStatus . "</p>"; ?>

          <div class="loginRowButton"><input class="submitButton" type="submit" name="submit" value="Post" /></div>
          </div>
      </div>
    </div>
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
?>


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
