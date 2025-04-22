<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Handle add to cart requests
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $user_id = $_SESSION['user_id'];
    
    // Check if product already in cart
    $stmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    
    if ($stmt->rowCount() > 0) {
        // Update quantity if already in cart
        $stmt = $pdo->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$user_id, $product_id]);
    } else {
        // Add new item to cart
        $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)");
        $stmt->execute([$user_id, $product_id]);
    }
}

// Handle remove from cart requests
if (isset($_GET['remove'])) {
    $cart_id = $_GET['remove'];
    $user_id = $_SESSION['user_id'];
    
    // Verify the item belongs to the user before deleting
    $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $stmt->execute([$cart_id, $user_id]);
    
    header('Location: cart.php');
    exit;
}

// Handle quantity updates
if (isset($_POST['update_quantity'])) {
    $cart_id = $_POST['cart_id'];
    $quantity = $_POST['quantity'];
    $user_id = $_SESSION['user_id'];
    
    // Verify the item belongs to the user before updating
    $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$quantity, $cart_id, $user_id]);
    
    header('Location: cart.php');
    exit;
}

// Fetch user's cart items with product details
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopMania! - Your Cart</title>
    <link rel="stylesheet" href="dashboard.css">
    <style>
        .cart-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .cart-header {
            border-bottom: 1px solid #ddd;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .cart-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        
        .cart-item-image {
            width: 100px;
            height: 100px;
            object-fit: contain;
            margin-right: 20px;
        }
        
        .cart-item-details {
            flex-grow: 1;
        }
        
        .cart-item-title {
            font-size: 18px;
            margin-bottom: 5px;
        }
        
        .cart-item-price {
            font-weight: bold;
            color: #B12704;
            margin-bottom: 10px;
        }
        
        .cart-item-quantity {
            display: flex;
            align-items: center;
        }
        
        .quantity-input {
            width: 50px;
            text-align: center;
            margin: 0 10px;
        }
        
        .cart-item-actions {
            display: flex;
            flex-direction: column;
        }
        
        .remove-btn {
            background: none;
            border: none;
            color: #0066c0;
            cursor: pointer;
            text-align: left;
            padding: 5px 0;
        }
        
        .remove-btn:hover {
            text-decoration: underline;
            color: #c45500;
        }
        
        .cart-summary {
            margin-top: 20px;
            padding: 20px;
            background-color: #f3f3f3;
            border-radius: 4px;
            text-align: right;
        }
        
        .total-amount {
            font-size: 20px;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .checkout-btn {
            background-color: #f0c14b;
            border: 1px solid #a88734;
            padding: 10px 20px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 16px;
        }
        
        .checkout-btn:hover {
            background-color: #ddb347;
        }
        
        .empty-cart {
            text-align: center;
            padding: 50px;
        }
        
        .continue-shopping {
            display: inline-block;
            margin-top: 20px;
            color: #0066c0;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <!-- Include your existing navigation from dashboard.php -->
    <?php include 'nav.php'; ?>
    
    <div class="cart-container">
        <div class="cart-header">
            <h1>Shopping Cart</h1>
        </div>
        
        <?php if (empty($cart_items)): ?>
            <div class="empty-cart">
                <h2>Your cart is empty</h2>
                <p>You have no items in your shopping cart.</p>
                <a href="dashboard.php" class="continue-shopping">Continue Shopping</a>
            </div>
        <?php else: ?>
            <?php foreach ($cart_items as $item): ?>
                <div class="cart-item">
                    <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="cart-item-image">
                    <div class="cart-item-details">
                        <div class="cart-item-title"><?php echo htmlspecialchars($item['name']); ?></div>
                        <div class="cart-item-price">₹<?php echo number_format($item['price'], 2); ?></div>
                        <form method="post" action="cart.php" class="cart-item-quantity">
                            <button type="button" onclick="this.nextElementSibling.stepDown(); updateQuantity(this)">-</button>
                            <input type="number" name="quantity" class="quantity-input" 
                                   value="<?php echo $item['quantity']; ?>" min="1" 
                                   data-cart-id="<?php echo $item['cart_id']; ?>">
                            <button type="button" onclick="this.previousElementSibling.stepUp(); updateQuantity(this)">+</button>
                        </form>
                    </div>
                    <div class="cart-item-actions">
                        <form method="get" action="cart.php">
                            <input type="hidden" name="remove" value="<?php echo $item['cart_id']; ?>">
                            <button type="submit" class="remove-btn">Remove</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <div class="cart-summary">
                <div class="total-amount">Total: ₹<?php echo number_format($total, 2); ?></div>
                <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
        function updateQuantity(button) {
            const input = button.parentElement.querySelector('.quantity-input');
            const form = document.createElement('form');
            form.method = 'post';
            form.action = 'cart.php';
            
            const cartIdInput = document.createElement('input');
            cartIdInput.type = 'hidden';
            cartIdInput.name = 'cart_id';
            cartIdInput.value = input.dataset.cartId;
            
            const quantityInput = document.createElement('input');
            quantityInput.type = 'hidden';
            quantityInput.name = 'quantity';
            quantityInput.value = input.value;
            
            const updateInput = document.createElement('input');
            updateInput.type = 'hidden';
            updateInput.name = 'update_quantity';
            updateInput.value = '1';
            
            form.appendChild(cartIdInput);
            form.appendChild(quantityInput);
            form.appendChild(updateInput);
            
            document.body.appendChild(form);
            form.submit();
        }
    </script>
</body>
</html>