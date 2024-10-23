<?php
include '../includes/db.php';

$sql = "SELECT * FROM products";
$result = $conn->query($sql);

if ($result === false) {
    echo "Error: " . $conn->error; 
    exit();
}

if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) {
        echo "<script>alert('Product deleted successfully!'); window.location.href='view_products.php';</script>";
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
    <title>View Products</title>
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

        button {
            border: none;
            background-color: #fff;
        }
    </style>
</head>

<body>

    <div class="container">
    <a href="../index.php">
        <img src="/project/icons/icons-cancel.png" alt="back" style="width: 30px; height: 30px;">
    </a>
        <h1>View Products</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Price (₦)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['name']; ?></td>
                            <td>₦<?php echo number_format($row['price'], 2); ?></td>
                            <td>
                                <button class="edit_btn">
                                    <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="edit_text">
                                        <img src="/project/icons/icons-edit.png" alt="Edit" style="width: 25px; height: 25px;">
                                    </a>
                                </button>
                                <button class="dlt_btn">
                                    <a href="view_products.php?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this product?');" class="dlt_text">
                                        <img src="/project/icons/icons-remove.png" alt="Delete" style="width: 25px; height: 25px;">
                                    </a>
                                </button>
                            </td>

                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No products found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

    </div>

</body>

</html>