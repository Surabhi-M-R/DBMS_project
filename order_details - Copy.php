<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['order_id'])) {
    header('Location: orders.php');
    exit;
}

$order_id = $_GET['order_id'];

// Verify the order belongs to the user
$stmt = $pdo->prepare("
    SELECT * FROM orders 
    WHERE id = ? AND user_id = ?
");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    header('Location: orders.php');
    exit;
}

// Fetch order items
$stmt = $pdo->prepare("
    SELECT oi.*, p.name, p.image_url, p.description 
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$stmt->execute([$order_id]);
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopMania! - Order Details</title>
    <link rel="stylesheet" href="dashboard.css">
    <style>
        .order-details-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .order-header {
            border-bottom: 1px solid #ddd;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .order-id {
            font-weight: bold;
            color: #0066c0;
            font-size: 24px;
        }
        
        .order-date {
            color: #555;
            margin: 5px 0;
        }
        
        .order-status {
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 16px;
            font-weight: bold;
            display: inline-block;
            margin-top: 10px;
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
        
        .order-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }
        
        .order-items {
            margin-top: 20px;
        }
        
        .order-item {
            display: flex;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #f5f5f5;
        }
        
        .order-item-img {
            width: 120px;
            height: 120px;
            object-fit: contain;
            margin-right: 20px;
        }
        
        .order-item-details {
            flex-grow: 1;
        }
        
        .order-item-title {
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 5px;
        }
        
        .order-item-price {
            color: #B12704;
            font-size: 16px;
            margin-bottom: 10px;
        }
        
        .order-item-description {
            color: #555;
            margin-bottom: 10px;
        }
        
        .order-summary {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 4px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .summary-total {
            font-weight: bold;
            font-size: 18px;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
        }
        
        .shipping-info {
            margin-top: 30px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 4px;
        }
        
        .back-to-orders {
            display: inline-block;
            margin-top: 20px;
            color: #0066c0;
            text-decoration: none;
        }
        
        .back-to-orders:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <?php include 'nav.php'; ?>
    
    <div class="order-details-container">
        <div class="order-header">
            <div class="order-id">Order #<?php echo $order['id']; ?></div>
            <div class="order-date">Placed on <?php echo date('F j, Y', strtotime($order['created_at'])); ?></div>
            <div class="order-status <?php echo $status_class; ?>">
                <?php echo htmlspecialchars($order['status']); ?>
            </div>
        </div>
        
        <div class="order-grid">
            <div class="order-items">
                <h2>Order Items</h2>
                <?php foreach ($order_items as $item): ?>
                    <div class="order-item">
                        <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="order-item-img">
                        <div class="order-item-details">
                            <div class="order-item-title"><?php echo htmlspecialchars($item['name']); ?></div>
                            <div class="order-item-price">₹<?php echo number_format($item['price_per_unit'], 2); ?> × <?php echo $item['quantity']; ?></div>
                            <div class="order-item-description"><?php echo htmlspecialchars($item['description']); ?></div>
                            <div>Total: ₹<?php echo number_format($item['price_per_unit'] * $item['quantity'], 2); ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="order-summary">
                <h2>Order Summary</h2>
                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span>₹<?php echo number_format($order['total_amount'], 2); ?></span>
                </div>
                <div class="summary-row">
                    <span>Shipping:</span>
                    <span>FREE</span>
                </div>
                <div class="summary-row">
                    <span>Tax:</span>
                    <span>₹0.00</span>
                </div>
                <div class="summary-row summary-total">
                    <span>Total:</span>
                    <span>₹<?php echo number_format($order['total_amount'], 2); ?></span>
                </div>
            </div>
        </div>
        
        <div class="shipping-info">
            <h2>Shipping Information</h2>
            <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>
            <p><strong>Shipping Address:</strong></p>
            <p><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
        </div>
        
        <a href="orders.php" class="back-to-orders">&larr; Back to Orders</a>
    </div>
</body>
</html>