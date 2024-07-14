<?php
session_start(); // Ensure this is at the very top before any output

include 'dbconn.php';

// Check if the user ID is set in the session
if (!isset($_SESSION['userID'])) {
    die("User not logged in.");
}

$patientID = $_SESSION['userID'];

try {
    $sql = "SELECT 
        a.appointmentID, a.appointmentDate, a.diagnosis, m.medName, d.doctorName, mc.mcSerialNumber
        FROM appointment a
        JOIN prescription p ON a.prescriptionID = p.prescriptionID
        JOIN medication m ON p.medSerialNumber = m.medSerialNumber
        JOIN doctor d ON a.doctorID = d.doctorID
        LEFT JOIN medicalcertificate mc ON a.appointmentID = mc.appointmentID
        JOIN patient pa ON a.patientID = pa.patientID
        JOIN login l ON pa.patientID = l.userID
        JOIN usertype u ON l.userTypeID = u.userTypeID
        WHERE l.userID = :patientID AND a.appointmentStatus = 'Completed'";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':patientID', $patientID, PDO::PARAM_STR);
    $stmt->execute();
    
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($result === false) {
        throw new Exception("Error getting result set: " . $stmt->errorInfo()[2]);
    }
    
} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
    die();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Records | Patient</title>
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
            padding: 20px 0;
            text-align: center;
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

        .clinic-appointment {
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
            color: #fff;
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
		.edit-btn {
		  background-color: #0D4458;
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
		  background-color: #4158d0;
		  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
		}
		.header-back {
            position: absolute;
            top: 30px;
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
    </style>
</head>
<body>
<input type="hidden" id="userID" value="<?php echo htmlspecialchars($_SESSION['userID']); ?>">
    <header>
        <h1>MedicHub | Records</h1>
        <nav>
            <div class="header-back" onclick="window.location.href='patientDashboard.html?userType=patient&userID=<?php echo $_SESSION['userID'];?>'">
            <img src="https://cdn-icons-png.flaticon.com/512/93/93634.png" alt="Back">
        </div>
        </nav>
    </header>

    <div class="container">
        <div id="appointment" class="clinic-appointment">
            <h2>Medical Records</h2>
            <table class="transparent-table">
                <thead>
                    <tr>
                        <th>Appointment ID</th>
                        <th>Date</th>
                        <th>Diagnosis</th>
                        <th>Prescribed Medicine</th>
                        <th>Doctor in Charge</th>
                        <th>Medical Certificate</th>
						<th>Action</th>
                    </tr>
                </thead>
                <tbody>
				<?php
				if ($result && count($result) > 0) {
					foreach ($result as $row) {
						echo "<tr>";
						echo "<td>". htmlspecialchars($row['appointmentID']). "</td>";
						echo "<td>". htmlspecialchars($row['appointmentDate']). "</td>";
						echo "<td>". htmlspecialchars($row['diagnosis']). "</td>";
						echo "<td>". htmlspecialchars($row['medName']). "</td>";
						echo "<td>". htmlspecialchars($row['doctorName']). "</td>";
						echo "<td>". htmlspecialchars($row['mcSerialNumber']). "</td>";
						echo "<td>";
                        if ($row['mcSerialNumber'] != null) {
                            echo '<button class="edit-btn" onclick="location.href=\'viewMC.php?userType=patient&userID=' . $patientID . '&appointmentID=' . $row['appointmentID'] . '\'">Download</button>';
                        }
                        echo "</td>";
						echo "</tr>";
					}
				} else {
					echo "<tr><td colspan='7'>No completed appointment found! </td></tr>";
				}
			   ?>
				</tbody>
            </table>
        </div>
    </div>
</body>
<script>
	function goBack() {
            window.history.back();
        }

</script>
</html>
