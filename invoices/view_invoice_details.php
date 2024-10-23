<?php
include '../includes/db.php';

$settings_sql = "SELECT company_name FROM settings WHERE id = 1";
$settings_result = $conn->query($settings_sql);
$settings = $settings_result->fetch_assoc();
$company_name = $settings['company_name'] ?? 'Default Company';

$invoice_id = $_GET['invoice_id'];

$invoice_sql = "SELECT * FROM invoices WHERE invoice_id = ?";
$invoice_stmt = $conn->prepare($invoice_sql);
if ($invoice_stmt === false) {
    die("Error preparing invoice statement: " . $conn->error);
}
$invoice_stmt->bind_param('i', $invoice_id);
$invoice_stmt->execute();
$invoice_result = $invoice_stmt->get_result();
$invoice = $invoice_result->fetch_assoc();

$items_sql = "
    SELECT ii.*, p.name AS product_name 
    FROM invoice_items ii 
    JOIN products p ON ii.product_id = p.id 
    WHERE ii.invoice_id = ?";
$items_stmt = $conn->prepare($items_sql);
if ($items_stmt === false) {
    die("Error preparing items statement: " . $conn->error);
}
$items_stmt->bind_param('i', $invoice_id);
$items_stmt->execute();
$items_result = $items_stmt->get_result();

if ($items_result->num_rows === 0) {
    echo "<p>No products found for this invoice.</p>"; 
} else {
    echo "<p>Products found: " . $items_result->num_rows . "</p>";
}


$email_body = "Dear " . $invoice['customer_name'] . ",\n\nHere are your receipt details:\n\n";
$email_body .= "Name: " . $invoice['customer_name'] . "\n";
$email_body .= "Email: " . $invoice['customer_email'] . "\n";
$email_body .= "Phone: " . $invoice['customer_phone'] . "\n";
$email_body .= "Address: " . $invoice['customer_address'] . "\n";
$email_body .= "Issue Date: " . $invoice['issue_date'] . "\n";
$email_body .= "Total: ₦" . number_format($invoice['total'], 2) . "\n\n";
$email_body .= "Purchased Products:\n";

while ($item = $items_result->fetch_assoc()) {
    $email_body .= "Product: " . $item['product_name'] . "\n";
    $email_body .= "Quantity: " . $item['quantity'] . "\n";
    $email_body .= "Price: ₦" . number_format($item['price'], 2) . "\n";
    $email_body .= "Discount: " . $item['discount'] . "%\n";
    $email_body .= "Subtotal: ₦" . number_format($item['subtotal'], 2) . "\n\n";
}

$email_body .= "Thank you for your business!";

$mailto_link = "mailto:" . $invoice['customer_email'] . "?subject=Your%20Receipt%20from%20" . rawurlencode($company_name) . "&body=" . rawurlencode($email_body);

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 13px;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
            padding: 10px;
        }

        th {
            background-color: #333;
            color: #fff;
        }

        td {
            text-align: left;
        }

        h1 {
            text-align: center;
        }
        .send-btn{
            background-color: #28a745;
            text-decoration: none;
            color: white;
            padding: 5px 10px;
            border-radius: 3px;
        }

    </style>
</head>

<body>

    <div class="container">
        <a href="view_invoices.php" class="back-btn">
            <img src="/project/icons/icons-back.png " alt="Bck" style="width: 30px; height: 30px;">
        </a>
        <h1>Receipt Details</h1>

        <h3>Customer Information</h3>
        <p><strong>Name:</strong> <?php echo $invoice['customer_name']; ?></p>
        <p><strong>Email:</strong> <?php echo $invoice['customer_email']; ?></p>
        <p><strong>Phone:</strong> <?php echo $invoice['customer_phone']; ?></p>
        <p><strong>Address:</strong> <?php echo $invoice['customer_address']; ?></p>
        <p><strong>Issue Date:</strong> <?php echo $invoice['issue_date']; ?></p>
        <p><strong>Total:</strong> ₦<?php echo number_format($invoice['total'], 2); ?></p>

        <h3>Products</h3>
        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Discount (%)</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $items_result->data_seek(0);

                while ($item = $items_result->fetch_assoc()):
                ?>
                    <tr>
                        <td><?php echo $item['product_name'] ?? 'Unknown Product'; ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>₦<?php echo number_format($item['price'], 2); ?></td>
                        <td><?php echo $item['discount']; ?>%</td>
                        <td>₦<?php echo number_format($item['subtotal'], 2); ?></td>
                    </tr>
                <?php endwhile; ?>

            </tbody>
        </table>


        <a href="<?php echo $mailto_link; ?>" class="send-btn">Send Receipt via Email</a>
    </div>
</body>


</html>

<?php
$invoice_stmt->close();
$items_stmt->close();
$conn->close();
?>