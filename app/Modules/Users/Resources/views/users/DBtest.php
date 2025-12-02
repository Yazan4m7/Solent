<?php

//header('Content-type: text/plain; charset=utf-16');

    $servername = "159.65.123.30";
    $username = "mobileUser";
    $password = 'Random@#pass0rd';
    $dbname = "manshore";
	$sSQL= 'SET CHARACTER SET utf8'; 

    // we will get actions from the app to do operations in the database...
    //$action = $_POST["action"];
     
    // Create Connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check Connection
mysqli_query($conn,$sSQL);
    if($conn->connect_error){
        die("Connection Failed: " . $conn->connect_error);
        return;
    }
 

?>