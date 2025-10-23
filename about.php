<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About - MediConnect</title>
    <style>
        :root {
            --primary: #2563eb;
            --secondary: #1e40af;
            --text: #1f2937;
            --light: #f9fafb;
            --gray: #e5e7eb;
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
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        .page-header {
            text-align: center;
            padding: 4rem 0 2rem;
            background: linear-gradient(135deg, #e0f2fe, #bae6fd);
        }
        .page-header h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: var(--secondary);
        }
        .page-header p {
            font-size: 1.2rem;
            color: #4b5563;
            max-width: 600px;
            margin: 0 auto;
        }
        .section {
            margin: 4rem 0;
        }
        .section h2 {
            color: var(--primary);
            margin-bottom: 1.5rem;
            font-size: 2rem;
        }
        .section p {
            margin-bottom: 1rem;
            line-height: 1.8;
            color: #4b5563;
        }
        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        .team-card {
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border: 1px solid var(--gray);
        }
        .team-card img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin-bottom: 1rem;
            background: var(--light);
        }
        .btn {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
            text-decoration: none;
        }
        .btn:hover {
            background-color: var(--secondary);
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
            .page-header h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <?php
    $is_logged_in = isset($_SESSION['user_id']);
    $user_type = isset($_SESSION['user_type']) ? $_SESSION['user_type'] : '';
    $user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Guest';
    
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

    <div class="page-header">
        <div class="container">
            <h1>About MediConnect</h1>
            <p>Revolutionizing healthcare through digital innovation and patient-centric solutions</p>
        </div>
    </div>

    <div class="container">
        <div class="section">
            <h2>Our Mission</h2>
            <p>MediConnect was founded with a simple yet powerful mission: to make healthcare management seamless, secure, and accessible for everyone. We believe that every individual should have complete control over their health information and easy access to their medical history.</p>
            <p>In today's fast-paced world, managing health records across different healthcare providers can be challenging. MediConnect solves this by providing a centralized platform where patients and doctors can collaborate effectively.</p>
        </div>

        <div class="section">
            <h2>What We Do</h2>
            <p>MediConnect is a comprehensive digital health record management system that enables:</p>
            <ul style="margin-left: 2rem; margin-bottom: 1rem; color: #4b5563;">
                <li style="margin-bottom: 0.5rem;">Secure storage of medical records and prescriptions</li>
                <li style="margin-bottom: 0.5rem;">Easy appointment scheduling and management</li>
                <li style="margin-bottom: 0.5rem;">Digital prescription management</li>
                <li style="margin-bottom: 0.5rem;">Health tracking and monitoring</li>
                <li style="margin-bottom: 0.5rem;">Seamless doctor-patient communication</li>
                <li style="margin-bottom: 0.5rem;">Medical document storage and sharing</li>
            </ul>
        </div>

        <div class="section">
            <h2>Our Values</h2>
            <p><strong>Security First:</strong> We implement enterprise-grade security measures to protect your sensitive health information.</p>
            <p><strong>Patient-Centric:</strong> Every feature is designed with the patient's needs and convenience in mind.</p>
            <p><strong>Innovation:</strong> We continuously evolve our platform to incorporate the latest healthcare technologies.</p>
            <p><strong>Accessibility:</strong> Making healthcare management available to everyone, regardless of technical expertise.</p>
        </div>

        <div class="section">
            <h2>Why Choose MediConnect?</h2>
            <p>Unlike traditional paper-based records or fragmented digital solutions, MediConnect offers a unified platform that grows with your healthcare needs. Our system is designed to be intuitive for patients while providing powerful tools for healthcare providers.</p>
            <p>With real-time updates, secure messaging, and comprehensive record-keeping, MediConnect ensures that you're always in control of your health journey.</p>
        </div>

        <div class="section" style="text-align: center;">
            <h2>Ready to Get Started?</h2>
            <p>Join thousands of patients and doctors who have transformed their healthcare experience with MediConnect.</p>
            <a href="<?php echo $is_logged_in ? $dashboard_link : 'register.php'; ?>" class="btn">
                <?php echo $is_logged_in ? 'Go to Dashboard' : 'Create Your Account'; ?>
            </a>
        </div>
    </div>

    <script>
        // Mobile menu toggle
        document.querySelector('.mobile-menu-btn').addEventListener('click', function() {
            const navLinks = document.querySelector('.nav-links');
            navLinks.classList.toggle('show');
        });

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