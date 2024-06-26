<?php
	$servername='localhost';
	$username='root';
	$password='';
	$dbname = "iaslogin";

    //Making connection with the DB
	$conn=mysqli_connect($servername,$username,$password, $dbname);

    //Checking the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
