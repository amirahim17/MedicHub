<?php
session_start();
include 'dbconn.php';

if (!isset($_SESSION['userID'])) {
    die("User not logged in.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $serialNumber = $_POST['serialNumber'];

    try {
        $sql = "DELETE FROM medication WHERE medSerialNumber = :serialNumber";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':serialNumber', $serialNumber, PDO::PARAM_STR);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Medicine not found']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>
