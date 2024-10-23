<?php
include 'includes/db.php';

$settings_sql = "SELECT * FROM settings WHERE id = 1";
$settings_result = $conn->query($settings_sql);
$settings = $settings_result->fetch_assoc();

$total_invoices_sql = "SELECT COUNT(*) AS total_invoices FROM invoices";
$total_invoices_result = $conn->query($total_invoices_sql);
$total_invoices = $total_invoices_result->fetch_assoc()['total_invoices'];

$total_products_sql = "SELECT COUNT(*) AS total_products FROM products";
$total_products_result = $conn->query($total_products_sql);
$total_products = $total_products_result->fetch_assoc()['total_products'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Invoice Management System</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>

    <div class="sidebar">
        <h2><a href="index.php" class="dash">Dashboard</a></h2>
        <ul>
            <li><a href="#" onclick="toggleDropdown('invoiceDropdown')">Receipt</a>
                <ul id="invoiceDropdown" style="display:none;">
                    <li><a href="#" onclick="loadPage('invoices/create_invoice.php')">Create Receipt</a></li>
                    <li><a href="#" onclick="loadPage('invoices/view_invoices.php')">View Receipt</a></li>
                </ul>
            </li>
            <li><a href="#" onclick="toggleDropdown('productDropdown')">Products</a>
                <ul id="productDropdown" style="display:none;">
                    <li><a href="products/add_product.php">Add Product</a></li>
                    <li><a href="products/view_products.php">View Products</a></li>
                </ul>
            </li>
            <li><a href="settings.php">Settings</a></li>
        </ul>
    </div>



    <div class="content">
        <div class="header">
            <img src="<?php echo $settings['company_logo']; ?>" alt="Company Logo" onerror="this.onerror=null; this.src='image.png';">
            <h1><?php echo $settings['company_name']; ?></h1>
        </div>

        <div id="pageContent">
            <h2>Dashboard</h2>
            <div class="dashboard">
                <div class="dashboard-card">
                    <h3>Total Receipt</h3>
                    <p><?php echo $total_invoices; ?></p>
                </div>
                <div class="dashboard-card">
                    <h3>Total Products</h3>
                    <p><?php echo $total_products; ?></p>
                </div>
            </div>
        </div>
    </div>

    <script src="scripts.js"></script>

</body>

</html>