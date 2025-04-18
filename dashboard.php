<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require 'db.php';

// Fetch user details from database
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopMania! - Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <nav>
        <a href="/"><img src="https://images.unsplash.com/photo-1556740738-b6a63e27c4df?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80" alt="ShopMania Logo" class="logo"></a>
        
        <div class="nav-country">
            <i class="fa-solid fa-location-dot"></i>
            <div>
                <p>Deliver to</p>
                <h1>India</h1>
            </div>
        </div>
        <div class="nav-search">
            <div class="nav-search-category">
                <p>All</p>
                <i class="fa-solid fa-chevron-down"></i>
            </div>
            <input type="text" class="nav-search-input" placeholder="Search ShopMania!">
            <div class="nav-search-icon"> 
                <i class="fa-solid fa-magnifying-glass"></i>
            </div>
        </div>
        <div class="nav-language">
            <i class="fa-solid fa-globe"></i>
            <p>EN</p>
            <i class="fa-solid fa-chevron-down"></i>
        </div>
        <div class="user-profile nav-text">
            <div>
                <p>Hello, <?php echo htmlspecialchars($user['username']); ?></p>
                <h1>Account <i class="fa-solid fa-chevron-down"></i></h1>
            </div>
            <div class="user-dropdown">
                <a href="dashboard.php"><i class="fa-solid fa-user"></i> My Account</a>
                <a href="orders.php"><i class="fa-solid fa-box"></i> My Orders</a>
                <a href="wishlist.php"><i class="fa-solid fa-heart"></i> Wishlist</a>
                <a href="settings.php"><i class="fa-solid fa-cog"></i> Account Settings</a>
                <a href="logout.php"><i class="fa-solid fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
        <a href="orders.php" class="nav-text">
            <p>Return</p>
            <h1>& Orders</h1>
        </a>
        <a href="cart.php" class="nav-cart">
            <i class="fa-solid fa-cart-shopping"></i>
            <span class="cart-count">0</span>
            <h4>Cart</h4>
        </a>
    </nav>
    <div class="nav-bottom">
        <div>
            <i class="fa-solid fa-bars"></i>
            <p>All</p>
        </div>
        <p>Today's Deals</p>
        <p>Customer Services</p>
        <p>Registry</p>
        <p>Gift Cards</p>
        <p>Sell</p>
    </div>

    <div class="welcome-banner">
        <h2>Welcome back, <?php echo htmlspecialchars($user['username']); ?>!</h2>
        <p>Explore the best deals and exclusive offers at ShopMania!</p>
    </div>

    <div class="account-section">
        <h2><i class="fas fa-user-circle"></i> Account Information</h2>
        <div class="account-details">
            <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Member since:</strong> <?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>
        </div>
        <a href="settings.php" class="btn"><i class="fa-solid fa-edit"></i> Edit Account</a>
    </div>

    <div class="header-slider">
        <a href="#" class="control_prev"><i class="fa-solid fa-chevron-left"></i></a>
        <a href="#" class="control_next"><i class="fa-solid fa-chevron-right"></i></a>
        <ul>
            <li><img src="https://images.unsplash.com/photo-1523275335684-37898b6baf30?ixlib=rb-4.0.3&auto=format&fit=crop&w=1399&q=80" class="header-img" alt="Electronics"></li>
            <li><img src="https://images.unsplash.com/photo-1491637639811-60e2756cc1c7?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80" class="header-img" alt="Stationery"></li>
            <li><img src="https://images.unsplash.com/photo-1555529669-e69e7aa0ba9a?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80" class="header-img" alt="Camera"></li>
            <li><img src="https://images.unsplash.com/photo-1571781926291-c477ebfd024b?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80" class="header-img" alt="Watch"></li>
        </ul>
    </div>

    <div class="shop-section header-box">
        <?php
        $stmt = $pdo->prepare("SELECT * FROM products LIMIT 8");
        $stmt->execute();
        $products = $stmt->fetchAll();
        
        if (empty($products)) {
            $products = [
                ['id' => 1, 'name' => 'Wireless Headphones', 'price' => 99.99, 'image_url' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80'],
                ['id' => 2, 'name' => 'Smart Watch', 'price' => 199.99, 'image_url' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?ixlib=rb-4.0.3&auto=format&fit=crop&w=1399&q=80'],
                ['id' => 3, 'name' => 'Bluetooth Speaker', 'price' => 79.99, 'image_url' => 'https://images.unsplash.com/photo-1572569511254-d8f925fe2cbb?ixlib=rb-4.0.3&auto=format&fit=crop&w=1489&q=80'],
                ['id' => 4, 'name' => 'Coffee Maker', 'price' => 129.99, 'image_url' => 'https://images.unsplash.com/photo-1580913428735-bd3c269d6a82?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80'],
                ['id' => 5, 'name' => 'Running Shoes', 'price' => 89.99, 'image_url' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80'],
                ['id' => 6, 'name' => 'Backpack', 'price' => 49.99, 'image_url' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80'],
                ['id' => 7, 'name' => 'Sunglasses', 'price' => 59.99, 'image_url' => 'https://images.unsplash.com/photo-1511499767150-a48a237f0083?ixlib=rb-4.0.3&auto=format&fit=crop&w=1480&q=80'],
                ['id' => 8, 'name' => 'Smartphone', 'price' => 699.99, 'image_url' => 'https://images.unsplash.com/photo-1592899677977-9c10ca588bbd?ixlib=rb-4.0.3&auto=format&fit=crop&w=1528&q=80']
            ];
        }
        
        foreach ($products as $product): ?>
            <div class="box">
                <div class="box-content">
                    <h2><?php echo htmlspecialchars($product['name']); ?></h2>
                    <div class="box-img" style="background-image: url('<?php echo htmlspecialchars($product['image_url']); ?>');">
                        <form method="post" action="cart.php" class="add-to-cart-form">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <button type="submit" name="add_to_cart" class="add-to-cart-btn">
                                <i class="fa-solid fa-cart-plus"></i> Add to Cart
                            </button>
                        </form>
                    </div>
                    <p class="price">₹<?php echo number_format($product['price'], 2); ?></p>
                    <a href="#" class="see-more">See more</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="box1">
        <h2>Discover Unique Finds at ShopMania!</h2>
        <p>Explore our curated collection of exclusive products.</p>
    </div>

    <div class="gallery-wrap">
        <div id="backbtn"><i class="fas fa-chevron-left fa-2x"></i></div>
        <div class="gallery">
            <div>
                <span><img src="https://images.unsplash.com/photo-1517336714731-489689fd1ca8?ixlib=rb-4.0.3&auto=format&fit=crop&w=1526&q=80" alt="Electronics"></span>
                <span><img src="https://images.unsplash.com/photo-1460353581641-37baddab0fa2?ixlib=rb-4.0.3&auto=format&fit=crop&w=1471&q=80" alt="Footwear"></span>
                <span><img src="https://images.unsplash.com/photo-1556911220-bff31c812dba?ixlib=rb-4.0.3&auto=format&fit=crop&w=1468&q=80" alt="Home Decor"></span>
            </div>
            <div>
                <span><img src="https://images.unsplash.com/photo-1542838132-92c53300491e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1374&q=80" alt="Groceries"></span>
                <span><img src="https://images.unsplash.com/photo-1489987707025-afc232f7ea0f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80" alt="Clothing"></span>
                <span><img src="https://images.unsplash.com/photo-1544947950-fa07a98d237f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80" alt="Books"></span>
            </div>
        </div>
        <div id="frontbtn"><i class="fas fa-chevron-right fa-2x"></i></div>
    </div>

    <footer>
        <div class="foot-panel1">
            Back to top
        </div>
        <div class="foot-panel2">
            <ul>
                <p>Get to know us</p>
                <a href="#">Careers</a>
                <a href="#">Blog</a>
                <a href="#">About ShopMania</a>
                <a href="#">Investor Relations</a>
                <a href="#">ShopMania Devices</a>
                <a href="#">ShopMania Science</a>
            </ul>
            <ul>
                <p>Connect with us</p>
                <a href="#"><i class="fab fa-facebook"></i> Facebook</a>
                <a href="#"><i class="fab fa-twitter"></i> Twitter</a>
                <a href="#"><i class="fab fa-instagram"></i> Instagram</a>
            </ul>
            <ul>
                <p>Make money with us</p>
                <a href="#">Sell on ShopMania!</a>
                <a href="#">Protect and Build Your Brand</a>
                <a href="#">Amazon Global Selling</a>
                <a href="#">Advertise Your Products</a>
            </ul>
            <ul>
                <p>Let Us Help You</p>
                <a href="#">Your Account</a>
                <a href="#">Returns Centre</a>
                <a href="#">ShopMania! App Download</a>
                <a href="#">Help</a>
            </ul>
        </div>
        <div class="foot-panel3">
            <img src="https://images.unsplash.com/photo-1556740738-b6a63e27c4df?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80" alt="ShopMania Logo" class="logo">
        </div>
        <div class="foot-panel4">
            <div class="pages">
                <a href="#">Conditions of use</a>
                <a href="#">Privacy Notice</a>
                <a href="#">Your ads Privacy Choices</a>
            </div>
            <div class="copy-right">
                © 1996-2025, ShopMania!.com, Inc. or its affiliates
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Dashboard loaded for user: <?php echo $user["username"]; ?>');
            
            // Slider functionality
            const slider = document.querySelector('.header-slider ul');
            const slides = document.querySelectorAll('.header-slider li');
            const prevBtn = document.querySelector('.control_prev');
            const nextBtn = document.querySelector('.control_next');
            let currentIndex = 0;
            
            function showSlide(index) {
                slider.style.transform = `translateX(-${index * 100}%)`;
            }
            
            prevBtn.addEventListener('click', (e) => {
                e.preventDefault();
                currentIndex = (currentIndex > 0) ? currentIndex - 1 : slides.length - 1;
                showSlide(currentIndex);
            });
            
            nextBtn.addEventListener('click', (e) => {
                e.preventDefault();
                currentIndex = (currentIndex < slides.length - 1) ? currentIndex + 1 : 0;
                showSlide(currentIndex);
            });

            // Gallery navigation
            const gallery = document.querySelector('.gallery');
            const backBtn = document.querySelector('#backbtn');
            const frontBtn = document.querySelector('#frontbtn');
            
            backBtn.addEventListener('click', () => {
                gallery.scrollBy({ left: -gallery.offsetWidth, behavior: 'smooth' });
            });
            
            frontBtn.addEventListener('click', () => {
                gallery.scrollBy({ left: gallery.offsetWidth, behavior: 'smooth' });
            });
        });
    </script>
</body>
</html>