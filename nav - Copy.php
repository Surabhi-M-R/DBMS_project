<!-- nav.php -->
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
        <!-- In the user dropdown menu, make sure you have: -->
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
        <?php 
        // Display cart count
        if (isset($_SESSION['user_id'])) {
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM cart WHERE user_id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $cart_count = $stmt->fetch()['count'];
            if ($cart_count > 0) {
                echo '<span class="cart-count">' . $cart_count . '</span>';
            }
        }
        ?>
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