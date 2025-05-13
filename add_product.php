<?php include 'db.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <title>Add to Cart</title>
</head>
<body>
    <h2>Add to cart</h2>
    <form action="add_product.php" method="POST">
        <label>Product Name:</label><br>
        <input type="text" name="name" required><br><br>

        <label>Price:</label><br>
        <input type="number" step="0.01" name="price" required><br><br>

        <label>Image Filename (e.g., shirt.jpg):</label><br>
        <input type="text" name="image" required><br><br>

        <input type="submit" name="submit" value="Add Product">
    </form>

    <?php
    if (isset($_POST['submit'])) {
        $name = $_POST['name'];
        $price = $_POST['price'];
        $image = $_POST['image'];

        $sql = "INSERT INTO products (name, price, image) VALUES ('$name', '$price', '$image')";

        if ($conn->query($sql) === TRUE) {
            echo "<p style='color:green;'>Product added successfully!</p>";
        } else {
            echo "<p style='color:red;'>Error: " . $conn->error . "</p>";
        }
    }
    ?>
</body>
</html>