<?php
session_start();
if (!isset($_SESSION['userID']) ||!isset($_SESSION['userType'])) {
    echo "Session variables not set!";
    exit;
}

include("dbconn.php");
$pdo = $conn;

$userType = $_POST['userType'];
$userID = $_POST['userID'];

if ($userType === 'doctor') {
    $doctorName = $_POST['doctorName'];
    $doctorNRIC = $_POST['doctorNRIC'];
    $doctorSpeciality = $_POST['doctorSpeciality'];
    $availability = $_POST['availability'];

    $stmt = $pdo->prepare("UPDATE doctor SET doctorName =?, doctorNRIC =?, doctorSpeciality =?, availability =? WHERE doctorID =?");
    $stmt->bindParam(1, $doctorName, PDO::PARAM_STR);
    $stmt->bindParam(2, $doctorNRIC, PDO::PARAM_STR);
    $stmt->bindParam(3, $doctorSpeciality, PDO::PARAM_STR);
    $stmt->bindParam(4, $availability, PDO::PARAM_STR);
    $stmt->bindParam(5, $userID, PDO::PARAM_STR);
    $stmt->execute();

    // Redirect to the profile page
    header("Location: profile.php?userType=doctor&userID=$userID");
    exit;
} elseif ($userType === 'patient') {
    $patientName = $_POST['patientName'];
    $patientNRIC = $_POST['patientNRIC'];
    $patientAddress = $_POST['patientAddress'];

    $stmt = $pdo->prepare("UPDATE patient SET patientName =?, patientNRIC =?, patientAddress =? WHERE patientID =?");
    $stmt->bindParam(1, $patientName, PDO::PARAM_STR);
    $stmt->bindParam(2, $patientNRIC, PDO::PARAM_STR);
    $stmt->bindParam(3, $patientAddress, PDO::PARAM_STR);
    $stmt->bindParam(4, $userID, PDO::PARAM_STR);
    $stmt->execute();

    // Redirect to the profile page
    header("Location: profile.php?userType=patient&userID=$userID");
    exit;
} else {
    echo "Invalid user type!";
}
?>