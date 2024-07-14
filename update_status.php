<?php
// Ensure session is started and handle POST request properly
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include("dbconn.php"); // Ensure correct path to your database connection script
    $pdo = $conn;

    // Ensure proper handling of JSON input
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (!$data || !isset($data['appointmentId']) || !isset($data['appointmentStatus'])) {
        // Handle invalid JSON or missing data
        $response = [
            'status' => 'error',
            'message' => 'Invalid data received.'
        ];
        echo json_encode($response);
        exit;
    }

    // Extract data from JSON
    $appointmentID = $data['appointmentId'];
    $appointmentStatus = $data['appointmentStatus'];

    // Update appointment status in the database
    try {
        $stmt = $pdo->prepare("UPDATE appointment SET appointmentStatus = ? WHERE appointmentID = ?");
        $stmt->execute([$appointmentStatus, $appointmentID]);

        $response = [
            'status' => 'success',
            'message' => 'Appointment status updated successfully!'
        ];
        echo json_encode($response);
    } catch (PDOException $e) {
        $response = [
            'status' => 'error',
            'message' => 'Failed to update appointment status: ' . $e->getMessage()
        ];
        echo json_encode($response);
    }
} else {
    // Handle invalid requests
    $response = [
        'status' => 'error',
        'message' => 'Invalid request method!'
    ];
    echo json_encode($response);
}
?>
