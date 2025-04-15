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
    <style>
        .user-profile {
            position: relative;
            display: inline-block;
        }
        .user-dropdown {
            display: none;
            position: absolute;
            right: 0;
            background-color: #f9f9f9;
            min-width: 200px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
            border-radius: 4px;
        }
        .user-dropdown a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }
        .user-dropdown a:hover {
            background-color: #f1f1f1;
        }
        .user-profile:hover .user-dropdown {
            display: block;
        }
        .welcome-banner {
            background-color: #232f3e;
            color: white;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .account-section {
            background-color: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <nav>
        <a href="/"><img src="shop.jpg"></a>
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
                <i class="fa-solid fa-circle-chevron-down"></i>
            </div>
            <input type="text" class="nav-search-input" placeholder="Search ShopMania!">
            <div class="nav-search-icon"> 
                <i class="fa-solid fa-magnifying-glass"></i>
            </div>
        </div>
        <div class="nav-language">
            <i class="fa-solid fa-flag"></i>
            <p>EN</p>
            <i class="fa-solid fa-circle-chevron-down"></i>
        </div>
        <div class="user-profile nav-text">
            <div>
                <p>Hello, <?php echo htmlspecialchars($user['username']); ?></p>
                <h1>Account <i class="fa-solid fa-circle-chevron-down"></i></h1>
            </div>
            <div class="user-dropdown">
                <a href="dashboard.php">My Account</a>
                <a href="orders.php">My Orders</a>
                <a href="wishlist.php">Wishlist</a>
                <a href="settings.php">Account Settings</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
        <a href="orders.php" class="nav-text">
            <p>Return</p>
            <h1>& Orders</h1>
        </a>
        <a href="cart.php" class="nav-cart">
            <i class="fa-solid fa-cart-shopping"></i>
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

    <!-- Welcome Banner with User Info -->
    <div class="welcome-banner">
        <h2>Welcome back, <?php echo htmlspecialchars($user['username']); ?>!</h2>
        <p>Enjoy your shopping experience at ShopMania!</p>
    </div>

    <!-- Account Section -->
    <div class="account-section">
        <h2><i class="fas fa-user-circle"></i> Account Information</h2>
        <div class="account-details">
            <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Member since:</strong> <?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>
        </div>
        <a href="settings.php" class="btn">Edit Account</a>
    </div>

    <!-- Main Shop Content -->
    <div class="header-slider">
        <a href="#" class="control_prev"><i class="fa-solid fa-arrow-left"></i></a>
        <a href="#" class="control_next"><i class="fa-solid fa-arrow-right"></i></a>
        <ul>
            <img src="kitchen.jpg" class="header-img" alt="">
            <img src="SamplePhoto_3.jpg" class="header-img" alt="">
            <img src="SamplePhoto_6.jpg" class="header-img" alt="">
            <img src="SamplePhoto_14.jpg" class="header-img" alt="">
        </ul>
    </div>
    <div class="shop-section header-box">
        <div class="box1 box">
            <div class="box-content">
                <h2>Health and Personal Care</h2>
                <div class="box-img" style="background-image: url('v1.jpg');"></div>
                <p>See more</p>
            </div>
        </div>
        <div class="box2 box">
            <div class="box-content">
                <h2>Electronics Gadgets</h2>
                <div class="box-img" style="background-image: url('hero.jpg');"></div>
                <p>See more</p>
            </div>
        </div>
        <div class="box3 box">
            <div class="box-content">
                <h2>Beauty</h2>
                <div class="box-img" style="background-image: url('beauty.jpg');"></div>
                <p>See more</p>
            </div>
        </div>
        <div class="box4 box">
            <div class="box-content">
                <h2>Fashion</h2>
                <div class="box-img" style="background-image: url('fashion.jpg');"></div>
                <p>See more</p>
            </div>
        </div>
        <div class="box5 box">
            <div class="box-content">
                <h2>Grocery</h2>
                <div class="box-img" style="background-image: url('grocery.jpg');"></div>
                <p>See more</p>
            </div>
        </div>
        <div class="box6 box">
            <div class="box-content">
                <h2>Sandals</h2>
                <div class="box-img" style="background-image: url('foot.jpg');"></div>
                <p>See more</p>
            </div>
        </div>
        <div class="box7 box">
            <div class="box-content">
                <h2>Mobile</h2>
                <div class="box-img" style="background-image: url('phone.jpg');"></div>
                <p>See more</p>
            </div>
        </div>
        <div class="box8 box">
            <div class="box-content">
                <h2>Accessories</h2>
                <div class="box-img" style="background-image: url('access.jpg');"></div>
                <p>See more</p>
            </div>
        </div>
    </div>
    <div class="box1">
        <h2>For a Store with Unique Selling..</h2>
    </div>
    <div class="gallery-wrap">
        <img src="back.jpg" id="backbtn">
        <div class="gallery">
            <div>
                <span><img src="ele.jpg"></span>
                <span><img src="foot1.jpg"></span>
                <span><img src="home1.jpg"></span>
            </div>
            <div>
                <span><img src="groc1.jpg"></span>
                <span><img src="clo1.jpg"></span>
                <span><img src="book1.jpg"></span>
            </div>
        </div>
        <img src="front.png" id="frontbtn">
    </div>
    <footer>
        <div class="foot-panel1">
            Back to top
        </div>
        <div class="foot-panel2">
            <ul>
                <p>Get to know us</p>
                <a>Carrers</a>
                <a>Blog</a>
                <a>About ShopMania</a>
                <a>Investor Relations</a>
                <a>ShopMania Devices</a>
                <a>ShopMania Science</a>
            </ul>
            <ul>
                <p>Connect with us</p>
                <a>Facebook</a>
                <a>Twitter</a>
                <a>Instagram</a>
            </ul>
            <ul>
                <p>Make money with us</p>
                <a>Sell on ShopMania!</a>
                <a>Protect and Build Your Brand</a>
                <a>Amazon Global Selling</a>
                <a>Investor Relations</a>
                <a>Advertise Your Products </a>
            </ul>
            <ul>
                <p>Let Us Help You</p>
                <a>Your Account</a>
                <a>Returns Centre</a>
                <a>ShopMania! App Download</a>
                <a>Help</a>
            </ul>
        </div>
        <div class="foot-panel3">
            <div class="logo"></div>
        </div>
        <div class="foot-panel4">
            <div class="pages">
                <a>Conditions of use</a>
                <a>Privacy Notice</a>
                <a>Your ads Privacy Choices</a>
            </div>
            <div class="copy-right">
                o 1996-2023, ShopMania!.com, Inc. or its affliations
            </div>
        </div>
    </footer>
    <script src="dbms.js"></script>
    <script>
        // Add any additional JavaScript needed for the dashboard
        document.addEventListener('DOMContentLoaded', function() {
            // You can add dashboard-specific JavaScript here
            console.log('Dashboard loaded for user: <?php echo $user["username"]; ?>');
        });
    </script>
</body>
</html>