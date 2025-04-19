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
    <title>Sushop - Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
</head>
<body>
    <div class="background-blur"></div>
    
    <nav class="glass-nav">
        <a href="/" class="logo-container">
            <img src="https://i.imgur.com/Jx7A7XW.png" alt="Sushop Logo" class="logo">
            <span class="logo-text">Sushop</span>
        </a>
        
        <div class="nav-country">
            <i class="fa-solid fa-location-dot"></i>
            <div>
                <p>Deliver to</p>
                <h1>India</h1>
            </div>
        </div>
        
        <div class="nav-search glass-input">
            <div class="nav-search-category">
                <p>All</p>
                <i class="fa-solid fa-chevron-down"></i>
            </div>
            <input type="text" class="nav-search-input" placeholder="Search Sushop">
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
            <div class="user-dropdown glass-dropdown">
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
            <div class="cart-icon-container">
                <i class="fa-solid fa-cart-shopping"></i>
                <span class="cart-count">0</span>
            </div>
            <h4>Cart</h4>
        </a>
    </nav>
    
    <div class="nav-bottom glass-nav-bottom">
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

    <div class="welcome-banner glass-card animate__animated animate__fadeIn">
        <div class="welcome-content">
            <h2>Welcome back, <?php echo htmlspecialchars($user['username']); ?>!</h2>
            <p>Explore the best deals and exclusive offers at Sushop!</p>
            <div class="welcome-icons">
                <i class="fas fa-gift pulse"></i>
                <i class="fas fa-tag pulse"></i>
                <i class="fas fa-star pulse"></i>
            </div>
        </div>
        <div class="welcome-image">
            <img src="https://i.imgur.com/9QZ2zJj.jpg" alt="Welcome Image" onerror="this.src='https://i.imgur.com/placeholder.jpg'">
        </div>
    </div>

    <div class="account-section glass-card">
        <h2><i class="fas fa-user-circle"></i> Account Information</h2>
        <div class="account-details">
            <div class="detail-item">
                <i class="fas fa-user"></i>
                <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
            </div>
            <div class="detail-item">
                <i class="fas fa-envelope"></i>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            </div>
            <div class="detail-item">
                <i class="fas fa-calendar-alt"></i>
                <p><strong>Member since:</strong> <?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>
            </div>
        </div>
        <a href="settings.php" class="btn purple-gradient"><i class="fa-solid fa-edit"></i> Edit Account</a>
    </div>

    <div class="header-slider animate__animated animate__fadeInUp">
        <a href="#" class="control_prev"><i class="fa-solid fa-chevron-left"></i></a>
        <a href="#" class="control_next"><i class="fa-solid fa-chevron-right"></i></a>
        <ul>
            <li><img src="https://i.imgur.com/abc123.jpg" class="header-img" alt="Electronics" onerror="this.src='https://i.imgur.com/placeholder.jpg'"></li>
            <li><img src="https://i.imgur.com/def456.jpg" class="header-img" alt="Stationery" onerror="this.src='https://i.imgur.com/placeholder.jpg'"></li>
            <li><img src="https://i.imgur.com/ghi789.jpg" class="header-img" alt="Camera" onerror="this.src='https://i.imgur.com/placeholder.jpg'"></li>
            <li><img src="https://i.imgur.com/jkl012.jpg" class="header-img" alt="Watch" onerror="this.src='https://i.imgur.com/placeholder.jpg'"></li>
            <li><img src="https://i.imgur.com/mno345.jpg" class="header-img" alt="Smartwatch" onerror="this.src='https://i.imgur.com/placeholder.jpg'"></li>
        </ul>
        <div class="slider-dots"></div>
    </div>

    <div class="shop-section">
        <?php
        $stmt = $pdo->prepare("SELECT * FROM products LIMIT 8");
        $stmt->execute();
        $products = $stmt->fetchAll();
        
        if (empty($products)) {
            $products = [
                ['id' => 1, 'name' => 'Wireless Headphones', 'price' => 99.99, 'image_url' => 'https://i.imgur.com/headphones.jpg'],
                ['id' => 2, 'name' => 'Smart Watch', 'price' => 199.99, 'image_url' => 'https://i.imgur.com/smartwatch.jpg'],
                ['id' => 3, 'name' => 'Bluetooth Speaker', 'price' => 79.99, 'image_url' => 'https://i.imgur.com/speaker.jpg'],
                ['id' => 4, 'name' => 'Coffee Maker', 'price' => 129.99, 'image_url' => 'https://i.imgur.com/coffeemaker.jpg'],
                ['id' => 5, 'name' => 'Running Shoes', 'price' => 89.99, 'image_url' => 'https://i.imgur.com/shoes.jpg'],
                ['id' => 6, 'name' => 'Backpack', 'price' => 49.99, 'image_url' => 'https://i.imgur.com/backpack.jpg'],
                ['id' => 7, 'name' => 'Sunglasses', 'price' => 59.99, 'image_url' => 'https://i.imgur.com/sunglasses.jpg'],
                ['id' => 8, 'name' => 'Smartphone', 'price' => 699.99, 'image_url' => 'https://i.imgur.com/smartphone.jpg']
            ];
        }
        
        foreach ($products as $product): ?>
            <div class="box glass-card animate__animated animate__fadeInUp">
                <div class="box-content">
                    <div class="box-header">
                        <h2><?php echo htmlspecialchars($product['name']); ?></h2>
                        <div class="wishlist-icon">
                            <i class="far fa-heart"></i>
                        </div>
                    </div>
                    <div class="box-img" style="background-image: url('<?php echo htmlspecialchars($product['image_url']); ?>');">
                        <form method="post" action="cart.php" class="add-to-cart-form">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <button type="submit" name="add_to_cart" class="add-to-cart-btn purple-gradient">
                                <i class="fa-solid fa-cart-plus"></i> Add to Cart
                            </button>
                        </form>
                    </div>
                    <div class="box-footer">
                        <p class="price">₹<?php echo number_format($product['price'], 2); ?></p>
                        <div class="rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="far fa-star"></i>
                            <span>(24)</span>
                        </div>
                        <a href="#" class="see-more">View Details <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="promo-banner glass-card purple-gradient">
        <div class="promo-content">
            <h2>Exclusive Member Deals</h2>
            <p>Enjoy 30% off on selected items with code: PURPLE30</p>
            <a href="#" class="btn white-btn">Shop Now</a>
        </div>
        <div class="promo-image">
            <img src="https://i.imgur.com/promo123.jpg" alt="Promo Image" onerror="this.src='https://i.imgur.com/placeholder.jpg'">
        </div>
    </div>

    <div class="categories-section">
        <h2 class="section-title"><i class="fas fa-th-large"></i> Shop by Categories</h2>
        <div class="categories-grid">
            <div class="category-card glass-card" style="background-image: linear-gradient(rgba(102, 45, 145, 0.7), rgba(102, 45, 145, 0.7)), url('https://i.imgur.com/electronics.jpg')">
                <h3>Electronics</h3>
                <p>200+ Products</p>
                <a href="#">Shop Now <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="category-card glass-card" style="background-image: linear-gradient(rgba(102, 45, 145, 0.7), rgba(102, 45, 145, 0.7)), url('https://i.imgur.com/fashion.jpg')">
                <h3>Fashion</h3>
                <p>500+ Products</p>
                <a href="#">Shop Now <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="category-card glass-card" style="background-image: linear-gradient(rgba(102, 45, 145, 0.7), rgba(102, 45, 145, 0.7)), url('https://i.imgur.com/homekitchen.jpg')">
                <h3>Home & Kitchen</h3>
                <p>350+ Products</p>
                <a href="#">Shop Now <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="category-card glass-card" style="background-image: linear-gradient(rgba(102, 45, 145, 0.7), rgba(102, 45, 145, 0.7)), url('https://i.imgur.com/groceries.jpg')">
                <h3>Groceries</h3>
                <p>1000+ Products</p>
                <a href="#">Shop Now <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <div class="testimonials-section">
        <h2 class="section-title"><i class="fas fa-quote-left"></i> What Our Customers Say</h2>
        <div class="testimonials-container">
            <div class="testimonial-card glass-card">
                <div class="testimonial-header">
                    <img src="customer1.jpg" alt="Customer" onerror="this.src='https://i.imgur.com/placeholder.jpg'">
                    <div>
                        <h4>JOHANAZ NADAF</h4>
                        <div class="rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                </div>
                <p>"Sushop has the best deals and fastest delivery! I'm always impressed with their service."</p>
            </div>
            <div class="testimonial-card glass-card">
                <div class="testimonial-header">
                    <img src="customer2.jpg" alt="Customer" onerror="this.src='https://i.imgur.com/placeholder.jpg'">
                    <div>
                        <h4>Surabhi M R</h4>
                        <div class="rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="far fa-star"></i>
                        </div>
                    </div>
                </div>
                <p>"Great selection of products and excellent customer support. Highly recommended!"</p>
            </div>
            <div class="testimonial-card glass-card">
                <div class="testimonial-header">
                    <img src="https://i.imgur.com/customer3.jpg" alt="Customer" onerror="this.src='https://i.imgur.com/placeholder.jpg'">
                    <div>
                        <h4>Arfa</h4>
                        <div class="rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                    </div>
                </div>
                <p>"I love the user interface and how easy it is to find what I'm looking for. 5 stars!"</p>
            </div>
        </div>
    </div>

    <footer class="glass-footer">
        <div class="footer-top">
            <div class="footer-column">
                <h3>Get to Know Us</h3>
                <ul>
                    <li><a href="#">About Sushop</a></li>
                    <li><a href="#">Careers</a></li>
                    <li><a href="#">Press Releases</a></li>
                    <li><a href="#">Sushop Science</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h3>Make Money with Us</h3>
                <ul>
                    <li><a href="#">Sell products</a></li>
                    <li><a href="#">Become an Affiliate</a></li>
                    <li><a href="#">Advertise Your Products</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h3>Payment Products</h3>
                <ul>
                    <li><a href="#">Sushop Business Card</a></li>
                    <li><a href="#">Shop with Points</a></li>
                    <li><a href="#">Reload Your Balance</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h3>Let Us Help You</h3>
                <ul>
                    <li><a href="#">Your Account</a></li>
                    <li><a href="#">Your Orders</a></li>
                    <li><a href="#">Shipping Rates</a></li>
                    <li><a href="#">Returns & Replacements</a></li>
                    <li><a href="#">Help</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-middle">
            <div class="footer-logo">
                <img src="https://i.imgur.com/Jx7A7XW.png" alt="Sushop Logo" onerror="this.src='https://i.imgur.com/placeholder.jpg'">
                <span>Sushop</span>
            </div>
            <div class="footer-social">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-pinterest"></i></a>
                <a href="#"><i class="fab fa-youtube"></i></a>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="footer-links">
                <a href="#">Conditions of Use</a>
                <a href="#">Privacy Notice</a>
                <a href="#">Interest-Based Ads</a>
            </div>
            <div class="copyright">
                © 2025, Sushop.com, Inc. or its affiliates
            </div>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/gsap.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Dashboard loaded for user: <?php echo $user["username"]; ?>');
            
            // Slider functionality
            const slider = document.querySelector('.header-slider ul');
            const slides = document.querySelectorAll('.header-slider li');
            const prevBtn = document.querySelector('.control_prev');
            const nextBtn = document.querySelector('.control_next');
            const dotsContainer = document.querySelector('.slider-dots');
            let currentIndex = 0;
            
            // Create dots
            slides.forEach((slide, index) => {
                const dot = document.createElement('span');
                dot.classList.add('dot');
                if (index === 0) dot.classList.add('active');
                dot.addEventListener('click', () => {
                    currentIndex = index;
                    showSlide(currentIndex);
                    updateDots();
                });
                dotsContainer.appendChild(dot);
            });
            
            function showSlide(index) {
                gsap.to(slider, {
                    x: `-${index * 100}%`,
                    duration: 0.5,
                    ease: "power2.out"
                });
            }
            
            function updateDots() {
                document.querySelectorAll('.dot').forEach((dot, index) => {
                    if (index === currentIndex) {
                        dot.classList.add('active');
                    } else {
                        dot.classList.remove('active');
                    }
                });
            }
            
            prevBtn.addEventListener('click', (e) => {
                e.preventDefault();
                currentIndex = (currentIndex > 0) ? currentIndex - 1 : slides.length - 1;
                showSlide(currentIndex);
                updateDots();
            });
            
            nextBtn.addEventListener('click', (e) => {
                e.preventDefault();
                currentIndex = (currentIndex < slides.length - 1) ? currentIndex + 1 : 0;
                showSlide(currentIndex);
                updateDots();
            });
            
            // Auto slide
            let slideInterval = setInterval(() => {
                currentIndex = (currentIndex < slides.length - 1) ? currentIndex + 1 : 0;
                showSlide(currentIndex);
                updateDots();
            }, 5000);
            
            // Pause on hover
            slider.addEventListener('mouseenter', () => {
                clearInterval(slideInterval);
            });
            
            slider.addEventListener('mouseleave', () => {
                slideInterval = setInterval(() => {
                    currentIndex = (currentIndex < slides.length - 1) ? currentIndex + 1 : 0;
                    showSlide(currentIndex);
                    updateDots();
                }, 5000);
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
            
            // Wishlist toggle
            document.querySelectorAll('.wishlist-icon').forEach(icon => {
                icon.addEventListener('click', function() {
                    this.querySelector('i').classList.toggle('far');
                    this.querySelector('i').classList.toggle('fas');
                    this.querySelector('i').classList.toggle('active');
                    
                    if (this.querySelector('i').classList.contains('active')) {
                        gsap.to(this, {
                            scale: 1.2,
                            duration: 0.3,
                            onComplete: function() {
                                gsap.to(this.target, {
                                    scale: 1,
                                    duration: 0.2
                                });
                            }
                        });
                    }
                });
            });
            
            // Add hover effects to cards
            document.querySelectorAll('.box').forEach(box => {
                box.addEventListener('mouseenter', function() {
                    gsap.to(this, {
                        y: -10,
                        duration: 0.3,
                        boxShadow: '0 15px 30px rgba(102, 45, 145, 0.3)'
                    });
                });
                
                box.addEventListener('mouseleave', function() {
                    gsap.to(this, {
                        y: 0,
                        duration: 0.3,
                        boxShadow: '0 4px 10px rgba(0, 0, 0, 0.3)'
                    });
                });
            });
            
            // Animate elements on scroll
            const animateOnScroll = function() {
                const elements = document.querySelectorAll('.animate-on-scroll');
                
                elements.forEach(element => {
                    const elementPosition = element.getBoundingClientRect().top;
                    const windowHeight = window.innerHeight;
                    
                    if (elementPosition < windowHeight - 100) {
                        element.classList.add('animate__animated', 'animate__fadeInUp');
                    }
                });
            };
            
            window.addEventListener('scroll', animateOnScroll);
            animateOnScroll(); // Run once on load

            // Handle image errors
            document.querySelectorAll('img').forEach(img => {
                img.addEventListener('error', function() {
                    this.src = 'https://i.imgur.com/placeholder.jpg';
                    this.alt = 'Placeholder image';
                });
            });
        });
    </script>
</body>
</html>