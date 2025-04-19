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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            margin: 0;
            padding: 0;
            color: #333;
        }
        
        .background-blur {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(102, 45, 145, 0.1) 0%, rgba(102, 45, 145, 0.05) 100%);
            backdrop-filter: blur(5px);
            z-index: -1;
        }
        
        .orders-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 30px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }
        
        .orders-header {
            border-bottom: 1px solid rgba(102, 45, 145, 0.1);
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .orders-header h1 {
            font-size: 28px;
            font-weight: 600;
            margin: 0;
            color: #662d91;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .order-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(102, 45, 145, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .order-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(102, 45, 145, 0.1);
        }
        
        .order-header > div {
            display: flex;
            flex-direction: column;
        }
        
        .order-id {
            font-weight: 600;
            color: #662d91;
            font-size: 18px;
        }
        
        .order-date {
            color: #666;
            font-size: 14px;
            margin-top: 5px;
        }
        
        .order-status {
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .status-processing {
            background: rgba(255, 193, 7, 0.15);
            color: #ff8f00;
            border: 1px solid rgba(255, 193, 7, 0.3);
        }
        
        .status-shipped {
            background: rgba(76, 175, 80, 0.15);
            color: #2e7d32;
            border: 1px solid rgba(76, 175, 80, 0.3);
        }
        
        .status-delivered {
            background: rgba(33, 150, 243, 0.15);
            color: #1565c0;
            border: 1px solid rgba(33, 150, 243, 0.3);
        }
        
        .order-details {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }
        
        .order-items {
            margin-top: 10px;
        }
        
        .order-item {
            display: flex;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(102, 45, 145, 0.1);
        }
        
        .order-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .order-item-img {
            width: 80px;
            height: 80px;
            object-fit: contain;
            margin-right: 20px;
            border-radius: 8px;
            background: white;
            padding: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        .order-item-details {
            flex-grow: 1;
        }
        
        .order-item-title {
            font-weight: 500;
            margin-bottom: 8px;
            color: #333;
        }
        
        .order-item-price {
            color: #B12704;
            font-weight: 500;
            margin-bottom: 5px;
        }
        
        .order-summary {
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(102, 45, 145, 0.1);
        }
        
        .order-summary h3 {
            margin-top: 0;
            margin-bottom: 20px;
            color: #662d91;
            font-size: 18px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px dashed rgba(102, 45, 145, 0.1);
            align-items: center;
        }
        
        .summary-row span {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .summary-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .summary-total {
            font-weight: 600;
            font-size: 18px;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid rgba(102, 45, 145, 0.2);
            color: #662d91;
        }
        
        .view-details-btn {
            background: linear-gradient(135deg, #662d91 0%, #912d73 100%);
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 20px;
            font-weight: 500;
            transition: all 0.3s ease;
            width: 100%;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(102, 45, 145, 0.2);
        }
        
        .view-details-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 45, 145, 0.3);
            background: linear-gradient(135deg, #5a267f 0%, #7e265f 100%);
        }
        
        .empty-orders {
            text-align: center;
            padding: 60px 20px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 12px;
            border: 1px solid rgba(102, 45, 145, 0.1);
        }
        
        .empty-orders h2 {
            color: #662d91;
            font-weight: 600;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .empty-orders p {
            color: #666;
            font-size: 16px;
            margin-bottom: 25px;
        }
        
        .continue-shopping {
            display: inline-flex;
            align-items: center;
            margin-top: 20px;
            padding: 12px 30px;
            background: linear-gradient(135deg, #662d91 0%, #912d73 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 45, 145, 0.2);
            gap: 8px;
        }
        
        .continue-shopping:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 45, 145, 0.3);
            background: linear-gradient(135deg, #5a267f 0%, #7e265f 100%);
        }
        
        @media (max-width: 768px) {
            .order-details {
                grid-template-columns: 1fr;
            }
            
            .orders-container {
                padding: 20px 15px;
                margin: 20px 15px;
            }
            
            .order-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .order-status {
                margin-top: 10px;
                align-self: flex-start;
            }
            
            .order-item {
                flex-direction: column;
            }
            
            .order-item-img {
                margin-right: 0;
                margin-bottom: 15px;
                width: 100%;
                height: auto;
                max-height: 200px;
            }
        }
    </style>
</head>
<body>
    <div class="background-blur"></div>
    <?php include 'nav.php'; ?>
    
    <div class="orders-container animate__animated animate__fadeIn">
        <div class="orders-header">
            <h1><i class="fas fa-box-open"></i> Your Orders</h1>
        </div>
        
        <?php if (empty($orders)): ?>
            <div class="empty-orders">
                <h2><i class="fas fa-shopping-basket"></i> You haven't placed any orders yet</h2>
                <p>Start shopping to see your orders here.</p>
                <a href="dashboard.php" class="continue-shopping"><i class="fas fa-arrow-left"></i> Continue Shopping</a>
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
                            <i class="fas fa-<?php 
                                if ($status_class == 'status-processing') echo 'clock';
                                elseif ($status_class == 'status-shipped') echo 'truck';
                                else echo 'check-circle';
                            ?>"></i> <?php echo htmlspecialchars($order['status']); ?>
                        </div>
                    </div>
                    
                    <div class="order-details">
                        <div class="order-items">
                            <?php foreach ($order_items as $item): ?>
                                <div class="order-item">
                                    <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="order-item-img" onerror="this.src='https://via.placeholder.com/80?text=Product+Image'">
                                    <div class="order-item-details">
                                        <div class="order-item-title"><?php echo htmlspecialchars($item['name']); ?></div>
                                        <div class="order-item-price">₹<?php echo number_format($item['price_per_unit'], 2); ?> × <?php echo $item['quantity']; ?></div>
                                        <div>Total: ₹<?php echo number_format($item['price_per_unit'] * $item['quantity'], 2); ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="order-summary">
                            <h3><i class="fas fa-receipt"></i> Order Summary</h3>
                            <div class="summary-row">
                                <span><i class="fas fa-tag"></i> Subtotal:</span>
                                <span>₹<?php echo number_format($order['total_amount'], 2); ?></span>
                            </div>
                            <div class="summary-row">
                                <span><i class="fas fa-shipping-fast"></i> Shipping:</span>
                                <span>FREE</span>
                            </div>
                            <div class="summary-row summary-total">
                                <span><i class="fas fa-wallet"></i> Total:</span>
                                <span>₹<?php echo number_format($order['total_amount'], 2); ?></span>
                            </div>
                            
                            <div class="summary-row">
                                <span><i class="fas fa-credit-card"></i> Payment Method:</span>
                                <span><?php echo htmlspecialchars($order['payment_method']); ?></span>
                            </div>
                            
                            <div class="summary-row">
                                <span><i class="fas fa-map-marker-alt"></i> Shipping Address:</span>
                                <span><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></span>
                            </div>
                            
                            <a href="order_details.php?order_id=<?php echo $order['id']; ?>" class="view-details-btn">
                                <i class="fas fa-search"></i> View Order Details
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/gsap.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Animation for order cards
            gsap.from('.order-card', {
                duration: 0.5,
                y: 20,
                opacity: 0,
                stagger: 0.1,
                ease: "power2.out"
            });
            
            // Fallback for broken images
            document.querySelectorAll('.order-item-img').forEach(img => {
                img.addEventListener('error', function() {
                    this.src = 'https://via.placeholder.com/80?text=Product+Image';
                });
            });
        });
    </script>
</body>
</html> 