<?php
session_start(); 

include 'dbconn.php';

// Check if the user ID is set in the session
if (!isset($_SESSION['userID'])) {
    die("User not logged in.");
}

$doctorID = $_SESSION['userID'];

try {
    $sql = "SELECT 
        medSerialNumber,
        medName,
        mfgDate,
        expDate,
        quantity,
        medFactory
    FROM 
        medication";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($result === false) {
        throw new Exception("Error getting result set: " . $stmt->errorInfo[2]);
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
    <title>Medicine Inventory | Doctor </title>
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
            padding: 0px;
        }

        header {
            background: #0D4458;
            color: #fff;
            padding: 20px;
            text-align: center;
            position: relative; /* Ensure relative positioning for the header */
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
            color: #4158d0;
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
    </style>
</head>
<body>
<input type="hidden" id="userID" value="<?php echo htmlspecialchars($_SESSION['userID']); ?>">
    <header>
        <div class="header-back" onclick="window.location.href='homePageStaff.html?userType=doctor&userID=<?php echo $_SESSION['userID'];?>'">
            <img src="https://cdn-icons-png.flaticon.com/512/93/93634.png" alt="Back">
        </div>
        <h1>MedicHub | Doctor </h1>
        <nav>
        </nav>
    </header>

    <div class="container">
        <div id="appointment" class="clinic-appointment">
            <h2>Medicine Inventory</h2>
            <table class="transparent-table">
                <thead>
                    <tr>
                        <th>SERIAL NUMBER</th>
                        <th>NAME</th>
                        <th>MFG. DATE</th>
                        <th>EXP</th>
                        <th>QUANTITY</th>
                        <th>MFG. FACTORY</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result && count($result) > 0) {
                        foreach ($result as $row) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['medSerialNumber']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['medName']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['mfgDate']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['expDate']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['medFactory']) . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No records found</td></tr>";
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
