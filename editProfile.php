<?php
session_start();
if (!isset($_SESSION['userID']) ||!isset($_SESSION['userType'])) {
    echo "Session variables not set!";
    exit;
}

include("dbconn.php");
$pdo = $conn;

$userType = $_GET['userType'];
$userID = $_GET['userID'];
function getPatientInfo($pdo, $patientID) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM patient WHERE patientID =?");
        $stmt->bindParam(1, $patientID, PDO::PARAM_STR);
        $stmt->execute();
        $patient = $stmt->fetch();
        if ($patient) {
            return $patient;
        } else {
            echo "Patient with ID $patientID not found in the database!\n";
            return false;
        }
    } catch (PDOException $e) {
        echo "Error: ". $e->getMessage(). "\n";
        return false;
    }
	$patientID = $_SESSION['userID'];
}

function getDoctorInfo($pdo, $doctorID) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM doctor WHERE doctorID =?");
        $stmt->bindParam(1, $doctorID, PDO::PARAM_STR);
        $stmt->execute();
        $doctor = $stmt->fetch();
        if ($doctor) {
            return $doctor;
        } else {
            echo "Doctor with ID $doctorID not found in the database!\n";
            return false;
        }
    } catch (PDOException $e) {
        echo "Error: ". $e->getMessage(). "\n";
        return false;
    }
	$doctorID = $_SESSION['userID'];
}
if ($userType === 'doctor') {
    $doctor = getDoctorInfo($pdo, $userID);
    if ($doctor) {
       ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
			<link href="https://fonts.googleapis.com/css?family=Poppins:400,500,600,700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
            <title>Edit Doctor Profile</title>
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
                    min-height: 100vh;
                }
                header {
                    background:#0D4458;
                    color: #fff;
                    padding: 20px 0;
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
                nav ul li.login-btn {
                    background-color: transparent;
                    border: none;
                    cursor: pointer;
                }
                nav ul li.login-btn i {
                    font-size: 20px;
                    color: #fff;
                    transition: color 0.3s ease;
                }
                nav ul li.login-btn i:hover {
                    color: rgba(255, 255, 255, 0.7);
                }
               .profile-card {
                    background-color: #fff;
                    color: #262626;
                    border-radius: 10px;
                    padding: 20px;
                    width: 400px;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                    margin-top: 50px;
                    text-align: left;
                }
               .profile-card h2 {
                    margin-bottom: 20px;
                    font-size: 24px;
                    font-weight: 600;
                }
               .profile-card form {
                    display: flex;
                    flex-direction: column;
                }
               .profile-card form label {
                    margin: 10px 0 5px;
                }
               .profile-card form input {
                    margin: 5px 0 10px;
                    padding: 10px;
                    border: 1px solid #ccc;
                    border-radius: 5px;
                }
               .profile-card form.submit-button {
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
               .profile-card form.submit-button:hover {
                    background-color: #3448b7;
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
				<div class="header-back" onclick="window.location.href='profile.php?userType=doctor&userID=<?php echo $_SESSION['userID'];?>'">
					<img src="https://cdn-icons-png.flaticon.com/512/93/93634.png" alt="Back">
				</div>
				</nav>
            </header>
            <div class="profile-card">
                <h2>Edit Doctor Profile</h2>
                <form method="POST" action="update.php">
                    <input type="hidden" name="userType" value="doctor">                  
          <input type="hidden" name="userID" value="<?php echo $userID; ?>">
                    <label for ="doctorName">Name:</label>
                    <input type="text" id="doctorName" name="doctorName" value="<?php echo $doctor['doctorName']; ?>" required>
                    <label for="doctorNRIC">NRIC:</label>
                    <input type="text" id="doctorNRIC" name="doctorNRIC" value="<?php echo $doctor['doctorNRIC']; ?>" required>
                    <label for="doctorSpeciality">Speciality:</label>
                    <input type="text" id="doctorSpeciality" name="doctorSpeciality" value="<?php echo $doctor['doctorSpeciality']; ?>" required>
                    <label for="availability">Availability:</label>
					  <select id="availability" name="availability"> 
						<option value="Available">Available</option>
						<option value="Not Available">Not Available</option>
					  </select>
                    <button type="submit" class="submit-button">SUBMIT</button>
                </form>
            </div>
			<script>
			function goBack(){
				window.history.back();
			}
			</script>
        </body>
    </html>
    <?php
    } else {
        echo "Doctor not found!";
    }
} elseif ($userType === 'patient') {
    $patient = getPatientInfo($pdo, $userID);
    if ($patient) {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
			<link href="https://fonts.googleapis.com/css?family=Poppins:400,500,600,700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
            <title>Edit Patient Profile</title>
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
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    min-height: 100vh;
                }
                header {
                    background: #0D4458;
                    color: #fff;
                    padding: 20px 0;
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
                    padding: 
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
                nav ul li.login-btn {
                    background-color: transparent;
                    border: none;
                    cursor: pointer;
                }
                nav ul li.login-btn i {
                    font-size: 20px;
                    color: #fff;
                    transition: color 0.3s ease;
                }
                nav ul li.login-btn i:hover {
                    color: rgba(255, 255, 255, 0.7);
                }
               .profile-card {
                    background-color: #fff;
                    color: #262626;
                    border-radius: 10px;
                    padding: 20px;
                    width: 400px;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                    margin-top: 50px;
                    text-align: left;
                }
               .profile-card h2 {
                    margin-bottom: 20px;
                    font-size: 24px;
                    font-weight: 600;
                }
               .profile-card form {
                    display: flex;
                    flex-direction: column;
                }
               .profile-card form label {
                    margin: 10px 0 5px;
                }
               .profile-card form input {
                    margin: 5px 0 10px;
                    padding: 10px;
                    border: 1px solid #ccc;
                    border-radius: 5px;
                }
               .profile-card form.submit-button {
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
               .profile-card form.submit-button:hover {
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
                <h1>MedicHub | Profile </h1>
                <nav>
					<div class="header-back" onclick="window.location.href='profile.php?userType=patient&userID=<?php echo $_SESSION['userID'];?>'">
						<img src="https://cdn-icons-png.flaticon.com/512/93/93634.png" alt="Back">
					</div>
                </nav>
            </header>
            <div class="profile-card">
                <h2>Edit Patient Profile</h2>
                <form method="POST" action="update.php">
                    <input type="hidden" name="userType" value="patient">
                    <input type="hidden" name="userID" value="<?php echo $userID; ?>">
                    <label for="patientName">Name:</label>
                    <input type="text" id="patientName" name="patientName" value="<?php echo $patient['patientName']; ?>" required>
                    <label for="patientNRIC">NRIC:</label>
                    <input type="text" id="patientNRIC" name="patientNRIC" value="<?php echo $patient['patientNRIC']; ?>" required>
                    <label for="patientAddress">Address:</label>
                    <input type="text" id="patientAddress" name="patientAddress" value="<?php echo $patient['patientAddress']; ?>" required>
                    <button type="submit" class="submit-button">SUBMIT</button>
                </form>
            </div>
			<script>
			function goBack(){
				window.history.back();
			}
			</script>
        </body>
    </html>
    <?php
    } else {
        echo "Patient not found!";
    }
} else {
    echo "Invalid user type!";
}
?>