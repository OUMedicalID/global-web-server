<?php
//ini_set("display_errors", 1);
//ini_set("track_errors", 1);
//ini_set("html_errors", 1);
//error_reporting(E_ALL);
http_response_code(200);

$servername = "localhost";
$username = "root";
$password = "xxxxxxxxxxx";
$dbname = "MedicalID";


file_put_contents("data.txt", json_encode($_POST));


if(!isset($_POST["email"])){
  echo json_encode(array("success" => "false" , "msg" => "No email POST parameter."));
  exit(); //Exit script if we don't even have an email. Lines below won't execute.
}

	try {

  		$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password); // Initialize the connection.
  		//$conn->setAttribute(PDO::ERRMODE_EXCEPTION);
  		// Get current records, just to see if records  exists already or not.
		$stmt = $conn->prepare("SELECT * FROM users WHERE email=:email"); 
		$stmt->execute(['email' => $_POST["email"]]); 
		$row = $stmt->fetch();

	

		//echo "We are going to execute INSERT INTO users (email) VALUES (".$_POST["email"].");";
		//echo "<br>";

		$stmt = $conn->prepare("INSERT INTO users ".createKeyList()." VALUES ".createKeyPreparedList());
   
  		$info = array();

  		foreach($_POST as $key=>$value){
  			$info[$key] = $value;
  			//echo "We will bind ".$key." to ".$value."<br>\n";
  		}

  		if ($stmt->execute($info)) { 
  			echo json_encode(array("success" => "true"));
  			exit();
  		}else{
  			echo json_encode(array("success" => "false"));
  		}

	} catch (PDOException $e) {
		echo json_encode(array("success" => "false"));
    //echo $e->getMessage();
	}




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


