<?php
//ini_set("display_errors", 1);
//ini_set("track_errors", 1);
//ini_set("html_errors", 1);
//error_reporting(E_ALL);

$servername = "localhost";
$username = "root";
$password = "xxxxxxxxxxx";
$dbname = "MedicalID";
date_default_timezone_set('US/Eastern');


file_put_contents("data.txt", json_encode($_POST));
if(!isset($_POST["email"]))exit(); //Exit script if we don't even have an email. Lines below won't execute.



try {

  	$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password); // Initialize the connection.
  	//$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //$conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
  	//$conn->setAttribute(PDO::ERRMODE_EXCEPTION);
  	// Get current records, just to see if records  exists already or not.
	$stmt = $conn->prepare("SELECT * FROM users WHERE email=:email"); 
	$stmt->execute(['email' => $_POST["email"]]); 
	$row = $stmt->fetch();

	if(empty($row)){
		// Records do not exist, so we will INSERT.

		

		$stmt = $conn->prepare("INSERT INTO users ".createKeyList()." VALUES ".createKeyPreparedList());
  		
  		$info = array();

  		foreach($_POST as $key=>$value){
  			$info[$key] = $value;
  			//echo "We will bind ".$key." to ".$value."<br>\n";
  		}

  		$info["date_last_active"] = date('Y-m-d');

  		$stmt->execute($info); 
  		echo json_encode(array("success" => "true"));


	}else{
		//echo "Our updated statement UPDATE users ".createUpdateStmt()." WHERE email=:email<br>";
		$stmt = $conn->prepare("UPDATE users SET ".createUpdateStmt()." WHERE email=:email");
		

		$info = array();
  		foreach($_POST as $key=>$value){
  			$info[$key] = $value;
  			//echo "We will bind ".$key." to ".$value."<br>\n";
  		}

  		$info["date_last_active"] = date('Y-m-d');

  		if ($stmt->execute($info)) { 
  			echo json_encode(array("success" => "true"));
  			exit();
  		}else{
  			echo json_encode(array("success" => "false", "c" => "1"));
  		}


	}


} catch (PDOException $e) {
    echo $e->getMessage();
}


// INSERT Helpers.

function createKeyList(){
	$str = "(";
	$keys = array_keys($_POST);
	foreach($keys as $key) $str .= $key.", ";
	return rtrim($str, ", ").")";

}
function createKeyPreparedList(){
	$str = "(";
	$keys = array_keys($_POST);
	foreach($keys as $key) $str .= ":".$key.", ";
	return rtrim($str, ", ").")";

}
function createValueList(){
	$str = "(";
	$values = array_values($_POST);
	foreach($values as $val) $str .= $val.", ";
	return rtrim($str, ", ").")";

}



// Update Helpers.



function createUpdateStmt(){
	$str = "";
	$values = array_keys($_POST);
	foreach($values as $val){
		if($val == "email")continue;
		$str .= $val."=:".$val.", ";
	}
	return rtrim($str, ", ");

}

?>


