<?php
require_once 'dbconnection.php'; // Ensure database connection
require_once 'tcpdf/tcpdf.php'; // Include TCPDF

// Fetch analytics data
try {
    $totalOrders = $conn->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    $totalRevenue = $conn->query("SELECT SUM(total_price) FROM orders")->fetchColumn() ?: 0;

    $orderTrends = $conn->query("SELECT DATE(order_date) as date, COUNT(*) as count FROM orders GROUP BY DATE(order_date) ORDER BY date ASC")->fetchAll(PDO::FETCH_ASSOC);
    $stockLevels = $conn->query("SELECT product_name, stock FROM items")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Query failed: " . $e->getMessage());
}

// Create a new PDF document
$pdf = new TCPDF();
$pdf->SetTitle('Analytics Report');
$pdf->SetAuthor('Admin Panel');
$pdf->SetMargins(10, 10, 10);
$pdf->AddPage();

// Title
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'Analytics Report', 0, 1, 'C');
$pdf->Ln(5);

// Total Orders and Revenue
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 10, "Total Orders: $totalOrders", 0, 1);
$pdf->Cell(0, 10, "Total Revenue: $".number_format($totalRevenue, 2), 0, 1);
$pdf->Ln(5);

// Order Trends Table
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, 'Order Trends', 0, 1);
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(50, 8, 'Date', 1);
$pdf->Cell(50, 8, 'Total Orders', 1);
$pdf->Ln();

foreach ($orderTrends as $trend) {
    $pdf->Cell(50, 8, $trend['date'], 1);
    $pdf->Cell(50, 8, $trend['count'], 1);
    $pdf->Ln();
}
$pdf->Ln(5);

// Stock Levels Table
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, 'Stock Levels', 0, 1);
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(70, 8, 'Product', 1);
$pdf->Cell(30, 8, 'Stock', 1);
$pdf->Ln();

foreach ($stockLevels as $item) {
    $pdf->Cell(70, 8, $item['product_name'], 1);
    $pdf->Cell(30, 8, $item['stock'], 1);
    $pdf->Ln();
}

// Output PDF
$pdf->Output('analytics_report.pdf', 'D');
?>
