<?php
session_start(); // Ensure this is at the very top before any output

include 'dbconn.php';

// Check if the user ID is set in the session
if (!isset($_SESSION['userID'])) {
    die("User not logged in.");
}

$patientID = $_SESSION['userID'];
$adminID = $_SESSION['adminID'];
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
    <title>Medicine Inventory | Admin </title>
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
            background:#0D4458;
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
            color: #4158d0;
            border: none;
            padding-left: 0;
            margin-top: -10px;
            font-size: 20px;
            font-weight: 500;
            cursor: pointer;
            background: #6c63ff
            transition: all 0.3s ease;
            margin-left: 10px;
        }
		.field button:hover{
			background-color: rgb(34, 30, 92);
		}

        .quantity-btn {
            display: flex;
            align-items: center;
        }

        .quantity-btn button {
            background-color: #0D4458;
            color: #fff;
            border: none;
            padding: 5px 10px;
            margin: 0 5px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .quantity-btn button:hover {
            background-color: #3448b7;
        }

        .quantity-input {
            width: 50px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 5px;
            margin: 0 5px;
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
		.add-Med{
			position: absolute;
            top: 50%;
            right: 20px;
            transform: translateY(-50%);
            cursor: pointer;
		}
		.add-Med img{
			width: 50px;
            height: 50px;
            filter: invert(100%);
		}
		.add-Med img:hover{
			filter: brightness(50%);
		}
		
    </style>
</head>
<body>
<input type="hidden" id="userID" value="<?php echo htmlspecialchars($_SESSION['userID']); ?>">
    <header>
        <h1>MedicHub | Admin </h1>  
        <div class="header-back" onclick="window.location.href='homePageAdmin.html'">
            <img src ="https://cdn-icons-png.flaticon.com/512/93/93634.png" alt="Back">
        </div>
		<div class = "add-Med" onclick="window.location.href='medicineRegistration.html'">
		<img src="https://cdn-icons-png.freepik.com/512/114/114888.png" alt="ADD MEDICINE">
		</div>
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
                        <th>EDIT QUANTITY</th>
						<th>ACTION</th>
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
                            echo "<td class='quantity-btn'>
                                    <button onclick='updateQty(\"" . $row['medSerialNumber'] . "\", -1)'>-</button>
                                    <input type='text' class='quantity-input' id='quantity_" . $row['medSerialNumber'] . "' value='" . htmlspecialchars($row['quantity']) . "' readonly>
                                    <button onclick='updateQty(\"" . $row['medSerialNumber'] . "\", 1)'>+</button>
                                  </td>";
							echo "<td class = 'deletebtn'>
									<button onclick='deleteMedicine(\"" . $row['medSerialNumber'] . "\")'>Delete</button>
								</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>No records found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        
    </div>
    
    <script>
		function goBack() {
            window.history.back();
        }

        function updateQty(serialNumber, change) {
            const inputElement = document.getElementById('quantity_' + serialNumber);
            let currentQuantity = parseInt(inputElement.value);

            if (currentQuantity + change >= 0) {
                fetch('updateQty.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `serialNumber=${serialNumber}&change=${change}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        inputElement.value = currentQuantity + change;
                    } else {
                        console.error('Failed to update quantity:', data.error);
                        alert('Failed to update quantity: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            } else {
                console.warn('Quantity cannot be negative:', currentQuantity + change);
            }
        }
		function deleteMedicine(serialNumber) {
			if (confirm("Are you sure you want to delete this medicine?")) {
				fetch('deleteMed.php', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/x-www-form-urlencoded'
					},
					body: `serialNumber=${serialNumber}`
				})
				.then(response => response.json())
				.then(data => {
					if (data.success) {
						document.location.reload();
					} else {
						console.error('Failed to delete medicine:', data.error);
						alert('Failed to delete medicine: ' + data.error);
					}
				})
				.catch(error => {
					console.error('Error:', error);
				});
			}
		}
    </script>
</body>
</html>
