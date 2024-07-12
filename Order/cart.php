<?php
require '../config.php';
session_start();

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $cart_id = $_GET['id']; // Get cart.id instead of product_id
    $user_id = $_SESSION['user_id'];

    $sql = "DELETE FROM cart WHERE id = ? AND user_id = ?"; // Use cart.id for deletion
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$cart_id, $user_id]);

    // Check if deletion was successful
    $deleted = $stmt->rowCount() > 0;

    if ($deleted) {
        echo "Product deleted from cart.";

        // Refresh cart items after deletion
        $sql = "SELECT cart.id, products.name, products.price, cart.quantity FROM cart 
                JOIN products ON cart.product_id = products.id 
                WHERE cart.user_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id]);
        $cart_items = $stmt->fetchAll();
    } else {
        echo "Failed to delete product from cart.";
    }
}

// Handle add action
if (isset($_GET['action']) && $_GET['action'] == 'add' && isset($_GET['id'])) {
    $product_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    $sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id, $product_id, 1]);

    echo "Product added to cart.";
}

// Retrieve cart items for the current user
if (isset($_SESSION['user_id'])) {
    $sql = "SELECT cart.id, products.name, products.price, cart.quantity FROM cart 
            JOIN products ON cart.product_id = products.id 
            WHERE cart.user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION['user_id']]);
    $cart_items = $stmt->fetchAll();
} else {
    $cart_items = []; // If user_id is not set in session, initialize an empty array
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url("cart.jpg");
            height: 100%;
            background-position: justify;
            background-repeat: no-repeat;
            background-size: cover;
        }

        .container {
            padding-top: 20px;
        }

        .table {
            background-color: #ffffff;
            /* White background for the table */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .table th,
        .table td {
            vertical-align: middle;
        }

        .btn-checkout {
            margin-top: 20px;
        }

        /* Optional: Add more styling for buttons */
        .btn-primary {
            background-color: #007bff;
            /* Blue primary color */
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            /* Darker blue on hover */
            border-color: #0056b3;
        }

        .btn-secondary {
            background-color: #6c757d;
            /* Gray secondary color */
            border-color: #6c757d;
        }

        .btn-secondary:hover {
            background-color: #545b62;
            /* Darker gray on hover */
            border-color: #545b62;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h1 class="mb-4">Cart</h1>
        <table class="table table-bordered">
            <thead class="thead-light">
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart_items as $item): ?>
                    <tr>
                        <td><?php echo $item['name']; ?></td>
                        <td><?php echo number_format($item['price'], 0, ',', '.'); ?> RP</td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td><?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?> RP</td>
                        <td>
                            <a href="cart.php?action=delete&id=<?php echo $item['id']; ?>"
                                class="btn btn-danger btn-sm">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="checkout.php" class="btn btn-primary btn-checkout">Proceed to Checkout</a>
        <a href="../index.php" class="btn btn-secondary">Back to Products</a>
    </div>
</body>

</html>