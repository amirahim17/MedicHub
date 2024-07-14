<?php
session_start();
include("dbconn.php");

if (!isset($_GET['appointmentID']) || !isset($_SESSION['userID'])) {
    echo "Session variables not set!";
    exit;
}

$appointmentID = $_GET['appointmentID'];
$userID = $_SESSION['userID'];
$patientID = $_SESSION['userID'];

// Fetch appointment details
$sql = "SELECT * FROM appointment WHERE appointmentID = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$appointmentID]);
$appointment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$appointment) {
    echo "Appointment not found.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Updating Booked Appointment</title>
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

        .form-container {
            background: #0D4458;
            color: #fff;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }

        label, input, select {
            display: block;
            width: 100%;
            margin-bottom: 10px;
        }

        input[type="submit"], button {
            background-color:#022332
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            font-weight: 500;
            text-decoration: none;
            transition: background-color 0.3s ease;
            cursor: pointer;
        }

        input[type="submit"]:hover, button:hover {
            background-color: #6c63ff;
        }

        .cancel-button {
            background-color: #6c63ff;
        }

        .cancel-button:hover {
            background-color: #c9302c;
        }
		.header-back {
            position: absolute;
            top: 30px; /* Adjust top position */
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
<header>
        <h1>Updating Booked Appointment Details</h1>
		<nav>
		<div class="header-back" onclick="window.location.href='AppointmentPage(patient).php?userType=patient&userID=<?php echo $_SESSION['userID'];?>'">
            <img src="https://cdn-icons-png.flaticon.com/512/93/93634.png" alt="Back">
        </div>
		</nav>
    </header>
    <div class="container">
        <div class="form-container">
            <form action="processUpdateAppointment.php" method="post" onsubmit="return confirmUpdatedBooking()">
                <label for="appointmentDate">Choose new Appointment Date (yyyy-mm-dd):</label>
                <input type="date" id="appointmentDate" name="appointmentDate" value="<?php echo $appointment['appointmentDate']; ?>" required><br>

                <label for="timeSlot">Choose a Time Slot:</label>
                <select id="timeSlot" name="timeSlot" required>
                    <option value="0800-0900" <?php if ($appointment['timeSlot'] == '0800-0900') echo 'selected'; ?>>0800-0900</option>
                    <option value="0900-1000" <?php if ($appointment['timeSlot'] == '0900-1000') echo 'selected'; ?>>0900-1000</option>
                    <option value="1000-1100" <?php if ($appointment['timeSlot'] == '1000-1100') echo 'selected'; ?>>1000-1100</option>
                    <option value="1100-1200" <?php if ($appointment['timeSlot'] == '1100-1200') echo 'selected'; ?>>1100-1200</option>
                    <option value="1400-1500" <?php if ($appointment['timeSlot'] == '1400-1500') echo 'selected'; ?>>1400-1500</option>
                    <option value="1500-1600" <?php if ($appointment['timeSlot'] == '1500-1600') echo 'selected'; ?>>1500-1600</option>
                    <option value="1600-1700" <?php if ($appointment['timeSlot'] == '1600-1700') echo 'selected'; ?>>1600-1700</option>
                    <option value="1700-1800" <?php if ($appointment['timeSlot'] == '1700-1800') echo 'selected'; ?>>1700-1800</option>
                </select><br>
				<input type="hidden" name="doctorID" value="<?php echo $appointment['doctorID'];?>">
                <input type="hidden" name="appointmentID" value="<?= $appointmentID ?>">
                <input type="submit" value="Confirm">
            </form>
        </div>
    </div>
	<script>
        function validateUpdatedDate() {
            var inputDate = document.getElementById("appointmentDate").value;
            var today = new Date();
            var input = new Date(inputDate);

            // Only allow dates after today
            today.setHours(0, 0, 0, 0);
            if (input <= today || input == today) {
                alert("Please select a valid appointment date.");
                return false;
            }
            return true;
        }

        function confirmUpdatedBooking() {
            if (validateUpdatedDate()) {
                return confirm("Do you want to confirm this updated appointment?");
            }
            return false;
        }

        function cancelAppointment() {
            if (confirm("Are you sure you want to cancel this appointment?")) {
                // Redirect to cancel_appointment.php
                window.location.href = "cancel_appointment.php";
            }
        }
		function goBack() {
        window.history.back();
    }
    </script>
</body>
</html>
