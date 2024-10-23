<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../includes/db.php';

$sql = "SELECT * FROM products";
$products_result = $conn->query($sql);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log(print_r($_POST, true));
    $customer_name = $_POST['customer_name'];
    $customer_email = $_POST['customer_email'];
    $customer_phone = $_POST['customer_phone'];
    $customer_address = $_POST['customer_address'];
    $total = str_replace(',', '', $_POST['total']);

    $stmt = $conn->prepare("INSERT INTO invoices (customer_name, customer_email, customer_phone, customer_address, total, issue_date) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssssd", $customer_name, $customer_email, $customer_phone, $customer_address, $total);

    if ($stmt->execute()) {
        $invoice_id = $stmt->insert_id;

        $product_ids = $_POST['product_id'];
        $quantities = $_POST['quantity'];
        $prices = $_POST['price'];
        $discounts = $_POST['discount'];
        $subtotals = $_POST['subtotal'];


        foreach ($product_ids as $index => $product_id) {
            $quantity = $quantities[$index];
            $price = $prices[$index];
            $discount = $discounts[$index];
            $subtotal = str_replace(',', '', $subtotals[$index]);
            error_log("Inserting Item: Product ID: $product_id, Quantity: $quantity, Price: $price, Discount: $discount, Subtotal: $subtotal");
            $item_stmt = $conn->prepare("INSERT INTO invoice_items (invoice_id, product_id, quantity, price, discount, subtotal) VALUES (?, ?, ?, ?, ?, ?)");
            $item_stmt->bind_param("iiiddd", $invoice_id, $product_id, $quantity, $price, $discount, $subtotal);

            if (!$item_stmt->execute()) {
                error_log("Error inserting item: " . $item_stmt->error);
            }
        }


        header("Location: ../view_invoices.php");
        exit();
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .section {
            margin-bottom: 20px;
        }

        input[type="text"],
        input[type="email"],
        input[type="number"],
        input[type="tel"],
        textarea {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 15px;
            margin-left: 10px;
            margin-right: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #333;
            color: #fff;
        }

        td input,
        td select {
            width: 100%;
            padding: 5px;
            margin: 0;
            border: 1px solid #ccc;
            border-radius: 3px;
            box-sizing: border-box;
        }

        button {
            padding: 10px;
            margin: 10px 0;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #218838;
        }

        .add-remove-btns {
            display: flex;
            justify-content: center;
            margin-top: 10px;
        }

        .add-remove-btns button {
            margin: 0 5px;
        }

        #productTable td button[type="button"] {
            background-color: #fff;
            padding: 3px 13px;
        }

        .productSelect {
            padding: 10px 13px;
            margin-bottom: 8px;
        }
    </style>
</head>

<body>

    <div class="container">
        <h1>Create Receipt</h1>

        <form method="POST" action="">
            <div class="section">
                <input type="text" id="customer_name" name="customer_name" placeholder="Customer Name" required>
                <input type="email" id="customer_email" name="customer_email" placeholder="Customer Email" required>
                <input type="tel" id="customer_phone" name="customer_phone" placeholder="Phone Number" required>
                <textarea id="customer_address" name="customer_address" rows="4" placeholder="Address" required></textarea>
            </div>

            <div class="section">
                <table id="productTable">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Discount (%)</th>
                            <th>Subtotal</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <select class="productSelect" name="product_id[]" onchange="updateProductDetails(this)" required>
                                    <option value="">Select a product</option>
                                    <?php while ($row = $products_result->fetch_assoc()): ?>
                                        <option value="<?php echo $row['id']; ?>" data-price="<?php echo htmlspecialchars($row['price']); ?>">
                                            <?php echo htmlspecialchars($row['name']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </td>
                            <td>
                                <input type="number" class="quantity" name="quantity[]" min="1" value="1" placeholder="Qty" required oninput="calculateSubtotal(this)">
                            </td>
                            <td>
                                <input type="number" class="price" name="price[]" step="0.01" placeholder="Price (₦)" required readonly>
                            </td>
                            <td>
                                <input type="number" class="discount" name="discount[]" step="0.01" placeholder="Discount" oninput="calculateSubtotal(this)">
                            </td>
                            <td>
                                <input type="text" class="subtotal" name="subtotal[]" placeholder="Subtotal" readonly>
                            </td>
                            <td>
                                <button type="button" onclick="removeProductRow(this)">
                                    <img src="/project/icons/icons-remove.png" alt="" style="width: 25px; height: 25px; ">
                                </button>
                            </td>
                        </tr>
                    </tbody>

                </table>
                <div class="section">
                    <label for="total">Total Amount (₦):</label>
                    <input type="text" id="total" name="total" placeholder="Total" readonly>
                </div>

                <div class="add-remove-btns">
                    <button type="button" onclick="addProductRow()">+ Add Product</button>
                </div>
            </div>

            <button type="submit">Done</button>
        </form>
    </div>

    <div id="productOptionsTemplate" style="display:none;">
        <?php
        $products_result = $conn->query($sql);
        while ($row = $products_result->fetch_assoc()): ?>
            <option data-price="<?php echo htmlspecialchars($row['price']); ?>" value="<?php echo htmlspecialchars($row['id']); ?>">
                <?php echo htmlspecialchars($row['name']); ?>
            </option>
        <?php endwhile; ?>
    </div>


    <script>



   




       

       
    </script>
</body>

</html>