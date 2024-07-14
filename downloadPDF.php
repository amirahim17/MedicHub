<?php
if(isset($_GET['action'])) {
    if($_GET['action'] == 'download_pdf') {
        // Code to generate and download PDF
        // For example, you can use a library like TCPDF or FPDF
        // Here, we're redirecting to a PHP file that generates PDF
        header('Location: generate_pdf.php');
        exit;
    } elseif($_GET['action'] == 'print') {
        // Code to handle printing
        // For example, you can use JavaScript to trigger print dialog
        echo "<script>window.print();</script>";
    }
}
?>