<?php
session_start();

// Database configuration
$host = "localhost";
$username = "root";
$password = "";
$database = "mediconnect";

// Create connection
$con = mysqli_connect($host, $username, $password, $database);

// Check connection
if(mysqli_connect_errno()) {
    die("Connection failed: " . mysqli_connect_error());
}

// Process form submission
$registration_success = false;
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input data
    $firstName = mysqli_real_escape_string($con, trim($_POST['firstName']));
    $lastName = mysqli_real_escape_string($con, trim($_POST['lastName']));
    $email = mysqli_real_escape_string($con, trim($_POST['email']));
    $phone = mysqli_real_escape_string($con, trim($_POST['phone']));
    $specialization = mysqli_real_escape_string($con, trim($_POST['specialization']));
    $qualification = mysqli_real_escape_string($con, trim($_POST['qualification']));
    $licenseNumber = mysqli_real_escape_string($con, trim($_POST['licenseNumber']));
    $hospital = mysqli_real_escape_string($con, trim($_POST['hospital']));
    $experience = mysqli_real_escape_string($con, $_POST['experience']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Generate unique doctor ID
    $doctor_id = 'DOC' . date('Ymd') . rand(1000, 9999);
    
    // Basic validation
    if (empty($firstName) || empty($lastName) || empty($email) || empty($phone) || 
        empty($specialization) || empty($qualification) || empty($licenseNumber)) {
        $error_message = "Please fill all required fields!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format!";
    } elseif (!preg_match('/^\d{10}$/', $phone)) {
        $error_message = "Phone number must be exactly 10 digits!";
    } else {
        // Check if email or license number already exists
        $check_query = "SELECT id FROM doctors WHERE email = '$email' OR license_number = '$licenseNumber'";
        $check_result = mysqli_query($con, $check_query);
        
        if (mysqli_num_rows($check_result) > 0) {
            $error_message = "Email or License Number already registered!";
        } else {
            // Insert into doctors table
            $query = "INSERT INTO doctors (
                doctor_id, first_name, last_name, email, phone, specialization, 
                qualification, license_number, hospital, experience, password, is_verified, created_at
            ) VALUES (
                '$doctor_id', '$firstName', '$lastName', '$email', '$phone', '$specialization',
                '$qualification', '$licenseNumber', '$hospital', '$experience', '$password', FALSE, NOW()
            )";
            
            if (mysqli_query($con, $query)) {
                // Get the newly created doctor ID
                $doctor_db_id = mysqli_insert_id($con);
                
                // Set session data for immediate login
                $_SESSION['user_id'] = $doctor_db_id;
                $_SESSION['user_type'] = 'doctor';
                $_SESSION['user_email'] = $email;
                $_SESSION['user_name'] = $firstName . ' ' . $lastName;
                
                // Redirect to doctor dashboard
                header("Location: doctor_dashboard.php");
                exit();
            } else {
                $error_message = "Error: " . mysqli_error($con);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Registration - MediConnect</title>
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1e40af;
            --error: #dc2626;
            --success: #16a34a;
            --gray-light: #e5e7eb;
            --gray-dark: #6b7280;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f3f4f6;
            color: #1f2937;
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
        
        .container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .registration-card {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .card-header {
            background-color: var(--primary);
            color: white;
            padding: 1.5rem;
            text-align: center;
        }
        
        .card-body {
            padding: 2rem;
        }
        
        .form-section {
            margin-bottom: 2rem;
        }
        
        .section-title {
            color: var(--primary);
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid var(--gray-light);
            font-size: 1.25rem;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--gray-dark);
        }
        
        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--gray-light);
            border-radius: 0.375rem;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        
        .btn {
            display: inline-block;
            padding: 0.875rem 1.75rem;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 0.375rem;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: var(--primary-dark);
        }
        
        .btn-block {
            display: block;
            width: 100%;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 0.375rem;
            margin-bottom: 1rem;
        }
        
        .alert-error {
            background-color: #fee2e2;
            color: var(--error);
            border: 1px solid #fecaca;
        }
        
        .text-muted {
            color: var(--gray-dark);
            font-size: 0.875rem;
        }
        
        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--gray-light);
        }
        
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .nav-links {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="logo">MediConnect</div>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="about.php">About</a>
            <a href="help.php">Help</a>
            <a href="login.php">Login</a>
        </div>
    </nav>

    <div class="container">
        <div class="registration-card">
            <div class="card-header">
                <h1>Doctor Registration</h1>
                <p>Join MediConnect as a healthcare professional</p>
            </div>
            
            <div class="card-body">
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-error">
                        <strong>Error!</strong> <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>
                
                <form id="registrationForm" method="POST" action="">
                    <!-- Personal Information Section -->
                    <div class="form-section">
                        <h2 class="section-title">Personal Information</h2>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="firstName">First Name*</label>
                                <input type="text" id="firstName" name="firstName" class="form-control" value="<?php echo isset($_POST['firstName']) ? htmlspecialchars($_POST['firstName']) : ''; ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="lastName">Last Name*</label>
                                <input type="text" id="lastName" name="lastName" class="form-control" value="<?php echo isset($_POST['lastName']) ? htmlspecialchars($_POST['lastName']) : ''; ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="email">Email Address*</label>
                                <input type="email" id="email" name="email" class="form-control" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="phone">Phone Number*</label>
                                <input type="tel" id="phone" name="phone" class="form-control" pattern="[0-9]{10}" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Professional Information Section -->
                    <div class="form-section">
                        <h2 class="section-title">Professional Information</h2>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="specialization">Specialization*</label>
                                <select id="specialization" name="specialization" class="form-control" required>
                                    <option value="">Select Specialization</option>
                                    <option value="Cardiology" <?php echo (isset($_POST['specialization']) && $_POST['specialization'] == 'Cardiology') ? 'selected' : ''; ?>>Cardiology</option>
                                    <option value="Dermatology" <?php echo (isset($_POST['specialization']) && $_POST['specialization'] == 'Dermatology') ? 'selected' : ''; ?>>Dermatology</option>
                                    <option value="Pediatrics" <?php echo (isset($_POST['specialization']) && $_POST['specialization'] == 'Pediatrics') ? 'selected' : ''; ?>>Pediatrics</option>
                                    <option value="Orthopedics" <?php echo (isset($_POST['specialization']) && $_POST['specialization'] == 'Orthopedics') ? 'selected' : ''; ?>>Orthopedics</option>
                                    <option value="Neurology" <?php echo (isset($_POST['specialization']) && $_POST['specialization'] == 'Neurology') ? 'selected' : ''; ?>>Neurology</option>
                                    <option value="Gynecology" <?php echo (isset($_POST['specialization']) && $_POST['specialization'] == 'Gynecology') ? 'selected' : ''; ?>>Gynecology</option>
                                    <option value="General Physician" <?php echo (isset($_POST['specialization']) && $_POST['specialization'] == 'General Physician') ? 'selected' : ''; ?>>General Physician</option>
                                    <option value="Dentist" <?php echo (isset($_POST['specialization']) && $_POST['specialization'] == 'Dentist') ? 'selected' : ''; ?>>Dentist</option>
                                    <option value="Psychiatry" <?php echo (isset($_POST['specialization']) && $_POST['specialization'] == 'Psychiatry') ? 'selected' : ''; ?>>Psychiatry</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="qualification">Qualification*</label>
                                <input type="text" id="qualification" name="qualification" class="form-control" placeholder="MBBS, MD, etc." value="<?php echo isset($_POST['qualification']) ? htmlspecialchars($_POST['qualification']) : ''; ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="licenseNumber">Medical License Number*</label>
                                <input type="text" id="licenseNumber" name="licenseNumber" class="form-control" value="<?php echo isset($_POST['licenseNumber']) ? htmlspecialchars($_POST['licenseNumber']) : ''; ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="hospital">Hospital/Clinic</label>
                                <input type="text" id="hospital" name="hospital" class="form-control" value="<?php echo isset($_POST['hospital']) ? htmlspecialchars($_POST['hospital']) : ''; ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="experience">Years of Experience</label>
                            <input type="number" id="experience" name="experience" class="form-control" min="0" max="50" value="<?php echo isset($_POST['experience']) ? htmlspecialchars($_POST['experience']) : '0'; ?>">
                        </div>
                    </div>
                    
                    <!-- Account Security Section -->
                    <div class="form-section">
                        <h2 class="section-title">Account Security</h2>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="password">Create Password*</label>
                                <input type="password" id="password" name="password" class="form-control" required>
                                <small class="text-muted">Minimum 8 characters with 1 uppercase, 1 lowercase, and 1 number</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="confirmPassword">Confirm Password*</label>
                                <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-block">Create Doctor Account</button>
                    </div>
                    
                    <div class="login-link">
                        <p>Already have an account? <a href="login.php">Login here</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            if (password.length < 8) {
                alert('Password must be at least 8 characters long');
                e.preventDefault();
                return;
            }
            
            if (!/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/.test(password)) {
                alert('Password must contain at least one uppercase letter, one lowercase letter, and one number');
                e.preventDefault();
                return;
            }
            
            if (password !== confirmPassword) {
                alert('Passwords do not match');
                e.preventDefault();
                return;
            }
        });
    </script>
</body>
</html>