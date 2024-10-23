<?php
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $company_name = $_POST['company_name'];

    if (isset($_FILES['company_logo']) && $_FILES['company_logo']['error'] === 0) {
        $file_tmp = $_FILES['company_logo']['tmp_name'];
        $file_name = basename($_FILES['company_logo']['name']);
        $target_dir = 'uploads/';
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($file_tmp, $target_file)) {
            $sql = "UPDATE settings SET company_name = ?, company_logo = ? WHERE id = 1";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $company_name, $target_file);
        } else {
            echo "<script>alert('Failed to upload logo.');</script>";
        }
    } else {
        $sql = "UPDATE settings SET company_name = ? WHERE id = 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $company_name);
    }


    if ($stmt->execute()) {
        echo "<script>alert('Settings updated successfully!'); window.location.href = 'settings.php';</script>";
    } else {
        echo "<script>alert('Error updating settings: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}

$sql = "SELECT * FROM settings WHERE id = 1";
$result = $conn->query($sql);
$settings = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
            margin-left: auto;  
            margin-right: auto; 
            display: block;
        }

        button:hover {
            background-color: #218838;
        }

        .current-logo {
            margin-top: 15px;
        }

        img {
            max-width: 150px;
        }
    </style>
</head>

<body>

    <div class="container">
        <a href="/project/index.php" class="back-btn">
            <img src="/project/icons/icons-cancel.png" alt="" style="width: 30px; height: 30px;">
        </a>
        <h1>Settings</h1>

        <form method="POST" action="settings.php" enctype="multipart/form-data">
            <div>
                <label for="company_name">Company Name:</label>
                <input type="text" id="company_name" name="company_name" value="<?php echo $settings['company_name']; ?>" required>
            </div>
            <div>
                <label for="company_logo">Company Logo:</label>
                <input type="file" id="company_logo" name="company_logo">
                <div class="current-logo">
                    <p>Current Logo:</p>
                    <img src="<?php echo $settings['company_logo']; ?>" alt="Company Logo">
                </div>
            </div>
            <button type="submit">Save</button>

        </form>

    </div>

</body>

</html>

<?php
$conn->close();
?>