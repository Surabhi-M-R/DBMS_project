<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch all orders for the current user
$stmt = $pdo->prepare("
    SELECT * FROM orders 
    WHERE user_id = ? 
    ORDER BY created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopMania! - My Orders</title>
    <link rel="stylesheet" href="dashboard.css">
    <style>
        .orders-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .orders-header {
            border-bottom: 1px solid #ddd;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .order-card {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .order-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .order-id {
            font-weight: bold;
            color: #0066c0;
        }
        
        .order-date {
            color: #555;
        }
        
        .order-status {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 14px;
            font-weight: bold;
        }
        
        .status-processing {
            background-color: #fff8e1;
            color: #ff8f00;
        }
        
        .status-shipped {
            background-color: #e8f5e9;
            color: #2e7d32;
        }
        
        .status-delivered {
            background-color: #e3f2fd;
            color: #1565c0;
        }
        
        .order-details {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }
        
        .order-items {
            margin-top: 10px;
        }
        
        .order-item {
            display: flex;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #f5f5f5;
        }
        
        .order-item-img {
            width: 80px;
            height: 80px;
            object-fit: contain;
            margin-right: 15px;
        }
        
        .order-item-details {
            flex-grow: 1;
        }
        
        .order-item-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .order-item-price {
            color: #B12704;
        }
        
        .order-summary {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 4px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        
        .summary-total {
            font-weight: bold;
            font-size: 18px;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
        }
        
        .view-details-btn {
            background-color: #f0c14b;
            border: 1px solid #a88734;
            padding: 8px 15px;
            border-radius: 3px;
            color: #111;
            text-decoration: none;
            display: inline-block;
            margin-top: 10px;
        }
        
        .view-details-btn:hover {
            background-color: #ddb347;
        }
        
        .empty-orders {
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
    <?php include 'nav.php'; ?>
    
    <div class="orders-container">
        <div class="orders-header">
            <h1>Your Orders</h1>
        </div>
        
        <?php if (empty($orders)): ?>
            <div class="empty-orders">
                <h2>You haven't placed any orders yet</h2>
                <p>Start shopping to see your orders here.</p>
                <a href="dashboard.php" class="continue-shopping">Continue Shopping</a>
            </div>
        <?php else: ?>
            <?php foreach ($orders as $order): 
                // Fetch items for this order
                $stmt = $pdo->prepare("
                    SELECT oi.*, p.name, p.image_url 
                    FROM order_items oi
                    JOIN products p ON oi.product_id = p.id
                    WHERE oi.order_id = ?
                ");
                $stmt->execute([$order['id']]);
                $order_items = $stmt->fetchAll();
                
                // Determine status class
                $status_class = '';
                if (strpos($order['status'], 'Processing') !== false) {
                    $status_class = 'status-processing';
                } elseif (strpos($order['status'], 'Shipped') !== false) {
                    $status_class = 'status-shipped';
                } elseif (strpos($order['status'], 'Delivered') !== false) {
                    $status_class = 'status-delivered';
                }
                ?>
                
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <span class="order-id">Order #<?php echo $order['id']; ?></span>
                            <span class="order-date">Placed on <?php echo date('F j, Y', strtotime($order['created_at'])); ?></span>
                        </div>
                        <div class="order-status <?php echo $status_class; ?>">
                            <?php echo htmlspecialchars($order['status']); ?>
                        </div>
                    </div>
                    
                    <div class="order-details">
                        <div class="order-items">
                            <?php foreach ($order_items as $item): ?>
                                <div class="order-item">
                                    <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="order-item-img">
                                    <div class="order-item-details">
                                        <div class="order-item-title"><?php echo htmlspecialchars($item['name']); ?></div>
                                        <div class="order-item-price">₹<?php echo number_format($item['price_per_unit'], 2); ?> × <?php echo $item['quantity']; ?></div>
                                        <div>Total: ₹<?php echo number_format($item['price_per_unit'] * $item['quantity'], 2); ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="order-summary">
                            <h3>Order Summary</h3>
                            <div class="summary-row">
                                <span>Subtotal:</span>
                                <span>₹<?php echo number_format($order['total_amount'], 2); ?></span>
                            </div>
                            <div class="summary-row">
                                <span>Shipping:</span>
                                <span>FREE</span>
                            </div>
                            <div class="summary-row summary-total">
                                <span>Total:</span>
                                <span>₹<?php echo number_format($order['total_amount'], 2); ?></span>
                            </div>
                            
                            <div class="summary-row">
                                <span>Payment Method:</span>
                                <span><?php echo htmlspecialchars($order['payment_method']); ?></span>
                            </div>
                            
                            <div class="summary-row">
                                <span>Shipping Address:</span>
                                <span><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></span>
                            </div>
                            
                            <a href="order_details.php?order_id=<?php echo $order['id']; ?>" class="view-details-btn">View Order Details</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>