<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help & Support - MediConnect</title>
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
        .faq-section {
            margin: 4rem 0;
        }
        .faq-section h2 {
            color: var(--primary);
            margin-bottom: 2rem;
            font-size: 2rem;
            text-align: center;
        }
        .faq-item {
            background: white;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border: 1px solid var(--gray);
        }
        .faq-question {
            padding: 1.5rem;
            cursor: pointer;
            display: flex;
            justify-content: between;
            align-items: center;
            font-weight: 600;
        }
        .faq-question::after {
            content: '+';
            font-size: 1.5rem;
            margin-left: auto;
        }
        .faq-question.active::after {
            content: '-';
        }
        .faq-answer {
            padding: 0 1.5rem;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease, padding 0.3s ease;
        }
        .faq-answer.show {
            padding: 0 1.5rem 1.5rem;
            max-height: 500px;
        }
        .contact-section {
            background: var(--light);
            padding: 3rem;
            border-radius: 1rem;
            margin: 4rem 0;
            text-align: center;
        }
        .contact-section h2 {
            color: var(--primary);
            margin-bottom: 1rem;
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
            <h1>Help & Support</h1>
            <p>Find answers to common questions and get the support you need</p>
        </div>
    </div>

    <div class="container">
        <div class="faq-section">
            <h2>Frequently Asked Questions</h2>
            
            <div class="faq-item">
                <div class="faq-question">How do I create an account?</div>
                <div class="faq-answer">
                    <p>Click on the "Register" button in the top navigation or on the home page. Fill in your personal details, contact information, and create a secure password. Once registered, you can immediately start using MediConnect.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">Is my medical data secure?</div>
                <div class="faq-answer">
                    <p>Yes, absolutely. We use enterprise-grade encryption to protect all your medical data. Our system is HIPAA compliant and we regularly conduct security audits to ensure your information remains safe and private.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">How do I book an appointment with a doctor?</div>
                <div class="faq-answer">
                    <p>After logging in, go to your dashboard and click on the "Appointments" tab. From there, you can search for available doctors, select a time slot that works for you, and book your appointment instantly.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">Can I upload my existing medical documents?</div>
                <div class="faq-answer">
                    <p>Yes! In your dashboard, go to the "Documents" section. You can upload various medical documents including lab reports, prescriptions, X-rays, and more. We support PDF, JPG, and PNG formats.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">How do doctors join MediConnect?</div>
                <div class="faq-answer">
                    <p>Doctors can register through our dedicated doctor registration process. We verify all medical credentials and licenses before activating doctor accounts to ensure the highest quality of healthcare providers.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">What if I forget my password?</div>
                <div class="faq-answer">
                    <p>Click on the "Login" button and then select "Forgot Password". Enter your registered email address and we'll send you instructions to reset your password securely.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">Can I access MediConnect on my mobile phone?</div>
                <div class="faq-answer">
                    <p>Yes! MediConnect is fully responsive and works perfectly on all devices including smartphones, tablets, and desktop computers. You can access your health records anytime, anywhere.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">How do I update my personal information?</div>
                <div class="faq-answer">
                    <p>Go to your dashboard and click on "Personal Info". From there, you can edit and update all your personal details, contact information, emergency contacts, and health information.</p>
                </div>
            </div>
        </div>

        <div class="contact-section">
            <h2>Still Need Help?</h2>
            <p>Our support team is here to assist you with any questions or issues you may have.</p>
            <p style="margin: 1rem 0;">Email: support@mediconnect.com</p>
            <p style="margin: 1rem 0;">Phone: +1 (555) 123-HELP</p>
            <p style="margin: 1rem 0;">Available: Monday - Friday, 9 AM - 6 PM</p>
            <a href="contact.php" class="btn">Contact Support</a>
        </div>
    </div>

    <script>
        // FAQ functionality
        document.querySelectorAll('.faq-question').forEach(question => {
            question.addEventListener('click', () => {
                const answer = question.nextElementSibling;
                const isActive = question.classList.contains('active');
                
                // Close all other FAQs
                document.querySelectorAll('.faq-question').forEach(q => {
                    q.classList.remove('active');
                });
                document.querySelectorAll('.faq-answer').forEach(a => {
                    a.classList.remove('show');
                });
                
                // Toggle current FAQ
                if (!isActive) {
                    question.classList.add('active');
                    answer.classList.add('show');
                }
            });
        });

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