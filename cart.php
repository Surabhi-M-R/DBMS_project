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
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f6f6f6;
            margin: 0;
            padding: 20px;
        }
        
        .cart-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .cart-header {
            border-bottom: 1px solid #ddd;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .cart-header h1 {
            margin: 0;
            color: #333;
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
            color: #333;
        }
        
        .cart-item-price {
            font-weight: bold;
            color: #B12704;
            margin-bottom: 10px;
        }
        
        .cart-item-quantity {
            display: flex;
            align-items: center;
            margin: 10px 0;
        }
        
        .quantity-input {
            width: 50px;
            text-align: center;
            margin: 0 10px;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .cart-item-quantity button {
            padding: 5px 10px;
            background: #f0f0f0;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .cart-item-quantity button:hover {
            background: #e0e0e0;
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
            color: #333;
        }
        
        .checkout-btn {
            background-color: #f0c14b;
            border: 1px solid #a88734;
            padding: 10px 20px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 16px;
            color: #111;
            text-decoration: none;
            display: inline-block;
        }
        
        .checkout-btn:hover {
            background-color: #ddb347;
        }
        
        .empty-cart {
            text-align: center;
            padding: 50px;
        }
        
        .empty-cart h2 {
            color: #333;
            margin-bottom: 10px;
        }
        
        .empty-cart p {
            color: #666;
            margin-bottom: 20px;
        }
        
        .continue-shopping {
            display: inline-block;
            margin-top: 20px;
            color: #0066c0;
            text-decoration: none;
            padding: 8px 16px;
            border: 1px solid #0066c0;
            border-radius: 3px;
        }
        
        .continue-shopping:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
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
                    <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                         alt="<?php echo htmlspecialchars($item['name']); ?>" 
                         class="cart-item-image"
                         onerror="this.src='https://via.placeholder.com/100x100?text=Product+Image'">
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
            const form = button.closest('form');
            
            // Create hidden inputs if they don't exist
            if (!form.querySelector('[name="cart_id"]')) {
                const cartIdInput = document.createElement('input');
                cartIdInput.type = 'hidden';
                cartIdInput.name = 'cart_id';
                cartIdInput.value = input.dataset.cartId;
                form.appendChild(cartIdInput);
            }
            
            if (!form.querySelector('[name="update_quantity"]')) {
                const updateInput = document.createElement('input');
                updateInput.type = 'hidden';
                updateInput.name = 'update_quantity';
                updateInput.value = '1';
                form.appendChild(updateInput);
            }
            
            form.submit();
        }

        // Handle image errors if the onerror attribute didn't work
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.cart-item-image').forEach(img => {
                img.onerror = function() {
                    this.src = 'https://via.placeholder.com/100x100?text=Product+Image';
                };
            });
        });
    </script>
</body>
</html>