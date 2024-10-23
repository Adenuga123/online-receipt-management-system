<?php
include '../includes/db.php';

$sql = "SELECT * FROM invoices ORDER BY invoice_id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Invoices</title>
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

        table {
            width: 100%;
            border-collapse: collapse;
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

        td a {
            text-decoration: none;
            color: blue;
        }


        
    </style>
</head>

<body>

    <div class="container">
        <a href="/project/index.php" class="back-btn">
            <img src="/project/icons/icons-back.png " alt="Bck" style="width: 30px; height: 30px;">
        </a>
        <h1>Receipts</h1>
        <table>
            <thead>
                <tr>
                    <th>Invoice ID</th>
                    <th>Customer Name</th>
                    <th>Total Amount</th>
                    <th>Issue Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['invoice_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                            <td>₦<?php echo number_format($row['total'], 2); ?></td>
                            <td><?php echo htmlspecialchars($row['issue_date']); ?></td>
                            <td>
                                <a href="/project/invoices/view_invoice_details.php?invoice_id=<?php echo $row['invoice_id']; ?>" class="view-btn">
                                    <img src="/project/icons/icons-view.png" alt="View" style="width: 25px; height: 25px;">
                                </a> |
                                <a href="/project/invoices/delete_invoice.php?invoice_id=<?php echo $row['invoice_id']; ?>"
                                    onclick="return confirm('Are you sure you want to delete this invoice?');"
                                    class="dlt-btn">
                                    <img src="/project/icons/icons-remove.png" alt="Delete" style="width: 25px; height: 25px;">
                                </a>
                            </td>


                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No invoices found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

    </div>

</body>

</html>

<?php $conn->close(); ?>