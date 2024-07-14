<?php
session_start();
$conn = include("dbconn.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $appointmentID = $_POST['appointmentID'];
    $appointmentDate = $_POST['appointmentDate'];
    $timeSlot = $_POST['timeSlot'];
    $doctorID = $_POST['doctorID'];

    // Update data in the database
    $sql = "UPDATE appointment SET appointmentDate = ?, timeSlot = ? WHERE appointmentID = ?";
    
    $stmt = $conn->prepare($sql);
    
    if ($stmt->execute([$appointmentDate, $timeSlot, $appointmentID])) {
        header("Location: AppointmentPage(patient).php");
        exit(); // Ensure that no other output is sent
    } else {
        echo "Error: ". $sql. "<br>". $conn->error;
    }
}

// Close the database connection
$conn = null;
?>