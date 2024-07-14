<?php
session_start();
$conn = include("dbconn.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $appointmentID = $_POST['appointmentID'];
    $appointmentDate = $_POST['appointmentDate'];
    $timeSlot = $_POST['timeSlot'];
	$doctorID = $_POST['doctorID'];

    // Insert data into the database
    $sql = "INSERT INTO appointment (appointmentID, appointmentDate, appointmentStatus, doctorID, patientID, timeSlot) VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $appointmentStatus = "Pending"; // Assuming appointment is initially pending
    
    if ($stmt->execute([$appointmentID, $appointmentDate, $appointmentStatus, $doctorID, $_SESSION['patientID'], $timeSlot])) {
		header("Location: AppointmentPage(patient).php");
        exit(); // Ensure that no other output is sent
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Close the database connection
$conn = null;
?>
