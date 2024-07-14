<?php
include 'dbconn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $serialNumber = $_POST['serialNumber'];
    $change = (int)$_POST['change'];

    try {
        $sql = "UPDATE medication SET quantity = quantity + :change WHERE medSerialNumber = :serialNumber";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':change', $change, PDO::PARAM_INT);
        $stmt->bindParam(':serialNumber', $serialNumber, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'No rows affected']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>
