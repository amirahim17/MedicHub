<?php
session_start();
if (!isset($_SESSION['userID']) || !isset($_SESSION['userType'])) {
    echo "Session variables not set!";
    exit;
}

include("dbconn.php");
$pdo = $conn;

$userType = $_GET['userType'];
$userID = $_GET['userID'];
$appointmentID = $_GET['appointmentID'];
$doctorID = $_SESSION['userID'];

function getAppointmentInfo($pdo, $appointmentID) {
    try {
        $stmt = $pdo->prepare("SELECT DISTINCT a.*, p.patientName, mn.medName, mc.mcSerialNumber, pr.prescriptionID
                              FROM appointment a
                              LEFT JOIN medicalcertificate mc ON a.appointmentID = mc.appointmentID
                              LEFT JOIN patient p ON a.patientID = p.patientID 
                              LEFT JOIN prescription pr ON a.prescriptionID = pr.prescriptionID 
                              LEFT JOIN medication mn ON pr.medSerialNumber = mn.medSerialNumber 
                              WHERE a.appointmentID =?");
        $stmt->bindParam(1, $appointmentID, PDO::PARAM_STR);
        $stmt->execute();
        $appointment = $stmt->fetch();
        if ($appointment) {
            return $appointment;
        } else {
            echo "Appointment not found for appointment ID $appointmentID!\n";
            return false;
        }
    } catch (PDOException $e) {
        echo "Error: ". $e->getMessage(). "\n";
        return false;
    }
}

if ($userType === 'doctor') {
    $appointment = getAppointmentInfo($pdo, $appointmentID);
    if ($appointment) {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Updating Appointment</title>
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                    font-family: 'Poppins', sans-serif;
                }

                body {
                    background-color: #f2f2f2;
                    color: #262626;
                    scroll-behavior: smooth;
                    background: url(medichub1.png) no-repeat center center fixed;
                    background-size: cover;
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
                    background: #fff;
                    color: #fff;
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

                .header-logout {
                    position: absolute;
                    top: 50%;
                    right: 20px;
                    transform: translateY(-50%);
                    cursor: pointer;
                }

                .header-logout img {
                    width: 30px;
                    height: 30px;
                    filter: invert(100%);
                }

                .header-logout img:hover {
                    filter: brightness(50%);
                }

                .appointment-card {
                    background: #0D4458;
                    color: #fff;
                    border-radius: 10px;
                    padding: 20px;
                    margin-top: 20px;
                }

                .appointment-card h2 {
                    font-size: 24px;
                    font-weight: 600;
                    margin-bottom: 10px;
                }

                .appointment-card form {
                    display: flex;
                    flex-direction: column;
                }

                .appointment-card label {
                    font-size: 16px;
                    font-weight: 500;
                    margin: 10px 0 5px;
                }

                .appointment-card input, .appointment-card select {
                    padding: 10px;
                    border-radius: 5px;
                    border: none;
                    margin-bottom: 10px;
                    font-size: 16px;
                }

                .submit-button {
                    background-color: #262626;
                    color: #fff;
                    padding: 10px 20px;
                    border: none;
                    border-radius: 5px;
                    font-size: 18px;
                    font-weight: 500;
                    cursor: pointer;
                    transition: background-color 0.3s ease;
                }

                .submit-button:hover {
                    background-color: rgb(34, 30, 92);
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
                <h1>MedicHub</h1>
                <nav>
                    <div class="header-back" >
						<a href = "AppointmentPage(doc).php?userID=<?php echo $_SESSION['userID'];?>">
						<img src ="https://cdn-icons-png.flaticon.com/512/93/93634.png" alt="Back">
						</a>
					</div>
                </nav>
            </header>
            <div class="container">
                <div class="appointment-card">
                    <h2>Updating Appointment</h2>
                    <form method="POST" action="updateApp.php">
                        <input type="hidden" name="appointmentID" value="<?php echo $appointmentID; ?>">
                        <input type="hidden" name="userType" value="<?php echo $userType; ?>">
                        <input type="hidden" name="userID" value="<?php echo $userID; ?>">
                        <label for="diagnosis">Diagnosis:</label>
                        <input type="text" id="diagnosis" name="diagnosis" value="<?php echo $appointment['diagnosis'];?>" required><br>
						<label for="curMed">Current Prescribed Medicine ID: </label>
						<input type="text" id="curMed" name="curMed" value="<?php echo htmlspecialchars($appointment['prescriptionID']); ?>" readonly>
                        <label for="prescriptionID">Update Medicine:</label>
                        <select id="prescriptionID" name="prescriptionID" required>
							<option value="" <?php if($appointment['prescriptionID'] == '-') echo 'selected'; ?>>Medicine Name</option>
                            <option value="PR0001" <?php if($appointment['prescriptionID'] == 'PR0001-Paracetamol') echo 'selected'; ?>>PR0001-Paracetamol</option>
                            <option value="PR0002" <?php if($appointment['prescriptionID'] == 'PR0002-Ibuprofen') echo 'selected'; ?>>PR0002-Ibuprofen</option>
                            <option value="PR0003" <?php if($appointment['prescriptionID'] == 'PR0003-Amoxicillin') echo 'selected'; ?>>PR0003-Amoxicillin</option>
                            <option value="PR0004" <?php if($appointment['prescriptionID'] == 'PR0004-Methotrexate') echo 'selected'; ?>>PR0004-Methotrexate</option>
                            <option value="PR0005" <?php if($appointment['prescriptionID'] == 'PR0005-Diuretics') echo 'selected'; ?>>PR0005-Diuretics</option>
                            <option value="PR0006" <?php if($appointment['prescriptionID'] == 'PR0006-Tramadol') echo 'selected'; ?>>PR0006-Tramadol</option>
                            <option value="PR0007" <?php if($appointment['prescriptionID'] == 'PR0007-Fentanyl') echo 'selected'; ?>>PR0007-Fentanyl</option>
                            <option value="PR0008" <?php if($appointment['prescriptionID'] == 'PR0008-Oxycodone') echo 'selected'; ?>>PR0008-Oxycodone</option>
                            <option value="PR0009" <?php if($appointment['prescriptionID'] == 'PR0009-Trimethoprim') echo 'selected'; ?>>PR0009-Trimethoprim</option>
                            <option value="PR0010" <?php if($appointment['prescriptionID'] == 'PR0010-Funderparinex') echo 'selected'; ?>>PR0010-Funderparinex</option>
                        </select>
                        <br>
                        <button type="submit" class="submit-button">Update Appointment</button>
                    </form>
                </div>
            </div>
			<script>
				function goBack() {
					window.location.href("homePageStaff.html?userType=doctor&userID=<?php echo $userID;?>");
				}
			</script>
        </body>
        </html>
        <?php
    } else {
        echo "Appointment not found.";
    }
}
?>
