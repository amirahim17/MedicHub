<?php
session_start();
if (!isset($_SESSION['userID']) || !isset($_SESSION['userType'])) {
    echo "Session variables not set!";
    exit;
}

include("dbconn.php");
$pdo = $conn;

// Get admin ID from session
$adminID = $_SESSION['userID'];

// Function to get doctor information based on admin ID
function getDoctorInfo($pdo, $adminID) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM doctor d");
        $stmt->execute();
        $doctors = $stmt->fetchAll();
        return $doctors;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage() . "\n"; // Debugging statement
        return false;
    }
}

$doctors = getDoctorInfo($pdo, $adminID);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Profile List | Admin</title>
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
		
        .doctor-list {
            background: #0D4458;
            color: #fff;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }

        .doctor h2 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .doctor p {
            font-size: 16px;
            line-height: 1.6;
        }

        .btn-container {
            text-align: center;
            margin-top: 20px;
        }

        .btn {
            background-color: #6c63ff;
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
            background-color: rgb(34, 30, 92);
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


    </style>
</head>
<body>
<input type="hidden" id="userID" value="<?php echo $_SESSION['userID']; ?>">
<header>
    <h1>MedicHub Doctor's</h1>
    <nav>
        <div class="header-back" onclick="window.location.href='homePageAdmin.html'">
            <img src="https://cdn-icons-png.flaticon.com/512/93/93634.png" alt="Back">
        </div>
    </nav>
</header>

<?php
if ($doctors) {
?>
<div class="container">
    <div id="doctor" class="doctor-list">
        <h2>Doctor Profile List</h2>
        <table class="transparent-table">
            <thead>
                <tr>
                    <th>Doctor ID</th>
                    <th>Doctor Name</th>
                    <th>Doctor NRIC</th>
                    <th>Doctor Speciality</th>
                    <th>Availability</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($doctors as $row) { ?>
                <tr>
                    <td><?php echo $row["doctorID"]; ?></td>
                    <td><?php echo $row["doctorName"]; ?></td>
                    <td><?php echo $row["doctorNRIC"]; ?></td>
                    <td><?php echo $row["doctorSpeciality"]; ?></td>
                    <td><?php echo $row["availability"]; ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<?php
}
?>
<script>
    function goBack() {
        window.history.back();
    }
</script>
</body>
</html>
