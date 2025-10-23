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
    $middleName = mysqli_real_escape_string($con, trim($_POST['middleName']));
    $lastName = mysqli_real_escape_string($con, trim($_POST['lastName']));
    $aadhaar = mysqli_real_escape_string($con, trim($_POST['aadhaar']));
    $dob = mysqli_real_escape_string($con, $_POST['dob']);
    $gender = mysqli_real_escape_string($con, $_POST['gender']);
    $maritalStatus = mysqli_real_escape_string($con, $_POST['maritalStatus']);
    $nationality = mysqli_real_escape_string($con, $_POST['nationality']);
    $address = mysqli_real_escape_string($con, trim($_POST['address']));
    $phone = mysqli_real_escape_string($con, trim($_POST['phone']));
    $email = mysqli_real_escape_string($con, trim($_POST['email']));
    $weight = !empty($_POST['weight']) ? mysqli_real_escape_string($con, $_POST['weight']) : NULL;
    $height = !empty($_POST['height']) ? mysqli_real_escape_string($con, $_POST['height']) : NULL;
    $bloodGroup = mysqli_real_escape_string($con, $_POST['bloodGroup']);
    $allergies = mysqli_real_escape_string($con, trim($_POST['allergies']));
    $currentMeds = mysqli_real_escape_string($con, trim($_POST['currentMeds']));
    $hasInsurance = mysqli_real_escape_string($con, $_POST['hasInsurance']);
    $insuranceProvider = mysqli_real_escape_string($con, trim($_POST['insuranceProvider']));
    $policyNumber = mysqli_real_escape_string($con, trim($_POST['policyNumber']));
    $groupNumber = mysqli_real_escape_string($con, trim($_POST['groupNumber']));
    $insurancePhone = mysqli_real_escape_string($con, trim($_POST['insurancePhone']));
    $emergencyName1 = mysqli_real_escape_string($con, trim($_POST['emergencyName1']));
    $emergencyRelation1 = mysqli_real_escape_string($con, $_POST['emergencyRelation1']);
    $emergencyPhone1 = mysqli_real_escape_string($con, trim($_POST['emergencyPhone1']));
    $emergencyEmail1 = mysqli_real_escape_string($con, trim($_POST['emergencyEmail1']));
    $emergencyName2 = mysqli_real_escape_string($con, trim($_POST['emergencyName2']));
    $emergencyRelation2 = mysqli_real_escape_string($con, $_POST['emergencyRelation2']);
    $emergencyPhone2 = mysqli_real_escape_string($con, trim($_POST['emergencyPhone2']));
    $emergencyEmail2 = mysqli_real_escape_string($con, trim($_POST['emergencyEmail2']));
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Basic validation
    if (empty($firstName) || empty($lastName) || empty($aadhaar) || empty($dob) || 
        empty($gender) || empty($address) || empty($phone) || empty($email) || 
        empty($emergencyName1) || empty($emergencyRelation1) || empty($emergencyPhone1)) {
        $error_message = "Please fill all required fields!";
    } elseif (!preg_match('/^\d{12}$/', $aadhaar)) {
        $error_message = "Aadhaar must be exactly 12 digits!";
    } elseif (!preg_match('/^\d{10}$/', $phone)) {
        $error_message = "Phone number must be exactly 10 digits!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format!";
    } else {
        // Check if email or Aadhaar already exists
        $check_query = "SELECT id FROM patients WHERE email = '$email' OR aadhaar = '$aadhaar'";
        $check_result = mysqli_query($con, $check_query);
        
        if (mysqli_num_rows($check_result) > 0) {
            $error_message = "Email or Aadhaar number already registered!";
        } else {
            // Insert into patients table
            $query = "INSERT INTO patients (
                first_name, middle_name, last_name, aadhaar, dob, gender, marital_status, 
                nationality, address, phone, email, weight, height, blood_group, allergies, 
                current_medications, has_insurance, insurance_provider, policy_number, 
                group_number, insurance_phone, emergency_name1, emergency_relation1, 
                emergency_phone1, emergency_email1, emergency_name2, emergency_relation2, 
                emergency_phone2, emergency_email2, password
            ) VALUES (
                '$firstName', '$middleName', '$lastName', '$aadhaar', '$dob', '$gender', 
                '$maritalStatus', '$nationality', '$address', '$phone', '$email', '$weight', 
                '$height', '$bloodGroup', '$allergies', '$currentMeds', '$hasInsurance', 
                '$insuranceProvider', '$policyNumber', '$groupNumber', '$insurancePhone', 
                '$emergencyName1', '$emergencyRelation1', '$emergencyPhone1', '$emergencyEmail1', 
                '$emergencyName2', '$emergencyRelation2', '$emergencyPhone2', '$emergencyEmail2', 
                '$password'
            )";
            
            if (mysqli_query($con, $query)) {
                // Get the newly created user ID
                $user_id = mysqli_insert_id($con);
                
                // Set session data for unified system
                $_SESSION['user_id'] = $user_id;
                $_SESSION['user_type'] = 'patient';
                $_SESSION['user_email'] = $email;
                $_SESSION['user_name'] = $firstName . ' ' . $lastName;
                
                // Redirect to dashboard
                header("Location: dashboard.php");
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
    <title>Patient Registration - MediConnect</title>
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
            max-width: 1000px;
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
            margin-bottom: 2.5rem;
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
        
        .radio-group {
            display: flex;
            gap: 1.5rem;
            margin-top: 0.5rem;
        }
        
        .radio-option {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        textarea.form-control {
            min-height: 100px;
            resize: vertical;
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
        
        .emergency-contact-card {
            background: #f8fafc;
            padding: 1.5rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid var(--gray-light);
        }
        
        .contact-title {
            font-size: 1rem;
            margin-bottom: 1rem;
            color: var(--primary);
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
            
            .radio-group {
                flex-direction: column;
                gap: 0.5rem;
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
                <h1>Create Your MediConnect Account</h1>
                <p>Secure your health records in one place</p>
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
                                <label for="middleName">Middle Name</label>
                                <input type="text" id="middleName" name="middleName" class="form-control" value="<?php echo isset($_POST['middleName']) ? htmlspecialchars($_POST['middleName']) : ''; ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="lastName">Last Name*</label>
                                <input type="text" id="lastName" name="lastName" class="form-control" value="<?php echo isset($_POST['lastName']) ? htmlspecialchars($_POST['lastName']) : ''; ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="aadhaar">Aadhaar Number*</label>
                                <input type="text" id="aadhaar" name="aadhaar" class="form-control" pattern="\d{12}" placeholder="Enter 12 digits" value="<?php echo isset($_POST['aadhaar']) ? htmlspecialchars($_POST['aadhaar']) : ''; ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="dob">Date of Birth*</label>
                                <input type="date" id="dob" name="dob" class="form-control" value="<?php echo isset($_POST['dob']) ? htmlspecialchars($_POST['dob']) : ''; ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label>Gender*</label>
                                <div class="radio-group">
                                    <label class="radio-option">
                                        <input type="radio" name="gender" value="male" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'male') ? 'checked' : ''; ?> required> Male
                                    </label>
                                    <label class="radio-option">
                                        <input type="radio" name="gender" value="female" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'female') ? 'checked' : ''; ?>> Female
                                    </label>
                                    <label class="radio-option">
                                        <input type="radio" name="gender" value="other" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'other') ? 'checked' : ''; ?>> Other
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="maritalStatus">Marital Status</label>
                                <select id="maritalStatus" name="maritalStatus" class="form-control">
                                    <option value="">Select</option>
                                    <option value="single" <?php echo (isset($_POST['maritalStatus']) && $_POST['maritalStatus'] == 'single') ? 'selected' : ''; ?>>Single</option>
                                    <option value="married" <?php echo (isset($_POST['maritalStatus']) && $_POST['maritalStatus'] == 'married') ? 'selected' : ''; ?>>Married</option>
                                    <option value="divorced" <?php echo (isset($_POST['maritalStatus']) && $_POST['maritalStatus'] == 'divorced') ? 'selected' : ''; ?>>Divorced</option>
                                    <option value="widowed" <?php echo (isset($_POST['maritalStatus']) && $_POST['maritalStatus'] == 'widowed') ? 'selected' : ''; ?>>Widowed</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="nationality">Nationality</label>
                                <input type="text" id="nationality" name="nationality" class="form-control" value="Indian" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Contact Information Section -->
                    <div class="form-section">
                        <h2 class="section-title">Contact Information</h2>
                        
                        <div class="form-group">
                            <label for="address">Complete Address*</label>
                            <textarea id="address" name="address" class="form-control" required><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
                        </div>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="phone">Contact Number*</label>
                                <input type="tel" id="phone" name="phone" class="form-control" pattern="[0-9]{10}" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email Address*</label>
                                <input type="email" id="email" name="email" class="form-control" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Health Information Section -->
                    <div class="form-section">
                        <h2 class="section-title">Health Information</h2>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="weight">Weight (kg)</label>
                                <input type="number" id="weight" name="weight" class="form-control" step="0.1" min="0" value="<?php echo isset($_POST['weight']) ? htmlspecialchars($_POST['weight']) : ''; ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="height">Height (cm)</label>
                                <input type="number" id="height" name="height" class="form-control" min="0" value="<?php echo isset($_POST['height']) ? htmlspecialchars($_POST['height']) : ''; ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="bloodGroup">Blood Group</label>
                                <select id="bloodGroup" name="bloodGroup" class="form-control">
                                    <option value="">Select</option>
                                    <option value="A+" <?php echo (isset($_POST['bloodGroup']) && $_POST['bloodGroup'] == 'A+') ? 'selected' : ''; ?>>A+</option>
                                    <option value="A-" <?php echo (isset($_POST['bloodGroup']) && $_POST['bloodGroup'] == 'A-') ? 'selected' : ''; ?>>A-</option>
                                    <option value="B+" <?php echo (isset($_POST['bloodGroup']) && $_POST['bloodGroup'] == 'B+') ? 'selected' : ''; ?>>B+</option>
                                    <option value="B-" <?php echo (isset($_POST['bloodGroup']) && $_POST['bloodGroup'] == 'B-') ? 'selected' : ''; ?>>B-</option>
                                    <option value="AB+" <?php echo (isset($_POST['bloodGroup']) && $_POST['bloodGroup'] == 'AB+') ? 'selected' : ''; ?>>AB+</option>
                                    <option value="AB-" <?php echo (isset($_POST['bloodGroup']) && $_POST['bloodGroup'] == 'AB-') ? 'selected' : ''; ?>>AB-</option>
                                    <option value="O+" <?php echo (isset($_POST['bloodGroup']) && $_POST['bloodGroup'] == 'O+') ? 'selected' : ''; ?>>O+</option>
                                    <option value="O-" <?php echo (isset($_POST['bloodGroup']) && $_POST['bloodGroup'] == 'O-') ? 'selected' : ''; ?>>O-</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="allergies">Known Allergies</label>
                            <input type="text" id="allergies" name="allergies" class="form-control" placeholder="Separate with commas" value="<?php echo isset($_POST['allergies']) ? htmlspecialchars($_POST['allergies']) : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="currentMeds">Current Medications</label>
                            <input type="text" id="currentMeds" name="currentMeds" class="form-control" placeholder="Separate with commas" value="<?php echo isset($_POST['currentMeds']) ? htmlspecialchars($_POST['currentMeds']) : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>Do you have health insurance?*</label>
                            <div class="radio-group">
                                <label class="radio-option">
                                    <input type="radio" name="hasInsurance" value="yes" <?php echo (isset($_POST['hasInsurance']) && $_POST['hasInsurance'] == 'yes') ? 'checked' : ''; ?>> Yes
                                </label>
                                <label class="radio-option">
                                    <input type="radio" name="hasInsurance" value="no" <?php echo (!isset($_POST['hasInsurance']) || $_POST['hasInsurance'] == 'no') ? 'checked' : ''; ?>> No
                                </label>
                            </div>
                        </div>
                        
                        <div class="insurance-details" id="insuranceDetails" style="display: none;">
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="insuranceProvider">Insurance Provider</label>
                                    <input type="text" id="insuranceProvider" name="insuranceProvider" class="form-control" value="<?php echo isset($_POST['insuranceProvider']) ? htmlspecialchars($_POST['insuranceProvider']) : ''; ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label for="policyNumber">Policy Number</label>
                                    <input type="text" id="policyNumber" name="policyNumber" class="form-control" value="<?php echo isset($_POST['policyNumber']) ? htmlspecialchars($_POST['policyNumber']) : ''; ?>">
                                </div>
                            </div>
                            
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="groupNumber">Group Number</label>
                                    <input type="text" id="groupNumber" name="groupNumber" class="form-control" value="<?php echo isset($_POST['groupNumber']) ? htmlspecialchars($_POST['groupNumber']) : ''; ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label for="insurancePhone">Insurance Phone</label>
                                    <input type="tel" id="insurancePhone" name="insurancePhone" class="form-control" pattern="[0-9]{10}" value="<?php echo isset($_POST['insurancePhone']) ? htmlspecialchars($_POST['insurancePhone']) : ''; ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Emergency Contacts Section -->
                    <div class="form-section">
                        <h2 class="section-title">Emergency Contacts</h2>
                        
                        <div class="emergency-contact-card">
                            <h3 class="contact-title">Primary Emergency Contact*</h3>
                            
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="emergencyName1">Full Name*</label>
                                    <input type="text" id="emergencyName1" name="emergencyName1" class="form-control" value="<?php echo isset($_POST['emergencyName1']) ? htmlspecialchars($_POST['emergencyName1']) : ''; ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="emergencyRelation1">Relationship*</label>
                                    <select id="emergencyRelation1" name="emergencyRelation1" class="form-control" required>
                                        <option value="">Select</option>
                                        <option value="spouse" <?php echo (isset($_POST['emergencyRelation1']) && $_POST['emergencyRelation1'] == 'spouse') ? 'selected' : ''; ?>>Spouse</option>
                                        <option value="parent" <?php echo (isset($_POST['emergencyRelation1']) && $_POST['emergencyRelation1'] == 'parent') ? 'selected' : ''; ?>>Parent</option>
                                        <option value="child" <?php echo (isset($_POST['emergencyRelation1']) && $_POST['emergencyRelation1'] == 'child') ? 'selected' : ''; ?>>Child</option>
                                        <option value="sibling" <?php echo (isset($_POST['emergencyRelation1']) && $_POST['emergencyRelation1'] == 'sibling') ? 'selected' : ''; ?>>Sibling</option>
                                        <option value="friend" <?php echo (isset($_POST['emergencyRelation1']) && $_POST['emergencyRelation1'] == 'friend') ? 'selected' : ''; ?>>Friend</option>
                                        <option value="other" <?php echo (isset($_POST['emergencyRelation1']) && $_POST['emergencyRelation1'] == 'other') ? 'selected' : ''; ?>>Other</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="emergencyPhone1">Phone Number*</label>
                                    <input type="tel" id="emergencyPhone1" name="emergencyPhone1" class="form-control" pattern="[0-9]{10}" value="<?php echo isset($_POST['emergencyPhone1']) ? htmlspecialchars($_POST['emergencyPhone1']) : ''; ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="emergencyEmail1">Email</label>
                                    <input type="email" id="emergencyEmail1" name="emergencyEmail1" class="form-control" value="<?php echo isset($_POST['emergencyEmail1']) ? htmlspecialchars($_POST['emergencyEmail1']) : ''; ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="emergency-contact-card">
                            <h3 class="contact-title">Secondary Emergency Contact</h3>
                            
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="emergencyName2">Full Name</label>
                                    <input type="text" id="emergencyName2" name="emergencyName2" class="form-control" value="<?php echo isset($_POST['emergencyName2']) ? htmlspecialchars($_POST['emergencyName2']) : ''; ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label for="emergencyRelation2">Relationship</label>
                                    <select id="emergencyRelation2" name="emergencyRelation2" class="form-control">
                                        <option value="">Select</option>
                                        <option value="spouse" <?php echo (isset($_POST['emergencyRelation2']) && $_POST['emergencyRelation2'] == 'spouse') ? 'selected' : ''; ?>>Spouse</option>
                                        <option value="parent" <?php echo (isset($_POST['emergencyRelation2']) && $_POST['emergencyRelation2'] == 'parent') ? 'selected' : ''; ?>>Parent</option>
                                        <option value="child" <?php echo (isset($_POST['emergencyRelation2']) && $_POST['emergencyRelation2'] == 'child') ? 'selected' : ''; ?>>Child</option>
                                        <option value="sibling" <?php echo (isset($_POST['emergencyRelation2']) && $_POST['emergencyRelation2'] == 'sibling') ? 'selected' : ''; ?>>Sibling</option>
                                        <option value="friend" <?php echo (isset($_POST['emergencyRelation2']) && $_POST['emergencyRelation2'] == 'friend') ? 'selected' : ''; ?>>Friend</option>
                                        <option value="other" <?php echo (isset($_POST['emergencyRelation2']) && $_POST['emergencyRelation2'] == 'other') ? 'selected' : ''; ?>>Other</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="emergencyPhone2">Phone Number</label>
                                    <input type="tel" id="emergencyPhone2" name="emergencyPhone2" class="form-control" pattern="[0-9]{10}" value="<?php echo isset($_POST['emergencyPhone2']) ? htmlspecialchars($_POST['emergencyPhone2']) : ''; ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label for="emergencyEmail2">Email</label>
                                    <input type="email" id="emergencyEmail2" name="emergencyEmail2" class="form-control" value="<?php echo isset($_POST['emergencyEmail2']) ? htmlspecialchars($_POST['emergencyEmail2']) : ''; ?>">
                                </div>
                            </div>
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
                        <button type="submit" class="btn btn-block">Create Account</button>
                    </div>
                    
                    <div class="login-link">
                        <p>Already have an account? <a href="login.php">Login here</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // DOM Elements
        const form = document.getElementById('registrationForm');
        const insuranceRadios = document.querySelectorAll('input[name="hasInsurance"]');
        const insuranceDetails = document.getElementById('insuranceDetails');
        
        // Show/hide insurance details based on selection
        insuranceRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                insuranceDetails.style.display = this.value === 'yes' ? 'block' : 'none';
            });
        });
        
        // Initialize insurance details visibility on page load
        document.addEventListener('DOMContentLoaded', function() {
            const selectedInsurance = document.querySelector('input[name="hasInsurance"]:checked');
            if (selectedInsurance && selectedInsurance.value === 'yes') {
                insuranceDetails.style.display = 'block';
            }
        });
        
        // Password validation
        form.addEventListener('submit', function(e) {
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