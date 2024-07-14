<?php
	/* php & mysqldb connection file */
	$user = "root"; 			//mysqlusername
	$pass = ""; 				//mysqlpassword
	$host = "localhost"; 		//server name or ipaddress //127.0.0.1
	$dbname = "dbmedichub"; 	//your db name

	try {
    	$conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		return $conn;
} catch(PDOException $e) {
    echo 'ERROR: '. $e->getMessage();
}
?>