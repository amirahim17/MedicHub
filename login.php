<?php
session_start();
include 'dbconn.php'; // Ensure this file exists and is correct
$pdo = $conn;

if (!$pdo) {
    die('Could not connect to the database.');
}

// Get username and password from form
$userID = $_POST['userID'];
$password = $_POST['password'];
$role = $_POST['role'];

// Prepare statement based on the role
if ($role === 'staff') {
    $fetchRole = $pdo->prepare("SELECT * FROM login l INNER JOIN usertype ut ON l.userTypeID = ut.userTypeID WHERE l.userID =? AND ut.userType IN ('admin', 'doctor')");
} else {
    $fetchRole = $pdo->prepare("SELECT * FROM login l INNER JOIN usertype ut ON l.userTypeID = ut.userTypeID WHERE l.userID =? AND ut.userType = 'patient'");
}
$fetchRole->execute([$userID]);

// Check if user exists
if ($fetchRole->rowCount() == 1) {
    // User exists, fetch the user data
    $user = $fetchRole->fetch();
    
    // Verify the password based on the role
    if ($role === 'staff') {
        // For staff, the password is not hashed
        if ($password === $user['userPassword']) {
            $_SESSION['loggedin'] = true;
            $_SESSION['userID'] = $userID;
            $_SESSION['userType'] = $user['userType'];

            // Set specific ID variable based on user type
            if (strpos($userID, 'ADM') === 0) {
                $_SESSION['adminID'] = $userID;
            } elseif (strpos($userID, 'D') === 0) {
                $_SESSION['doctorID'] = $userID;
            } else {
                // Invalid staff ID, redirect back with error
                header("Location: mainPageForm.html?error=1");
                exit();
            }

            // Redirect based on user type
            if (isset($_SESSION['adminID'])) {
                header("Location: homePageAdmin.html?userType=admin&adminID=" . $_SESSION['adminID'] . "&userID=" . $_SESSION['userID']); // Redirect to admin dashboard
            } elseif (isset($_SESSION['doctorID'])) {
                header("Location: homePageStaff.html?userType=doctor&doctorID=" . $_SESSION['doctorID'] . "&userID=" . $_SESSION['userID']); // Redirect to staff dashboard
            }
        } else {
            // Incorrect password for staff
            ?>
            <script>
                alert("Incorrect password");
                window.location.href = "mainPageForm.html?error=1";
            </script>
            <?php
            exit();
        }
    } else {
        // For patients, the password is hashed
        if (password_verify($password, $user['userPassword'])) {
            $_SESSION['loggedin'] = true;
            $_SESSION['userID'] = $userID;
            $_SESSION['userType'] = $user['userType'];
            $_SESSION['patientID'] = $userID;

            // Redirect to patient dashboard
            header("Location: patientDashboard.html?userType=patient&patientID=" . $_SESSION['patientID'] . "&userID=" . $_SESSION['userID']); // Redirect to patient dashboard
        } else {
            // Incorrect password for patient
            ?>
            <script>
                alert("Incorrect password");
                window.location.href = "mainPageForm.html?error=1";
            </script>
            <?php
            exit();
        }
    }
} else {
    // User does not exist, redirect to login page with error message
    ?>
    <script>
        alert("User does not exist");
        window.location.href = "mainPageForm.html?error=1";
    </script>
    <?php
    exit();
}
?>
