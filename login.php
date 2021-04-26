<?php
//ini_set("display_errors", 1);
//ini_set("track_errors", 1);
//ini_set("html_errors", 1);
//error_reporting(E_ALL);
http_response_code(200);
date_default_timezone_set('US/Eastern');

$servername = "localhost";
$username = "root";
$password = "xxxxxxxxxxx";
$dbname = "MedicalID";


if(!isset($_POST["email"])){
  echo json_encode(array("error" => "true" , "msg" => "No email POST parameter."));
  exit(); //Exit script if we don't even have an email. Lines below won't execute.
}


	try {

  		$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password); // Initialize the connection.
	  	//$conn->setAttribute(PDO::ERRMODE_EXCEPTION);
	  	// Get current records, just to see if records  exists already or not.
		$stmt = $conn->prepare("SELECT * FROM users WHERE email=:email"); 
		$stmt->execute(['email' => $_POST["email"]]); 
		$row = $stmt->fetch();

		if($row){
			foreach ($row as $key => $value){
				if($value = "" || $value == null || strlen($value) == 0)$row[$key] = "";
			}
			echo json_encode($row);
			

			// Update Last Active
			$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
			$stmt = $conn->prepare("UPDATE users SET date_last_active=:newDate WHERE email=:email"); 
			$newDate = date('Y-m-d');
		  	$stmt->bindParam(':newDate', $newDate);
		  	$stmt->bindParam(':email', $_POST["email"]);
		  	$stmt->execute(); 


			exit();
		}else{
			echo json_encode(array("error" => "true"));
			exit();
		}



	} catch (PDOException $e) {
		echo json_encode(array("error" => "true"));
    	echo $e->getMessage();
	}







?>