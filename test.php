<?php
    $server = "localhost";
    $username = "u640129124_admin";
    $password= "accessDataAdmin";
    $db = "u640129124_LaurenDatabase";

    $connection = new mysqli($server,$username,$password, $db);
    if($connection->connect_error){
      die("Connection Failed");
    }

    $status="";
    $user="";
    $pass="";

    $command = "CREATE TABLE memberContact (id INTEGER, first TEXT, last TEXT, email TEXT, relationship TEXT, birthday TEXT)";
    if($connection->query($command)===TRUE){echo "table created";}
    else{echo $connection->error . "<br>";}

?>
