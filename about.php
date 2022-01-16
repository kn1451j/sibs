<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>About Us</title>
  <link href="https://fonts.googleapis.com/css?family=Oswald|Pontano+Sans&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Quicksand&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Raleway&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="general.css" type="text/css">
  <link rel="stylesheet" href="about.css" type="text/css">
  <link rel="stylesheet" href="contact.css" type="text/css">
  <meta name="description" content="A social network for those who have family with special needs looking for support and resources from others in similar situations. Siblings Helping Other Siblings is a non-profit organization that seeks to connect people and create
    a more aware and knowledgable community. Although the website was specifically made with those who have special needs family members in mind, anyone interested in creating a support network is welcome to join and participate.">
  <meta name="keywords" content="siblings, family, siblings helping other siblings, SHOS, special needs, disabilities, disability, brother, sister, family help, care, family care, community">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" type="image/png" href="images/favicon.ico">
</head>
<body>
  <?php
    $genEr="";
    if($_POST["submitCon"]){
      $emailCon=$_POST["emailCon"];
      $msgCon=$_POST["msgCon"];
      $fromCon=$_POST["fromCon"];
      if(empty($emailCon) || empty($msgCon) || empty($fromCon)){
        $genEr="<p class='formTxt'>Please fill out all fields</p>";}
      else{
        $emailCon=filter_var($emailCon,FILTER_SANITIZE_EMAIL);
        if(filter_var($emailCon,FILTER_VALIDATE_EMAIL)==false){
          $genEr="<p class='formTxt'>Please enter a valid email address</p>";
        }
        else{
          $fromCon=check($fromCon);
          $msgCon="Someone filled out a contact us form on the SHOS website saying: " . check($msgCon);
          mail("laurencowell042502@gmail.com","Siblings Helping Other Siblings Contact",$msgCon,"From: $fromCon<$emailCon>");
          $genEr="<p class='formTxt'>Thanks! Your message was sent.</p>";
        }
      }
    }

    function check($var){
      return trim(stripslashes(htmlspecialchars($var)));
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
    <div class="slideCont"><p class="slideTitle">About Us</p></div>

    <div class="introduction">
      <div class="outerBox boxImg"><div class="box">
        <div class="imgCont"><p class="name">Lauren and Aidan Cowell</p><p class="subName">Founders of SHOS</p></div>
      <img src="images/sibs.jpg" class="bigImgCont" alt="graduation photo" /></div></div>

      <div class="outerBox lauren"><div class="box">
      <!--<div class="imgCont"><p class="name">Lauren Cowell</p><p class="subName">Founder of SHOS</p><img src="images/lauren.jpg" class="photo" alt="graduation photo" /></div>-->
      <div class="txtCont"><p class="aboutTxt">"Hi, my name is Lauren Cowell. I am the founder of Siblings Helping Other Siblings and the luckiest twin sister in the world! My brother Aidan is on the Autism Spectrum and never seizes to amaze me on a daily basis. He has taught me what hard work looks like and to push boundaries even when seemingly impossible. I can easily say that I would not be the person I am today without having Aidan as my brother. Although I value every opportunity and experience that I have had from having a sibling with special needs, and love my brother more than anything in the world, I would be lying if I said that I haven't struggled throughout my life with supporting him, and myself. It wasn't until recently that I became okay with talking to a professional about challenges that I have encountered and problems that I need help resolving. Although the support community is continuously growing with <!--<a href="resources.html#goToSib">programs</a>-->programs, there is still a lack of support for teenagers and young adults. I got the idea to create a safe, online support community where people can anonymously ask questions, share experiences, and get help through other members and myself. I hope that this website allows you to gain support and if there are any problems or inquiries please reach out to me."</p>
    </div></div></div>
    <div class="outerBox"><div class="box">
      <!--<div class="imgCont"><p class="name">Aiden Cowell</p><img src="images/aiden.jpg" class="photo" alt="graduation photo" /></div>-->
      <div class="txtCont"><p class="aboutTxt">"Being autistic is something that has given me a unique view on life much more for the better than the worse. It has given me more assets that have benefited me in the long run. I also have many ambitions in life including reading, fiction writing, and gymnastics, along with many others. The fact that this website is about supporting people like my sister is very enlightening. I support her fully with her goals on this website. I am very proud to have her as a sister!" - Aidan Cowell</p>
    </div></div></div>
    </div>
    <div class="contBox"><div class="backPhoto"><img class="backimage" src="images/people.jpg" /></div><div class="blueBox">
    <p class="title">Contact Us</p>
    <div class="table">
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"><div class="q">
    <div class="tr"><label>Name:</label>
    <input class="in" name="fromCon"></div>
    <div class="tr"><label>Email:</label>
    <input class="in" name="emailCon"></div>
    <div class="tr"><label>Message:</label>
    <textarea class="in" name="msgCon"></textarea></div>
    <?php echo $genEr; ?>
    <div class="tr" style="display: flex;
    justify-content: center;"><input class="submit" type="submit" name="submitCon"></div></div>
    </form></div>
  </div></div>
    <div class="spaceHolder">
    <div class="whiteBar"><div class="bottomTxt">
      <p class="contactInfoBottom">Siblings Helping Other Siblings (SHOS)</p><p class="contactInfoBottom"> Email: laurencowell042502@gmail.com</p><p class="contactInfoBottom">Property of Lauren and Aidan Cowell</p>
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