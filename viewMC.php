<?php
session_start();
include("dbconn.php");

// Check connection
if (!$conn) {
    die("Connection failed: " . $conn->errorInfo()[2]);
}

// Check if the required parameters are set in the URL
if (isset($_GET['appointmentID']) && isset($_GET['userID'])) {
    $appointmentID = $_GET['appointmentID'];
    $patientID = $_GET['userID'];
} else {
    die('Required parameters not set in URL.');
}

// Retrieve medical certificate data based on appointmentID
$sql = "SELECT mc.mcSerialNumber, mc.mcDate, mc.duration, 
               a.diagnosis, a.appointmentID, 
               p.patientName, p.patientNRIC, 
               d.doctorName, d.doctorNRIC
        FROM medicalcertificate mc
        JOIN appointment a ON mc.appointmentID = a.appointmentID
        JOIN patient p ON a.patientID = p.patientID
        JOIN doctor d ON a.doctorID = d.doctorID
        WHERE a.appointmentID = :appointmentID AND p.patientID = :patientID";

$stmt = $conn->prepare($sql);
$stmt->execute([':appointmentID' => $appointmentID, ':patientID' => $patientID]);
$certificate = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$certificate) {
    die("No medical certificate found for the given appointment ID.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedicHub | Medical Certificate View</title>
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,600,700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        .certificate {
            width: 148mm; /* A5 width */
            height: 210mm; /* A5 height */
            padding: 10mm;
            margin: auto;
            border: 1px solid #000;
            box-sizing: border-box;
            position: relative; /* Ensure relative positioning */
        }
        .header, .footer {
            text-align: center;
            margin-bottom: 10px;
        }
        .content {
            margin: 20px 0;
        }
        .footer {
            position: absolute;
            bottom: 10mm;
            right: 10mm;
            text-align: right;
        }
        .footer p {
            margin: 5px 0;
        }
        @media print {
            body, html {
                width: 148mm;
                height: 210mm;
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            .certificate {
                margin: 20;
                border: none;
            }
            .footer {
                position: absolute;
                bottom: 10mm;
                right: 10mm;
                text-align: right;
            }
            .no-print {
                margin: 10;
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" onclick="window.location.href='medicalRecords(patient).php'">
        <img src="https://cdn-icons-png.flaticon.com/512/93/93634.png" width="20px" height="20px" alt="Back">
    </div>
    <div class="certificate">
        <div class="header">
            <img src="Logo.png" alt="Logo" style="max-width: 200px;">
            <h1>MedicHub</h1>
            <p>123 Health Street, Raub, Pahang</p>
            <h2><u>Medical Certificate</u></h2>
        </div>
        <div class="content">
            <p>I hereby confirm that I have examined Mr./Ms./Mrs. <u><?php echo htmlspecialchars($certificate['patientName']); ?></u>, NRIC<u><?php echo htmlspecialchars($certificate['patientNRIC']); ?></u>, and found that they are unwell due to <u><?php echo htmlspecialchars($certificate['diagnosis']); ?></u> to carry out work/school for the duration of <u><?php echo htmlspecialchars($certificate['duration']); ?></u> day(s) starting from <u><?php echo htmlspecialchars($certificate['mcDate']); ?></u>.</p>
        </div>
        <div class="footer">
            <p><?php echo htmlspecialchars($certificate['doctorName']); ?></p>
            <p>NRIC: <?php echo htmlspecialchars($certificate['doctorNRIC']); ?></p>
            <p>MedicHub</p>
            <p>123 Health Street, Raub, Pahang</p>
        </div>
    </div>
</body>
</html>
