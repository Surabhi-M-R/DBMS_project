<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch user details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Fetch cart items with product details
$stmt = $pdo->prepare("
    SELECT c.id as cart_id, c.quantity, p.id as product_id, p.name, p.price, p.image_url 
    FROM cart c 
    JOIN products p ON c.product_id = p.id 
    WHERE c.user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$cart_items = $stmt->fetchAll();

// Calculate total
$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Handle checkout submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['place_order'])) {
    try {
        $pdo->beginTransaction();

        // Create order record
        $stmt = $pdo->prepare("
            INSERT INTO orders (user_id, total_amount, status, shipping_address, payment_method) 
            VALUES (?, ?, 'Processing', ?, ?)
        ");
        $stmt->execute([
            $_SESSION['user_id'],
            $total,
            $_POST['shipping_address'],
            $_POST['payment_method']
        ]);
        $order_id = $pdo->lastInsertId();

        // Create order items
        foreach ($cart_items as $item) {
            $stmt = $pdo->prepare("
                INSERT INTO order_items (order_id, product_id, quantity, price_per_unit)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([
                $order_id,
                $item['product_id'],
                $item['quantity'],
                $item['price']
            ]);
        }

        // Clear cart
        $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);

        $pdo->commit();

        // Redirect to order confirmation
        header("Location: order_confirmation.php?order_id=$order_id");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Order processing failed: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopMania! - Checkout</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .checkout-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 30px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .checkout-header {
            border-bottom: 1px solid #ddd;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .checkout-header h1 {
            margin: 0;
            color: #333;
        }

        .checkout-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }

        .checkout-form {
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #333;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 15px;
        }

        .order-summary {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 6px;
        }

        .order-summary h3 {
            margin-top: 0;
            margin-bottom: 15px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
            color: #444;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .order-total {
            font-size: 18px;
            font-weight: bold;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
        }

        .place-order-btn {
            background-color: #f0c14b;
            border: 1px solid #a88734;
            padding: 12px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            margin-top: 20px;
            transition: background-color 0.3s;
        }

        .place-order-btn:hover {
            background-color: #e6b23a;
        }

        .error-message {
            color: red;
            margin-bottom: 15px;
        }

        .empty-cart {
            text-align: center;
            padding: 50px;
        }

        .empty-cart h2 {
            color: #555;
        }

        .continue-shopping {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #007bff;
            font-weight: 600;
        }

        .continue-shopping:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="checkout-container">
    <div class="checkout-header">
        <h1>Checkout</h1>
    </div>

    <?php if (empty($cart_items)): ?>
        <div class="empty-cart">
            <h2>Your cart is empty</h2>
            <p>You have no items to checkout.</p>
            <a href="dashboard.php" class="continue-shopping">Continue Shopping</a>
        </div>
    <?php else: ?>
        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="post" action="checkout.php" class="checkout-form">
            <div class="checkout-grid">
                <div>
                    <h2>Shipping Information</h2>

                    <div class="form-group">
                        <label for="full_name">Full Name</label>
                        <input type="text" id="full_name" name="full_name"
                               value="<?php echo htmlspecialchars($user['username']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email"
                               value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="shipping_address">Shipping Address</label>
                        <textarea id="shipping_address" name="shipping_address" rows="4" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" required>
                    </div>

                    <h2>Payment Method</h2>
                    <div class="form-group">
                        <label>
                            <input type="radio" name="payment_method" value="Credit Card" checked>
                            Credit Card
                        </label><br>
                        <label>
                            <input type="radio" name="payment_method" value="PayPal">
                            PayPal
                        </label><br>
                        <label>
                            <input type="radio" name="payment_method" value="Cash on Delivery">
                            Cash on Delivery
                        </label>
                    </div>
                </div>

                <div class="order-summary">
                    <h3>Order Summary</h3>
                    <?php foreach ($cart_items as $item): ?>
                        <div class="order-item">
                            <span><?php echo htmlspecialchars($item['name']); ?> (×<?php echo $item['quantity']; ?>)</span>
                            <span>₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                        </div>
                    <?php endforeach; ?>

                    <div class="order-total">
                        <span>Total:</span>
                        <span>₹<?php echo number_format($total, 2); ?></span>
                    </div>

                    <button type="submit" name="place_order" class="place-order-btn">Place Order</button>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>

</body>
</html>
