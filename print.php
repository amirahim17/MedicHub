<?php include 'actions.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to MedicHub</title>
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,600,700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        /* Your CSS styles here */
    </style>
</head>
<body>
    <header>
        <!-- Your header content here -->
    </header>

    <div class="container">
        <div id="appointment" class="clinic-appointment">
            <!-- Your appointment content here -->
        </div>
		
		<div class="field">
            <form method="GET">
                <input type="hidden" name="action" value="download_pdf">
                <input type="submit" value="Download As PDF">
            </form>

            <form method="GET">
                <input type="hidden" name="action" value="print">
                <input type="submit" value="Print">
            </form>
        </div>
		
    </div>
</body>
</html>
