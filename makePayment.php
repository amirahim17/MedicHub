<?php
session_start();
include("dbconn.php");

// Check connection
if (!$conn) {
    die("Connection failed: " . $conn->errorInfo()[2]);
}

// Check if the required parameters are set in the URL
if (isset($_GET['appointmentID'])) {
    $appointmentID = $_GET['appointmentID'];
} else {
    die('Required parameters not set in URL.');
}

// Function to generate a new payment ID
function generatePaymentID($conn) {
    $sql = "SELECT paymentID FROM payment ORDER BY paymentID DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $lastPayment = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($lastPayment) {
        $lastID = $lastPayment['paymentID'];
        $number = (int)substr($lastID, 3);
        $newNumber = str_pad($number + 1, 4, '0', STR_PAD_LEFT);
        $newPaymentID = 'PAY' . $newNumber;
    } else {
        $newPaymentID = 'PAY0001';
    }

    return $newPaymentID;
}

// Generate new payment ID
$paymentID = generatePaymentID($conn);

// Fetch appointment details
$sql = "SELECT a.*, p.patientName, d.doctorName 
        FROM appointment a
        JOIN patient p ON a.patientID = p.patientID
        JOIN doctor d ON a.doctorID = d.doctorID
        WHERE a.appointmentID = :appointmentID";
$stmt = $conn->prepare($sql);
$stmt->execute([':appointmentID' => $appointmentID]);
$appointment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$appointment) {
    die("Invalid appointment ID!");
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $riskPanelAvailability = $_POST['riskPanelAvailability'];
    $panelName = $_POST['panelName'];
    $adminID = $_SESSION['adminID']; // Assuming admin ID is stored in session

    // Assign default value for billCharges if hidden
    if ($riskPanelAvailability === 'yes') {
        $billCharges = 0; // Set default value to 0 when hidden
    } else {
        $billCharges = $_POST['billCharges'];
    }

    // Insert payment information
    $sql = "INSERT INTO payment (paymentID, riskPanelAvailability, panelName, billCharges, appointmentID, adminID)
            VALUES (:paymentID, :riskPanelAvailability, :panelName, :billCharges, :appointmentID, :adminID)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':paymentID' => $paymentID,
        ':riskPanelAvailability' => $riskPanelAvailability,
        ':panelName' => $panelName,
        ':billCharges' => $billCharges,
        ':appointmentID' => $appointmentID,
        ':adminID' => $adminID
    ]);

    echo "<script>alert('Payment information saved successfully!'); window.location.href = 'payment(admin).php';</script>";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedicHub | Make Payment</title>
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

        .container {
            display: flex;
            justify-content: center;
            margin: 20px;
        }

        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            width: 500px;
        }

        .form-container form {
            display: flex;
            flex-direction: column;
        }

        .form-container form label {
            margin-bottom: 5px;
        }

        .form-container form input, .form-container form select {
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .form-container form button {
            padding: 10px;
            border: none;
            background-color: #0D4458;
            color: #fff;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .form-container form button:hover {
            background-color: rgba(34, 30, 92);
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

        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <header>
        <h1>MedicHub | Make Payment</h1>
        <nav>
            <div class="header-back" onclick="goBack()">
                <img src="https://cdn-icons-png.flaticon.com/512/93/93634.png" alt="Back">
            </div>
        </nav>
    </header>
    <div class="container">
        <div class="form-container">
            <h2>Make Payment for Appointment ID: <?php echo htmlspecialchars($appointmentID); ?></h2>
            <form method="post" action="">
                <label for="riskPanelAvailability">Risk Panel Availability</label>
                <select id="riskPanelAvailability" name="riskPanelAvailability" required onchange="toggleFields()">
                    <option value="yes">Yes</option>
                    <option value="no">No</option>
                </select>

                <div id="panelNameContainer">
                    <label for="panelName">Panel Name</label>
                    <select id="panelName" name="panelName">
                        <option value="AIA Berhad">AIA Berhad</option>
                        <option value="Prudential Sdn. Bhd.">Prudential Sdn. Bhd.</option>
                        <option value="Allianz Malaysia Berhad">Allianz Malaysia Berhad</option>
                        <option value="Zurich Insurance Group">Zurich Insurance Group</option>
                        <option value="Berkshire Hathaway Inc.">Berkshire Hathaway Inc.</option>
                        <option value="Aviva Insurance Berhad">Aviva Insurance Berhad</option>
                        <option value="AXA Insurance Berhad">AXA Insurance Berhad</option>
                    </select>
                </div>

                <div id="billChargesContainer">
                    <label for="billCharges">Bill Charges</label>
                    <input type="text" id="billCharges" name="billCharges" required>
                </div>

                <button type="submit">Submit</button>
            </form>
        </div>
    </div>
    <script>
        function goBack() {
            window.history.back();
        }

        function toggleFields() {
            const riskPanelAvailability = document.getElementById('riskPanelAvailability').value;
            const panelNameContainer = document.getElementById('panelNameContainer');
            const panelNameSelect = document.getElementById('panelName');
            const billChargesContainer = document.getElementById('billChargesContainer');
            const billChargesInput = document.getElementById('billCharges');

            if (riskPanelAvailability === 'yes') {
                panelNameContainer.classList.remove('hidden');
                panelNameSelect.disabled = false;
                billChargesContainer.classList.add('hidden');
                billChargesInput.value = '-';
                billChargesInput.disabled = true;
            } else {
                panelNameContainer.classList.add('hidden');
                panelNameSelect.disabled = true;
                panelNameSelect.value = '-';
                billChargesContainer.classList.remove('hidden');
                billChargesInput.value = '';
                billChargesInput.disabled = false;
            }
        }

        // Initialize panel name and bill charges display based on default selection
        document.addEventListener('DOMContentLoaded', function() {
            toggleFields();
        });
    </script>
</body>
</html>
