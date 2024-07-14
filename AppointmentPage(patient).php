<?php
session_start();
if (!isset($_SESSION['userID']) || !isset($_SESSION['userType'])) {
    echo "Session variables not set!";
    exit;
}

include("dbconn.php");
$pdo = $conn;

// Get patient ID from session
$patientID = $_SESSION['userID'];

// Function to get appointment information based on patient ID
function getAppointmentInfo($pdo, $patientID) {
    try {
        $stmt = $pdo->prepare("SELECT a.*, d.doctorName, m.medName, mc.mcSerialNumber
                              FROM appointment a 
                              LEFT JOIN doctor d ON a.doctorID = d.doctorID 
                              LEFT JOIN prescription p ON a.prescriptionID = p.prescriptionID 
                              LEFT JOIN medication m ON p.medSerialNumber = m.medSerialNumber
                              LEFT JOIN medicalCertificate mc ON mc.appointmentID = a.appointmentID
                              WHERE a.patientID = ?");
        $stmt->bindParam(1, $patientID, PDO::PARAM_STR);
        $stmt->execute();
        $appointments = $stmt->fetchAll();

        return $appointments;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage() . "\n"; // Debugging statement
        return false;
    }
}

$appointments = getAppointmentInfo($pdo, $patientID);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment | Patient</title>
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,600,700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #7E96A4;
            color: #262626;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        header {
            background: #0D4458;
            color: #fff;
            padding: 10px 0; /* Adjusted padding to reduce header height */
            text-align: center;
            position: relative;
        }

        header h1 {
            font-size: 36px; /* Adjusted font size to match given code */
            font-weight: 700;
        }

        .header-back {
            position: absolute;
            top: 40px; 
            left: 20px;
            cursor: pointer;
        }

        .header-back img {
            width: 30px;
            height: 30px;
            filter: invert(100%);
        }

        .header-back img:hover {
            filter: brightness(50%);
        }

        nav {
            margin-top: 20px;
        }

        nav ul {
            list-style: none;
            padding: 0;
            text-align: center;
        }

        nav ul li {
            display: inline-block;
            margin: 0 10px;
        }

        nav ul li a {
            text-decoration: none;
            color: #fff;
            font-size: 18px;
            font-weight: 500;
            padding: 5px 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        nav ul li a:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .appointment-list {
            background: #0D4458;
            color: #fff;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }

        .appointment h2 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .appointment p {
            font-size: 16px;
            line-height: 1.6;
        }

        .transparent-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .transparent-table th, .transparent-table td {
            padding: 10px;
            text-align: left;
            border: 1px solid rgba(255, 255, 255, 0.3);
            background: rgba(255, 255, 255, 0.1);
        }

        .transparent-table th {
            background: rgba(255, 255, 255, 0.2);
            font-weight: 600;
        }

        .field {
            margin-top: 20px;
            text-align: center;
        }

        .field button {
            background: #0D4458;
            color: #fff;
            font-size: 18px;
            font-weight: 600;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .field button:hover {
            background: #6c63ff;
        }

        .edit-btn {
            background: #0D4458;
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .edit-btn:hover {
            background: #6c63ff;
        }

        .delete-icon {
            width: 20px;
            height: 20px;
            background: transparent;
            border: none;
            cursor: pointer;
            display: inline-block;
            vertical-align: middle;
            background-image: url('delete-icon.png'); 
            background-size: contain;
            background-repeat: no-repeat;
        }
    </style>
    <script>
        function confirmDelete(userID, appointmentID) {
            if (confirm("Are you sure you want to delete this appointment?")) {
                window.location.href = 'delAppointment(pat).php?userType=patient&userID=' + userID + '&appointmentID=' + appointmentID;
            }
        }
    </script>
</head>
<body>
<header>
    <div class="container">
        <div class="header-back" onclick="window.location.href='patientDashboard.html?userType=patient&userID=<?php echo $_SESSION['userID'];?>'">
            <img src="https://cdn-icons-png.flaticon.com/512/93/93634.png" alt="Go Back">
        </div>
        <h1>MedicHub | Appointment </h1>
    </div>
</header>
<div class="container">
    <?php if (!empty($appointments)) { ?>
        <div id="appointment" class="appointment-list">
            <h2>Appointment List</h2>
            <table class="transparent-table">
                <thead>
                <tr>
                    <th>Appointment ID</th>
                    <th>Appointment Date</th>
                    <th>Time Slot</th>
                    <th>Diagnosis</th>
                    <th>Doctor Name</th>
                    <th>Medicine Name</th>
                    <th>Appointment Status</th>
                    <th>Update Appointment</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($appointments as $appointment) { ?>
                    <?php if ($appointment['appointmentStatus'] == 'Pending') { ?>
                        <tr>
                            <td><?= $appointment['appointmentID'] ?></td>
                            <td><?= $appointment['appointmentDate'] ?></td>
                            <td><?= $appointment['timeSlot'] ?></td>
                            <td><?= $appointment['diagnosis'] ?></td>
                            <td><?= $appointment['doctorName'] ?></td>
                            <td><?= $appointment['medName'] ?></td>
                            <td><?= $appointment['appointmentStatus'] ?></td>
                            <td>
                                <button class="edit-btn" onclick="location.href='updatingAppointment(pat).php?userType=patient&userID=<?= $patientID ?>&appointmentID=<?= $appointment['appointmentID'] ?>'">Update</button>
                                <a href="javascript:void(0);" onclick="confirmDelete('<?= $patientID ?>', '<?= $appointment['appointmentID'] ?>')">
                                    <img src="https://cdn-icons-png.freepik.com/256/64/64022.png" alt="Delete" class="delete-icon">
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } else { ?>
        <p>No booked appointment found!</p>
    <?php } ?>
    <div class="field">
        <button onclick="window.location.href='appointmentBook.php'">BOOK NEW APPOINTMENT</button>
    </div>
</div>
</body>
</html>
