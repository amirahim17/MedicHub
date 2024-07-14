<?php
session_start();
if (!isset($_SESSION['userID']) || !isset($_SESSION['userType'])) {
    echo "Session variables not set!";
    exit;
}

include("dbconn.php");
$pdo = $conn;

// Get user type, user ID, and appointment ID from the URL
$userType = $_GET['userType'];
$userID = $_GET['userID'];
$appointmentID = $_GET['appointmentID'];

if ($userType !== 'patient' || $userID !== $_SESSION['userID']) {
    echo "Invalid request!";
    exit;
}

// Function to get appointment status based on appointment ID
function getAppointmentStatus($pdo, $appointmentID) {
    try {
        $stmt = $pdo->prepare("SELECT appointmentStatus FROM appointment WHERE appointmentID = ?");
        $stmt->bindParam(1, $appointmentID, PDO::PARAM_STR); // Correct parameter type to PDO::PARAM_STR
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['appointmentStatus'] : null;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return null;
    }
}

// Function to delete related records
function deleteRelatedRecords($pdo, $appointmentID) {
    try {
        // Delete related medical certificates
        $stmt = $pdo->prepare("DELETE FROM medicalcertificate WHERE appointmentID = ?");
        $stmt->bindParam(1, $appointmentID, PDO::PARAM_STR); // Correct parameter type to PDO::PARAM_STR
        $stmt->execute();

        // Delete related payments
        $stmt = $pdo->prepare("DELETE FROM payment WHERE appointmentID = ?");
        $stmt->bindParam(1, $appointmentID, PDO::PARAM_STR); // Correct parameter type to PDO::PARAM_STR
        $stmt->execute();

        // Fetch prescriptionID related to the appointment
        $stmt = $pdo->prepare("SELECT prescriptionID FROM appointment WHERE appointmentID = ?");
        $stmt->bindParam(1, $appointmentID, PDO::PARAM_STR); // Correct parameter type to PDO::PARAM_STR
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result && $result['prescriptionID']) {
            // Delete related prescription
            $stmt = $pdo->prepare("DELETE FROM prescription WHERE prescriptionID = ?");
            $stmt->bindParam(1, $result['prescriptionID'], PDO::PARAM_STR); // Correct parameter type to PDO::PARAM_STR
            $stmt->execute();
        }

        return true;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}

// Function to delete appointment based on appointment ID
function deleteAppointment($pdo, $appointmentID) {
    try {
        // Fetch the current appointment status
        $appointmentStatus = getAppointmentStatus($pdo, $appointmentID);
        if ($appointmentStatus === null) {
            echo "Appointment not found!";
            return false;
        }

        // Debugging: Print the appointment status
        echo "Current appointment status: " . $appointmentStatus . "<br>";

        // Check if appointment status is 'Pending'
        if ($appointmentStatus === 'Pending') {
            // Delete related records first
            if (!deleteRelatedRecords($pdo, $appointmentID)) {
                echo "Failed to delete related records.";
                return false;
            }

            // Delete the appointment
            $stmt = $pdo->prepare("DELETE FROM appointment WHERE appointmentID = ?");
            $stmt->bindParam(1, $appointmentID, PDO::PARAM_STR); // Correct parameter type to PDO::PARAM_STR
            $stmt->execute();
            return true;
        } else {
            echo "Appointment cannot be deleted as it is already " . $appointmentStatus . ".";
            return false;
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}

if (deleteAppointment($pdo, $appointmentID)) {
    echo "Appointment deleted successfully!";
    // Redirect back to the appointments page
	// echo "<script>alert('Successfully deleted an appointment');</script>";
    header("Location: AppointmentPage(patient).php");
    exit;
} else {
    echo "Failed to delete the appointment.";
}
?>
