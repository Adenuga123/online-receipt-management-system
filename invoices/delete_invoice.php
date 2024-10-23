<?php
include '../includes/db.php'; 

if (isset($_GET['invoice_id'])) {
    $invoice_id = intval($_GET['invoice_id']);

    $delete_items_sql = "DELETE FROM invoice_items WHERE invoice_id = ?";
    $delete_invoice_sql = "DELETE FROM invoices WHERE invoice_id = ?";

    $delete_items_stmt = $conn->prepare($delete_items_sql);
    $delete_items_stmt->bind_param("i", $invoice_id);
    $delete_items_stmt->execute();

    $delete_invoice_stmt = $conn->prepare($delete_invoice_sql);
    $delete_invoice_stmt->bind_param("i", $invoice_id);
    $delete_invoice_stmt->execute();

    if ($delete_invoice_stmt->affected_rows > 0) {
        echo "<script>alert('Invoice deleted successfully!'); window.location.href='view_invoices.php';</script>";
    } else {
        echo "<script>alert('Error: Invoice not found or could not be deleted.'); window.location.href='view_invoices.php';</script>";
    }

    $delete_items_stmt->close();
    $delete_invoice_stmt->close();
} else {
    echo "<script>alert('No invoice ID provided.'); window.location.href='view_invoices.php';</script>";
}

$conn->close();
?>
