<?php
session_start();
include("dbconn.php");

// Check connection
if (!$conn) {
    die("Connection failed: " . $conn->errorInfo()[2]);
}

/// Fetch all appointments including paymentID
$sql = "SELECT a.appointmentID, p.patientName, d.doctorName, a.appointmentDate, 
        IFNULL(pay.paymentID, 'Not Paid') AS paymentID , pay.billCharges
        FROM appointment a
        JOIN patient p ON a.patientID = p.patientID
        JOIN doctor d ON a.doctorID = d.doctorID
        LEFT JOIN payment pay ON a.appointmentID = pay.appointmentID";
$stmt = $conn->prepare($sql);
$stmt->execute();
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedicHub | Admin Payment</title>
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,600,700&display=swap" rel="stylesheet">
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

        header nav {
            position: absolute;
            top: 50px; /* Adjusted to lower the button */
            left: 10px;
        }

        .container {
            display: flex;
            margin: 20px;
        }

        .sidebar {
            background-color: #fff;
            color: #fff;
            padding: 20px;
            width: 200px;
            border-radius: 10px;
        }

        .sidebar button {
            display: inline-block;
            width: 100%;
            background: #fff;
            color: #fff;
            padding: 10px;
            margin-bottom: 10px;
            text-align: center;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 500;
            transition: background-color 0.3s ease;
            background-clip: text;
            -webkit-background-clip: text;
            background: #6c63ff;
            outline: none;
        }

        .sidebar button:hover {
            background-color: rgba(34, 30, 92);
        }

        .main-content {
            flex-grow: 1;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            margin-left: 20px;
        }

        .appointment-list {
            margin-bottom: 20px;
        }

        .appointment-list table {
            width: 100%;
            border-collapse: collapse;
        }

        .appointment-list th, .appointment-list td {
            padding: 10px;
            border: 1px solid #ddd;
        }

        .appointment-list th {
            background-color: #0D4458;
            color: #fff;
        }

        .appointment-list td button {
            padding: 5px 10px;
            background-color: #0D4458;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .appointment-list td button:hover {
            background-color: rgba(34, 30, 92);
        }
    </style>
</head>
<body>
    <header>
        <h1>MedicHub</h1>
        <nav>
            <button class="back-button" onclick="window.location.href='homePageAdmin.html'" style="border: none; background: none;">
                <img src="https://cdn-icons-png.flaticon.com/512/93/93634.png" width="30" height="30" alt="Go Back">
            </button>
        </nav>
    </header>
    <div class="container">
        <div class="main-content">
            <h2>Payment</h2>
			<div></div>
            <div class="appointment-list">
                <table>
                    <thead>
                        <tr>
                            <th>Appointment ID</th>
                            <th>Patient Name</th>
                            <th>Doctor Name</th>
                            <th>Appointment Date</th>
                            <th>Payment ID</th>
							<th>Total Payment</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appointments as $appointment): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($appointment['appointmentID']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['patientName']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['doctorName']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['appointmentDate']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['paymentID']); ?></td>
							<td><?php echo htmlspecialchars($appointment['billCharges']); ?></td>
                            <td>
                                <?php if ($appointment['paymentID'] === 'Not Paid'): ?>
                                    <form action="makePayment.php" method="get">
                                        <input type="hidden" name="appointmentID" value="<?php echo htmlspecialchars($appointment['appointmentID']); ?>">
                                        <button type="submit">Make Payment</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
