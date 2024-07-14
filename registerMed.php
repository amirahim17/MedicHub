<?php
// Connect to database
include 'dbconn.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $medName = $_POST['medName'];
    $mfgDate = $_POST['mfgDate'];
    $expDate = $_POST['expDate'];
    $quantity = $_POST['quantity'];
    $medFactory = $_POST['medFactory'];

    try {
        $conn->beginTransaction();

        // Get the latest medSerialNumber from the medication table
        $sql = "SELECT medSerialNumber FROM medication ORDER BY medSerialNumber DESC LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();

        if ($result) {
            $row = $result[0];
            $latestMedSerialNumber = $row['medSerialNumber'];

            // Get the maximum medSerialNumber and increment it
            $numericPart = (int)substr($latestMedSerialNumber, 3);
            $newNumericPart = $numericPart + 1;

            // Generate new medSerialNumber
            $newMedSerialNumber = 'MED' . str_pad($newNumericPart, 4, '0', STR_PAD_LEFT);
        } else {
            // If no medSerialNumber found, start from MED0001
            $newMedSerialNumber = 'MED0001';
        }

        // Insert data into the medication table
        $sql = "INSERT INTO medication (medSerialNumber, medName, mfgDate, expDate, quantity, medFactory) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$newMedSerialNumber, $medName, $mfgDate, $expDate, $quantity, $medFactory]);

        $conn->commit();

        echo "Medicine registered successfully.";
        // Redirect to medicine inventory page
        echo "<script>window.location.href='medicineInventory(admin).php';</script>";
    } catch (Exception $e) {
        // Rollback the transaction if something went wrong
        $conn->rollBack();
        die("Error: " . $e->getMessage());
    }
}
?>
