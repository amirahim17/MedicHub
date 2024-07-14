<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['userID']) ||!isset($_SESSION['userType'])) {
    echo "Session variables not set!";
    exit;
}

include("dbconn.php");
$pdo = $conn;

$appointmentID = $_POST['appointmentID'];
$userType = $_POST['userType']; // Initialize $userType
var_dump($userType); // Debugging line
$userID = $_POST['userID'];

if (isset($userType) && $userType === 'doctor') {
    $diagnosis = $_POST['diagnosis'];
    $prescriptionID = $_POST['prescriptionID'];

    // Check if variables are defined and not null
    if (!isset($diagnosis) || !isset($prescriptionID)) {
        echo "One or more variables are not defined!";
        exit;
    }

    $stmt = $pdo->prepare("UPDATE appointment SET diagnosis =?, prescriptionID =?  WHERE appointmentID =?");
    $stmt->bindParam(1, $diagnosis, PDO::PARAM_STR);
	$stmt->bindParam(2, $prescriptionID, PDO::PARAM_STR);
	$stmt->bindParam(3, $appointmentID, PDO::PARAM_STR);
    $stmt->execute();
	$result = $stmt->execute();
	if ($result) {
		echo "Update successful!";
	} else {
		echo "Update failed: " . $stmt->errorInfo()[2];
	}

    // Redirect to the appointment page
    header("Location: AppointmentPage(doc).php?doctorID=$userID");
    exit;
} else {
    echo "Invalid user type!";
}
?>
