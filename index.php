<?php
session_start();
// Database connection
$con = mysqli_connect("localhost", "root", "", "mediconnect");
if(mysqli_connect_errno()) {
    // Continue without database if not critical for home page
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediConnect - Digital Health Records</title>
    <style>
        :root {
            --primary: #2563eb;
            --secondary: #1e40af;
            --accent: #3b82f6;
            --text: #1f2937;
            --light: #f9fafb;
            --gray: #e5e7eb;
            --success: #16a34a;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            color: var(--text);
            line-height: 1.6;
        }
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 5%;
            background-color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }
        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
        }
        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }
        .nav-links a {
            color: var(--text);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }
        .nav-links a:hover {
            color: var(--primary);
        }
        .user-badge {
            background: var(--primary);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-size: 0.875rem;
        }
        .hero {
            padding: 8rem 5% 5rem;
            text-align: center;
            background: linear-gradient(135deg, #e0f2fe, #bae6fd);
            min-height: 80vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .hero h1 {
            font-size: 3rem;
            margin-bottom: 1.5rem;
            color: var(--secondary);
        }
        .hero p {
            max-width: 700px;
            margin: 0 auto 2rem;
            font-size: 1.2rem;
            color: #4b5563;
        }
        .btn {
            display: inline-block;
            padding: 1rem 2rem;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            font-size: 1.1rem;
        }
        .btn:hover {
            background-color: var(--secondary);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.2);
        }
        .btn-outline {
            background: transparent;
            border: 2px solid var(--primary);
            color: var(--primary);
            margin-left: 1rem;
        }
        .btn-outline:hover {
            background: var(--primary);
            color: white;
        }
        .features {
            padding: 5rem 5%;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            background: white;
        }
        .feature-card {
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            transition: transform 0.3s, box-shadow 0.3s;
            border: 1px solid var(--gray);
            text-align: center;
        }
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        .feature-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        .feature-card h3 {
            color: var(--primary);
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }
        .feature-card p {
            color: #6b7280;
            line-height: 1.8;
        }
        .stats {
            padding: 4rem 5%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            text-align: center;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        .stat-item h3 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        .cta {
            padding: 5rem 5%;
            text-align: center;
            background: var(--light);
        }
        .cta h2 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: var(--text);
        }
        .footer {
            background: var(--text);
            color: white;
            padding: 3rem 5%;
            text-align: center;
        }
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            color: var(--primary);
            font-size: 1.5rem;
            cursor: pointer;
        }
        @media (max-width: 768px) {
            .nav-links {
                display: none;
                flex-direction: column;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background-color: white;
                padding: 1rem;
                box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            }
            .nav-links.show {
                display: flex;
            }
            .mobile-menu-btn {
                display: block;
            }
            .hero {
                padding: 6rem 5% 3rem;
            }
            .hero h1 {
                font-size: 2rem;
            }
            .btn-container {
                display: flex;
                flex-direction: column;
                gap: 1rem;
            }
            .btn-outline {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <?php
    $is_logged_in = isset($_SESSION['user_id']);
    $user_type = isset($_SESSION['user_type']) ? $_SESSION['user_type'] : '';
    $user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Guest';
    
    // Determine dashboard link based on user type
    $dashboard_link = $is_logged_in ? 
        ($user_type === 'doctor' ? 'doctor_dashboard.php' : 'dashboard.php') : 
        'login.php';
    ?>

    <nav class="navbar">
        <div class="logo">MediConnect</div>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="about.php">About</a>
            <a href="help.php">Help</a>
            <?php if ($is_logged_in): ?>
                <a href="<?php echo $dashboard_link; ?>">Dashboard</a>
                <div class="user-badge">
                    <?php 
                    echo htmlspecialchars($user_name);
                    if ($user_type === 'doctor') {
                        echo ' (Doctor)';
                    }
                    ?>
                </div>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </div>
        <button class="mobile-menu-btn">☰</button>
    </nav>

    <section class="hero">
        <h1>Your Complete Digital Health Solution</h1>
        <p>MediConnect revolutionizes healthcare management by providing a secure, comprehensive platform for patients and doctors to manage medical records, appointments, and treatments seamlessly.</p>
        <div class="btn-container">
            <a href="<?php echo $is_logged_in ? $dashboard_link : 'login.php'; ?>" class="btn">
                <?php echo $is_logged_in ? 'Go to Dashboard' : 'Get Started as Patient'; ?>
            </a>
            <?php if (!$is_logged_in): ?>
                <a href="login.php?type=doctor" class="btn btn-outline">I'm a Doctor</a>
            <?php endif; ?>
        </div>
    </section>

    <section class="features">
        <div class="feature-card">
            <div class="feature-icon">🔒</div>
            <h3>Secure Health Records</h3>
            <p>Enterprise-grade encryption protects your sensitive medical data. HIPAA compliant with secure cloud storage and regular backups.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">🌐</div>
            <h3>24/7 Access Anywhere</h3>
            <p>Access your complete medical history from any device. Your health records are always available when you need them most.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">📊</div>
            <h3>Comprehensive Tracking</h3>
            <p>Track treatments, medications, lab reports, appointments, and health metrics in one centralized platform.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">👨‍⚕️</div>
            <h3>Doctor Collaboration</h3>
            <p>Seamless communication between patients and healthcare providers. Share records securely with your doctors.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">📱</div>
            <h3>Mobile Friendly</h3>
            <p>Fully responsive design works perfectly on desktop, tablet, and mobile devices for healthcare on the go.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">⚡</div>
            <h3>Instant Updates</h3>
            <p>Real-time updates for appointments, prescriptions, and medical records. Never miss important health information.</p>
        </div>
    </section>

    <section class="stats">
        <div class="stats-grid">
            <div class="stat-item">
                <h3>10,000+</h3>
                <p>Patients Served</p>
            </div>
            <div class="stat-item">
                <h3>500+</h3>
                <p>Verified Doctors</p>
            </div>
            <div class="stat-item">
                <h3>50,000+</h3>
                <p>Health Records</p>
            </div>
            <div class="stat-item">
                <h3>99.9%</h3>
                <p>Uptime Reliability</p>
            </div>
        </div>
    </section>

    <section class="cta">
        <h2>Ready to Transform Your Healthcare Experience?</h2>
        <p>Join thousands of patients and doctors who trust MediConnect for their healthcare management needs.</p>
        <div style="margin-top: 2rem;">
            <a href="register.php" class="btn">Create Your Account</a>
            <a href="about.php" class="btn btn-outline">Learn More</a>
        </div>
    </section>

    <footer class="footer">
        <p>&copy; 2024 MediConnect. All rights reserved.</p>
        <p style="margin-top: 1rem; opacity: 0.8;">Secure Digital Health Record Management System</p>
        <div style="margin-top: 2rem;">
            <a href="privacy.php" style="color: white; margin: 0 1rem;">Privacy Policy</a>
            <a href="terms.php" style="color: white; margin: 0 1rem;">Terms of Service</a>
            <a href="contact.php" style="color: white; margin: 0 1rem;">Contact Us</a>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        document.querySelector('.mobile-menu-btn').addEventListener('click', function() {
            const navLinks = document.querySelector('.nav-links');
            navLinks.classList.toggle('show');
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const navLinks = document.querySelector('.nav-links');
            const mobileBtn = document.querySelector('.mobile-menu-btn');
            if (!navLinks.contains(event.target) && !mobileBtn.contains(event.target)) {
                navLinks.classList.remove('show');
            }
        });
    </script>
</body>
</html>