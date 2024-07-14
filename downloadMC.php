<?php
require_once('./fpdf/fpdf.php');

session_start();
if( !isset($_SESSION['mcSerialNumber'])) {
    echo "Session variables not set!";
  exit;
}
include("dbconn.php");
$pdo = $conn;

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Fetch data from the database
$stmt = $pdo->prepare("SELECT * FROM medicalcertificate  WHERE m.mcSerialNumber");
$stmt->execute([$mcSerialNumber]); // Update condition as needed
$data = $stmt->fetch(PDO::FETCH_ASSOC);

// Create new PDF instance
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('MedicHubSystem');
$pdf->SetTitle('Medical Certificate');
$pdf->SetSubject('Medical Certificate');
$pdf->SetKeywords('Medical Certificate, FPDF, PHP');

// Set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' Medical Certificate', PDF_HEADER_STRING);

// Set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// Set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Add a page
$pdf->AddPage();

// Set some content to display
$html = '<div class="certificate">
            <h2>Medical Certificate</h2>
            <p><strong>Medical Certificate ID:</strong> '.htmlspecialchars($data['newMcSerialNumber']).'</p>
            <p><strong>Appointment ID:</strong> '.htmlspecialchars($data['appointmentID']).'</p>
            <p><strong>Reason:</strong> '.htmlspecialchars($data['diagnosis']).'</p>
            <p><strong>Patient Name:</strong> '.htmlspecialchars($data['patientName']).'</p>
            <p><strong>Patient NRIC:</strong> '.htmlspecialchars($data['patientNRIC']).'</p>
            <p><strong>Doctor Name:</strong> '.htmlspecialchars($data['doctorName']).'</p>
            <p><strong>Date:</strong> '.htmlspecialchars($data['mcDate']).'</p>
            <p><strong>Duration (days):</strong> '.htmlspecialchars($data['duration']).'</p>
        </div>';

// Output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// Close and output PDF document
$pdf->Output('medical_certificate.pdf', 'D');
?>