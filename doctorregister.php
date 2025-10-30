<?php 
require_once 'includes/db_connect.php'; 

// --- Start of page logic using the $conn variable ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check login credentials using $conn...
}
require_once 'includes/session_start.php'; 
// Note: db.php is typically included in the handler, not the form page itself.

// Fetch potential registration errors/success message from session
$error_message = $_SESSION['reg_error'] ?? '';
unset($_SESSION['reg_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Registration - MediConnect</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Custom styles from the original snippet adapted for clarity */
        .container.form-page {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            gap: 2rem;
            max-width: 1000px;
            margin: 3rem auto;
        }
        .form-visual {
            flex-shrink: 0;
            width: 250px;
            padding: 2rem;
            border-radius: 1rem;
            background-color: #e3f2fd; /* Light blue visual background */
            text-align: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            margin-top: 1rem;
        }
        .form-card {
            flex-grow: 1;
            background-color: white;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
        }
        .form-row {
            display: flex;
            gap: 1.5rem;
        }
        .form-section {
            margin-bottom: 1.25rem;
        }
        .label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.3rem;
        }
        .input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #ddd;
            border-radius: 0.5rem;
            font-size: 1rem;
        }
    </style>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="container form-page">
    <div class="form-visual">
        <div style="font-size:3rem">🩺</div>
        <div style="font-weight:700">Doctor Registration</div>
        <div class="small">Register as a verified healthcare professional</div>
    </div>
    <div class="form-card">
        <h3 style="color:var(--primary); font-size: 1.5rem; margin-bottom: 1rem;">Professional & Contact Details</h3>

        <?php if ($error_message): ?>
            <div style="background-color:#fef2f2; border:1px solid #f87171; color:#dc2626; padding:12px; border-radius:0.5rem; margin-bottom:1.5rem;" role="alert">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <!-- *** FIX 1: Added action and method attributes *** -->
        <form action="handlers/register_doctor.php" method="POST">
            
            <!-- Personal Info (Split Full Name) -->
            <div class="form-row form-section">
                <div style="flex:1">
                    <label class="label" for="first_name">First Name</label>
                    <!-- *** FIX 2: Added name="first_name" *** -->
                    <input class="input" id="first_name" name="first_name" required placeholder="John">
                </div>
                <div style="flex:1">
                    <label class="label" for="last_name">Last Name</label>
                    <!-- *** FIX 2: Added name="last_name" *** -->
                    <input class="input" id="last_name" name="last_name" required placeholder="Doe">
                </div>
                <div style="width:160px">
                    <label class="label" for="gender">Gender</label>
                    <!-- *** FIX 2: Added name="gender" *** -->
                    <select class="input" id="gender" name="gender" required>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
            </div>

            <div class="form-row form-section">
                <div style="width:160px">
                    <label class="label" for="aadhaar_no">Aadhaar Number</label>
                    <!-- *** FIX 2: Added name="aadhaar_no" *** -->
                    <input class="input" id="aadhaar_no" name="aadhaar_no" required placeholder="123456789012" maxlength="12" pattern="\d{12}">
                </div>
                <div style="flex:1">
                    <label class="label" for="email">Email</label>
                    <!-- *** FIX 2: Added name="email" *** -->
                    <input class="input" id="email" name="email" required type="email" placeholder="doc@example.com">
                </div>
                <!-- *** MISSING FIELD ADDED: Phone *** -->
                <div style="width:160px">
                    <label class="label" for="phone">Phone</label>
                    <input class="input" id="phone" name="phone" required type="tel" placeholder="9876543210">
                </div>
            </div>

            <h4 style="color:var(--primary); margin-top:20px; font-size:1.2rem; border-bottom: 1px solid #eee; padding-bottom: 8px;">Professional Details</h4>

            <div class="form-row form-section">
                <div style="flex:1">
                    <label class="label" for="qualifications">Qualification / Degree</label>
                    <!-- *** FIX 2: Added name="qualifications" *** -->
                    <input class="input" id="qualifications" name="qualifications" required placeholder="MBBS, MD (Cardiology)">
                </div>
                <div style="width:160px">
                    <label class="label" for="experience_years">Years Experience</label>
                    <!-- *** FIX 2: Added name="experience_years" *** -->
                    <input class="input" id="experience_years" name="experience_years" required type="number" min="0" placeholder="e.g., 8">
                </div>
            </div>

            <div class="form-row form-section">
                <div style="flex:1">
                    <label class="label" for="specialization">Specialization</label>
                    <!-- *** FIX 2: Added name="specialization" *** -->
                    <input class="input" id="specialization" name="specialization" required placeholder="Cardiology, Orthopedics, etc.">
                </div>
                <div style="width:160px">
                    <label class="label" for="license_no">License / Reg. No.</label>
                    <!-- *** FIX 2: Added name="license_no" *** -->
                    <input class="input" id="license_no" name="license_no" required placeholder="LIC-123456">
                </div>
            </div>

            <div class="form-row form-section">
                <div style="width:180px">
                    <label class="label" for="surgeries_performed">Surgeries Performed</label>
                    <!-- *** FIX 2: Added name="surgeries_performed" *** -->
                    <input class="input" id="surgeries_performed" name="surgeries_performed" required type="number" min="0" value="0">
                </div>
                <!-- *** MISSING FIELD ADDED: Consultation Fee *** -->
                <div style="width:180px">
                    <label class="label" for="consultation_fee">Consultation Fee (₹)</label>
                    <input class="input" id="consultation_fee" name="consultation_fee" required type="number" step="0.01" min="100" placeholder="500.00">
                </div>
                <div style="flex:1">
                    <label class="label" for="hospital_clinic">Clinic / Hospital Name</label>
                    <!-- *** FIX 2: Added name="hospital_clinic" *** -->
                    <input class="input" id="hospital_clinic" name="hospital_clinic" required placeholder="Hospital Name">
                </div>
            </div>

            <h4 style="color:var(--primary);margin-top:20px; font-size:1.2rem; border-bottom: 1px solid #eee; padding-bottom: 8px;">Account Security</h4>
            <div class="form-row form-section">
                <div style="flex:1">
                    <label class="label" for="password">Password</label>
                    <!-- *** FIX 2: Added name="password" *** -->
                    <input class="input" id="password" name="password" required type="password" minlength="8" placeholder="Create a password">
                </div>
                <div style="flex:1">
                    <label class="label" for="confirm_password">Confirm Password</label>
                    <!-- *** FIX 2: Added name="confirm_password" *** -->
                    <input class="input" id="confirm_password" name="confirm_password" required type="password" minlength="8" placeholder="Confirm password">
                </div>
            </div>

            <div style="margin-top:20px;display:flex;justify-content:space-between;align-items:center">
                <div class="small">Already registered? <a href="login.php">Login</a></div>
                <!-- *** FIX 3: Changed type="button" to type="submit" *** -->
                <button class="btn" type="submit" style="background-color:var(--accent); color:white; padding: 10px 20px; border:none; border-radius:0.5rem; font-weight:600;">
                    🧾 Register as Doctor
                </button>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
