<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Booking</title>
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
            display: flex;
            flex-direction: column;
            align-items: center;
            
        }
        header {
            background: #0D4458;
            color: #fff;
            padding: 20px ;
            text-align: center;
            width: 100%;
            position: sticky;
            top: 0;
            z-index: 1000;
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
		.details{
			background-color: #fff;
            color: #262626;
            border-radius: 10px;
            padding: 20px;
			width: 500px;
			box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
			margin-top: 20px;
            text-align: left;
		}
		.details h2{
			margin-bottom: 20px;
            font-size: 24px;
			font-weight: 600;
		}
		.details form {
            display: flex;
            flex-direction: column;
        }
        .details form label {
             margin: 10px 0 5px;
        }
        .details form input {
            margin: 5px 0 10px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
               .details form.submit-button {
            background-color: #4158d0;
            color: #fff;
                    padding: 10px 20px;
                    border: none;
                    border-radius: 5px;
                    cursor: pointer;
                    font-size: 16px;
                    margin-top: 20px;
                    transition: background-color 0.3s ease;
                }
               .details form.submit-button:hover {
                    background-color: rgb(34, 30, 92);
                }
    </style>
</head>
<body>
   <body class="appointment-page">
    <header>
        <h1>MedicHub</h1>
        <nav>
            <div class="header-back" onclick="goBack()">
                <img src="https://cdn-icons-png.flaticon.com/512/93/93634.png" alt="Back">
            </div>
        </nav>
    </header>
    <?php
        session_start();
        $conn = include("dbconn.php");
        
        // to check if patientID is set in the session
        if (!isset($_SESSION['patientID'])){
            die('Patient ID is not set here :0 ');
        }
        
        //retrieve patientID from prev session
        $patientID = $_SESSION['patientID'];

        //fetching latest appointmentID to generate new one
        $sql = "SELECT appointmentID FROM appointment ORDER BY appointmentID DESC LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        
        if($result){
            $row = $result[0];
            $latestAppointmentID = $row['appointmentID'];
            
            // extracting the numeric part and increment it to generate new appointmentID :0
            $numericPart = (int)substr($latestAppointmentID, 3);
            $newNumericPart = $numericPart + 1;
            
            // generating new appointmentID ;0
            $newAppointmentID = 'APP'.str_pad($newNumericPart, 4,'0', STR_PAD_LEFT );
            
        }else {
            // if no appointmentID found, generate a new one
            $newAppointmentID = 'APP0001';
        }
        
        // fetching the patient data based on patientID
        $sql = "SELECT * FROM patient WHERE patientID =? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$patientID]);
        $result = $stmt->fetchAll();
        
        //output 
        
        //if($result){
            //$row = $result[0];
            //echo "Appointment ID: $newAppointmentID<br>";
            //echo "Patient ID: ". $row['patientID']. "<br>";
            //echo "Patient Name: ". $row['patientName']. "<br>";
        //} else{
            //echo "No data found for ". $patientID;
         
    ?>
   <div class = "details">
   <h2>Booking Appointment</h2>
    <form action="processAppointment.php" method="post" onsubmit="return confirmBooking()">
        <input type="hidden" name="appointmentID" value="<?php echo $newAppointmentID; ?>">
        <label for="appointmentDate">Choose an Appointment Date (dd-mm-yyyy):</label>
        <input type="date" id="appointmentDate" name="appointmentDate" required> <br>
        <label for="timeSlot">Choose a Time Slot:</label>
        <select id="timeSlot" name="timeSlot" required><br>
            <option value="0800-0900">0800-0900</option>
            <option value="0900-1000">0900-1000</option>
            <option value="1000-1100">1000-1100</option>
            <option value="1100-1200">1100-1200</option>
            <option value="1400-1500">1400-1500</option>
            <option value="1500-1600">1500-1600</option>
            <option value="1600-1700">1600-1700</option>
            <option value="1700-1800">1700-1800</option>
        </select><br>
		<label for="doctorID">Choose Doctor in Charge:</label>
		<select id="doctorID" name="doctorID" required>
			<option value="D0001">D0001 - Dr. Harith Johari</option>
			<option value="D0002">D0002 - Dr. Rashid</option>
			<option value="D0003">D0003 - Dr. Faqiha</option>
			<option value="D0004">D0004 - Dr. Laila</option>
		</select><br>
        <button type="submit" class = "submit-button">CONFIRM </button>
    </form>
	</div>
	    <script>
        function validateDate() {
            var inputDate = document.getElementById("appointmentDate").value;
            var today = new Date();
            var input = new Date(inputDate);
            
            // Only allow dates after today
            today.setHours(0, 0, 0, 0);
            if (input <= today) {
                alert("Please select a valid appointment date.");
                return false;
            }
            return true;
        }

        function confirmBooking() {
            if (validateDate()) {
                var confirmed = confirm("Do you want to confirm this appointment?");
                if (confirmed) {
                    alert("Successfully created new appointment");
                }
                return confirmed;
            }
            return false;
        }
		function goBack(){
				window.history.back();
			}
    </script>
</body>
</html>
