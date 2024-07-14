<?php
session_start();

// Check if the required parameters are set in the URL
if (isset($_GET['appointmentID']) && isset($_GET['userID'])) {
    $_SESSION['appointmentID'] = $_GET['appointmentID'];
    $_SESSION['doctorID'] = $_GET['userID'];
} else {
    die('Required parameters not set in URL.');
}
$doctorID = $_SESSION['userID'];
// Now the session variables are set, you can proceed with the rest of your logic
$appointmentID = $_SESSION['appointmentID'];
$doctorID = $_SESSION['doctorID'];
$userID = $_SESSION['doctorID'];

//echo "Session ID: " . session_id() . "<br>";
//echo "Appointment ID: " . $_SESSION['appointmentID'] . "<br>";
//echo "Doctor ID: " . $_SESSION['doctorID'] . "<br>";

include("dbconn.php");

// Check connection
if (!$conn) {
    die("Connection failed: " . $conn->errorInfo()[2]);
}

// Retrieve appointment data
$sql = "SELECT a.appointmentID, a.diagnosis, a.appointmentDate AS mcDate,
        p.patientName, p.patientNRIC, d.doctorName
        FROM appointment a
        JOIN patient p ON a.patientID = p.patientID
        JOIN doctor d ON d.doctorID = :doctorID
        WHERE a.appointmentID = :appointmentID";

$stmt = $conn->prepare($sql);
$stmt->execute([':doctorID' => $doctorID, ':appointmentID' => $appointmentID]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result) {
    $diagnosis = $result['diagnosis'];
    $patientName = $result['patientName'];
    $patientNRIC = $result['patientNRIC'];
    $doctorName = $result['doctorName'];
    $mcDate = $result['mcDate'];
} else {
    die("No record found for the given appointment ID.");
}

// Fetch the latest mcSerialNumber
$sql = "SELECT mcSerialNumber FROM medicalcertificate ORDER BY mcSerialNumber DESC LIMIT 1";
$result = $conn->query($sql);

if ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $latestMcSerialNumber = $row['mcSerialNumber'];
    
    // Extract the numeric part and increment it to generate a new mcSerialNumber
    $numericPart = (int)substr($latestMcSerialNumber, 2);
    $newNumericPart = $numericPart + 1;
    
    // Generate new mcSerialNumber
    $newMcSerialNumber = 'MC' . str_pad($newNumericPart, 4, '0', STR_PAD_LEFT);
} else {
    // If no mcSerialNumber found, generate a new one
    $newMcSerialNumber = 'MC0001';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the duration from the POST request and validate it
    $duration = isset($_POST['duration']) ? (int)$_POST['duration'] : 0;
    if ($duration < 1) {
        die("Duration must be at least 1 day.");
    }

    // Insert data into medicalcertificate table
    $sql = "INSERT INTO medicalcertificate (mcSerialNumber, mcDate, duration, diagnosis, appointmentID)
            VALUES (:mcSerialNumber, :mcDate, :duration, :diagnosis, :appointmentID)";
    $stmt = $conn->prepare($sql);
    $params = [
        ':mcSerialNumber' => $newMcSerialNumber,
        ':mcDate' => $mcDate,
        ':duration' => $duration,
        ':diagnosis' => $diagnosis,
        ':appointmentID' => $appointmentID
    ];

    if ($stmt->execute($params)) {
        echo "<script>alert('Medical certificate generated successfully!');</script>";
        echo "<script>window.location.href='medicalRecords(doc).php';</script>";
        exit;
    } else {
        echo "Error: " . $stmt->errorInfo()[2];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Certificate</title>
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,600,700&display=swap" rel="stylesheet">
    <style>
        .certificate {
            border: 1px solid #000;
            padding: 20px;
            margin: 20px;
            font-family: 'Poppins', sans-serif;
        }
        .certificate h2 {
            text-align: center;
        }
    </style>
</head>
<body align="center">
    <div class="certificate">
        <h2>Medical Certificate</h2>
        <form method="post" action="">
            <p><strong>Medical Certificate ID:</strong> <?php echo htmlspecialchars($newMcSerialNumber); ?></p>
            <p><strong>Appointment ID:</strong> <?php echo htmlspecialchars($appointmentID); ?></p>
            <p><strong>Reason:</strong> <?php echo htmlspecialchars($diagnosis); ?></p>
            <p><strong>Patient Name:</strong> <?php echo htmlspecialchars($patientName); ?></p>
            <p><strong>Patient NRIC:</strong> <?php echo htmlspecialchars($patientNRIC); ?></p>
            <p><strong>Doctor Name:</strong> <?php echo htmlspecialchars($doctorName); ?></p>
            <p><strong>Date:</strong> <?php echo htmlspecialchars($mcDate); ?></p>
            <p>
                <strong>Duration (days):</strong> 
                <input type="number" name="duration" min="1" required>
            </p>
            <p>
                <input type="submit" value="Generate Medical Certificate">
				<a href="medicalRecords(doc).php?userID=<?php echo $_SESSION['userID'];?>">
                <button type="button" >Back</button>
				</a>
				
            </p>
        </form>
    </div>
	<script>
	function goBack() {
					window.location.href('medicalRecords(doc).php');
				}
	</script>
</body>
</html>
