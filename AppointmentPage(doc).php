<?php
session_start();
if (!isset($_SESSION['userID']) || !isset($_SESSION['userType'])) {
    echo "Session variables not set!";
    exit;
}

include("dbconn.php");
$pdo = $conn;

// Get doctor ID from session
$doctorID = $_SESSION['userID'];

// Function to get appointment information based on doctor ID
function getAppointmentInfo($pdo, $doctorID) {
    try {
        $stmt = $pdo->prepare("SELECT DISTINCT a.*, p.patientName, mn.medName, mc.mcSerialNumber
                              FROM appointment a
                              LEFT JOIN medicalcertificate mc ON a.appointmentID = mc.appointmentID
                              LEFT JOIN patient p ON a.patientID = p.patientID 
                              LEFT JOIN prescription pr ON a.prescriptionID = pr.prescriptionID 
                              LEFT JOIN medication mn ON pr.medSerialNumber = mn.medSerialNumber 
                              WHERE a.doctorID = ?");
        $stmt->bindParam(1, $doctorID, PDO::PARAM_STR);
        $stmt->execute();
        $appointments = $stmt->fetchAll();

        return $appointments;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage() . "\n"; // Debugging statement
        return false;
    }
}

$appointments = getAppointmentInfo($pdo, $doctorID);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment | Doctor</title>
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
            background-color:  #7E96A4;
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
            padding: 20px 0;
            text-align: center;
            position: relative;
        }

        header h1 {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 10px;
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
            background: #fff;
            color: #fff; /* Changed to white color */
            font-size: 18px;
            font-weight: 500;
            padding: 5px 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            background-clip: text;
            -webkit-background-clip: text;
            background: #6c63ff;
        }

        nav ul li a:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        nav ul li .login-btn {
            background-color: transparent;
            border: none;
            cursor: pointer;
        }

        nav ul li .login-btn i {
            font-size: 20px;
            color: #fff;
            transition: color 0.3s ease;
        }

        nav ul li .login-btn i:hover {
            color: rgba(255, 255, 255, 0.7);
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

        .btn-container {
            text-align: center;
            margin-top: 20px;
        }

        .btn {
            background-color: #4158d0;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            font-weight: 500;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #3448b7;
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
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .field button {
            color: #0D4458;
            border: none;
            padding-left: 0;
            margin-top: -10px;
            font-size: 20px;
            font-weight: 500;
            cursor: pointer;
            background: #6c63ff;
            transition: all 0.3s ease;
            margin-left: 10px;
        }
        
        .field button:hover{
            background: rgb(34, 30, 92);
        }
        
        .edit-btn {
          background:#0D4458;
          color: #fff;
          padding: 10px 20px;
          border: none;
          border-radius: 5px;
          cursor: pointer;
          font-size: 16px;
          font-weight: 500;
          text-transform: uppercase;
          letter-spacing: 1px;
          box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .edit-btn:hover {
          background-color: #6c63ff;
          box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        .header-back {
                position: absolute;
                top: 50%;
                left: 20px;
                transform: translateY(-50%);
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
    </style>
</head>
<body>
<header>
    <h1>MedicHub | Doctor </h1>
    <nav>
            <div class="header-back">
                <a href="homePageStaff.html?userType=doctor&userID=<?php echo $_SESSION['userID'];?>">
                    <img src="https://cdn-icons-png.flaticon.com/512/93/93634.png" alt="Back">
                </a>
            </div>
        </nav>
</header>

<div class="container">
    <div id="appointment" class="appointment-list">
        <h2>Appointment List</h2>
        <?php 
        $hasPendingAppointments = false; 
        if ($appointments): ?>
        <table class="transparent-table">
            <thead>
            <tr>
                <th>Appointment Status</th>
                <th>Appointment ID</th>
                <th>Patient ID</th>
                <th>Patient Name</th>
                <th>Appointment Date</th>
                <th>Time Slot</th>
                <th>Diagnosis</th>
                <th>Prescription</th>
                <th>Edit Appointment</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($appointments as $appointment): ?>
                <?php if ($appointment['appointmentStatus'] == 'Pending'): ?>
                    <?php $hasPendingAppointments = true; ?>
                    <tr>
                        <td><button class="editgn-btn" onclick="updateStatus('<?php echo $appointment['appointmentID'];?>')">Click to Complete</button></td>
                        <td><?= $appointment['appointmentID'] ?></td>
                        <td><?= $appointment['patientID'] ?></td>
                        <td><?= $appointment['patientName'] ?></td>
                        <td><?= $appointment['appointmentDate'] ?></td>
                        <td><?= $appointment['timeSlot'] ?></td>
                        <td><?= $appointment['diagnosis'] ?></td>
                        <td><?= $appointment['medName'] ?></td>
                        <td>
                            <button class="editgn-btn" onclick="location.href='editApp.php?userType=doctor&userID=<?= $doctorID ?>&appointmentID=<?= $appointment['appointmentID'] ?>'">EDIT</button>
                        </td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php if (!$hasPendingAppointments): ?>
            <p>No pending appointments found!</p>
        <?php endif; ?>
        <?php else: ?>
            <p>No pending appointments found!</p>
        <?php endif; ?>
    </div>
</div>

<script>
// Ensure appointments array is available globally for JavaScript
const appointments = <?php echo json_encode($appointments); ?>;
function goBack() {
        window.history.back();
    }

function updateStatus(appointmentID) {
    console.log('Updating status for appointment ID:', appointmentID);

    // Find the appointment in the existing appointments array
    const appointment = appointments.find(appt => appt.appointmentID === appointmentID);

    if (appointment) {
        const { diagnosis, medName } = appointment;

        // Check if Diagnosis and Prescription (medName) are not empty or null
        if (diagnosis && medName) {
            // Both Diagnosis and Prescription are set, confirm update
            if (confirm('Are you sure you want to mark this appointment as Completed?')) {
                fetch('update_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ appointmentId: appointmentID, appointmentStatus: 'Completed' }),
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Response:', data);
                    if (data.status === 'success') {
                        alert('Appointment status updated successfully.');
                        location.reload();
                    } else {
                        alert('Failed to update appointment status.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while updating the appointment status.');
                });
            }
        } else {
            // Either Diagnosis or Prescription is not set, show error message
            alert('Diagnosis and Prescription must be set before completing the appointment.');
        }
    } else {
        // Appointment not found in the appointments array
        console.error('Appointment not found for ID:', appointmentID);
        alert('Appointment details not found. Please refresh the page.');
    }
}
</script>

</body>
</html>
