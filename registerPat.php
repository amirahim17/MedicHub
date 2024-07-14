<?php
include 'dbconn.php'; // Ensure this file exists and the path is correct

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $patientNRIC = $_POST['patientNRIC'];
    $patientName = $_POST['fullName'];
    $patientPhoneNo = $_POST['phoneNumber'];
    $patientAddress = $_POST['address'];
    $userPassword = $_POST['password'];

    // Hash the password before storing it
    $hashedPassword = password_hash($userPassword, PASSWORD_DEFAULT);

    try {
        // Check if user already exists by NRIC
        $sql = "SELECT * FROM patient WHERE patientNRIC = ?";
        $fetchPat = $conn->prepare($sql);
        $fetchPat->execute([$patientNRIC]);
        if ($fetchPat->rowCount() > 0) {
            echo json_encode(["status" => "exists"]);
            exit;
        }

        // Begin a transaction
        $conn->beginTransaction();

        // Get the maximum patientID and increment it
        $sql = "SELECT MAX(CAST(SUBSTRING(patientID, 2) AS UNSIGNED)) AS maxID FROM patient";
        $fetchMaxPat = $conn->prepare($sql);
        $fetchMaxPat->execute();
        $result = $fetchMaxPat->fetch(PDO::FETCH_ASSOC);
        $maxID = $result ? $result['maxID'] : 0;
        $newPatientID = 'P' . str_pad($maxID + 1, 4, '0', STR_PAD_LEFT);

        // Insert into patient table
        $sql = "INSERT INTO patient (patientID, patientNRIC, patientName, patientPhoneNo, patientAddress, registerDate) VALUES (?, ?, ?, ?, ?, CURDATE())";
        $insertPat = $conn->prepare($sql);
        $insertPat->execute([$newPatientID, $patientNRIC, $patientName, $patientPhoneNo, $patientAddress]);

        // Insert into usertype table
        $sql = "INSERT INTO usertype (userTypeID, userType) VALUES (?, 'patient')";
        $insertUserType = $conn->prepare($sql);
        $insertUserType->execute([$newPatientID]);

        // Insert into login table
        $sql = "INSERT INTO login (userID, userPassword, userTypeID) VALUES (?, ?, ?)";
        $insertLogin = $conn->prepare($sql);
        $insertLogin->execute([$newPatientID, $hashedPassword, $newPatientID]);

        // Commit the transaction
        $conn->commit();

        // Return success response
        echo json_encode(["status" => "success", "patientID" => $newPatientID]);
    } catch (Exception $e) {
        // Rollback the transaction if something went wrong
        $conn->rollBack();
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
}
?>
