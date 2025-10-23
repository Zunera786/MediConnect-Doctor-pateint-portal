<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

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

// Get user data
$user_id = $_SESSION['user_id'];
$user_query = "SELECT * FROM patients WHERE id = '$user_id'";
$user_result = mysqli_query($con, $user_query);

if (!$user_result || mysqli_num_rows($user_result) == 0) {
    // User not found, logout
    session_destroy();
    header("Location: login.php");
    exit();
}

$user = mysqli_fetch_assoc($user_result);

// Prepare user data for display
$full_name = $user['first_name'] . ' ' . $user['last_name'];
$initials = strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - MediConnect</title>
    <style>
        :root {
            --sidebar-width: 250px;
            --header-height: 60px;
            --primary: #2563eb;
            --secondary: #1e40af;
            --accent: #3b82f6;
            --text: #1f2937;
            --light: #f9fafb;
            --gray: #e5e7eb;
            --success: #16a34a;
            --warning: #ea580c;
            --error: #dc2626;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            color: var(--text);
            background-color: #f3f4f6;
        }
        .dashboard {
            display: grid;
            grid-template-columns: var(--sidebar-width) 1fr;
            min-height: 100vh;
        }
        .sidebar {
            background: white;
            border-right: 1px solid var(--gray);
            height: 100vh;
            position: sticky;
            top: 0;
            padding: 1rem 0;
        }
        .sidebar-header {
            padding: 0 1.5rem 1.5rem;
            border-bottom: 1px solid var(--gray);
            margin-bottom: 1rem;
        }
        .sidebar-nav {
            padding: 0 1rem;
        }
        .nav-item {
            margin-bottom: 0.5rem;
        }
        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            color: var(--text);
            text-decoration: none;
            transition: all 0.3s;
        }
        .nav-link:hover, .nav-link.active {
            background-color: var(--light);
            color: var(--primary);
        }
        .nav-link i {
            margin-right: 0.75rem;
            width: 24px;
            text-align: center;
        }
        .main-content {
            padding-bottom: 2rem;
        }
        .header {
            height: var(--header-height);
            background: white;
            border-bottom: 1px solid var(--gray);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 2rem;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .profile-dropdown {
            position: relative;
        }
        .profile-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: none;
            border: none;
            cursor: pointer;
        }
        .profile-img {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        .dropdown-menu {
            position: absolute;
            right: 0;
            top: calc(100% + 0.5rem);
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            width: 200px;
            padding: 0.5rem 0;
            display: none;
        }
        .dropdown-menu.show {
            display: block;
        }
        .dropdown-item {
            padding: 0.75rem 1rem;
            display: block;
            text-decoration: none;
            color: var(--text);
            transition: background-color 0.3s;
        }
        .dropdown-item:hover {
            background-color: var(--light);
        }
        .content {
            padding: 2rem;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .section-title {
            margin-bottom: 1.5rem;
            color: var(--primary);
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: white;
            border-radius: 0.5rem;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        .stat-card h3 {
            font-size: 0.875rem;
            color: #6b7280;
            margin-bottom: 0.5rem;
        }
        .stat-card p {
            font-size: 1.5rem;
            font-weight: 600;
        }
        .appointments-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        .appointments-table th, 
        .appointments-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--gray);
        }
        .appointments-table th {
            background-color: var(--light);
            font-weight: 600;
        }
        .badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-success {
            background-color: #dcfce7;
            color: var(--success);
        }
        .badge-warning {
            background-color: #ffedd5;
            color: var(--warning);
        }
        .badge-error {
            background-color: #fee2e2;
            color: var(--error);
        }
        .document-categories {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }
        .category-btn {
            padding: 0.5rem 1rem;
            background: var(--light);
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
        }
        .category-btn.active {
            background: var(--primary);
            color: white;
        }
        .upload-section {
            background: white;
            padding: 1.5rem;
            border-radius: 0.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        .upload-area {
            border: 2px dashed var(--gray);
            border-radius: 0.5rem;
            padding: 2rem;
            text-align: center;
            margin-bottom: 1rem;
            cursor: pointer;
        }
        .documents-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
        }
        .document-card {
            background: white;
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        .document-preview {
            height: 150px;
            background: var(--light);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .document-info {
            padding: 1rem;
        }
        .document-info h4 {
            margin-bottom: 0.5rem;
        }
        .document-meta {
            display: flex;
            justify-content: space-between;
            color: #6b7280;
            font-size: 0.875rem;
        }
        
        .info-card {
            background: white;
            border-radius: 0.5rem;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        .info-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .info-group {
            margin-bottom: 1rem;
        }

        .info-group label {
            font-size: 0.875rem;
            color: #6b7280;
            display: block;
            margin-bottom: 0.25rem;
        }

        .info-group p {
            font-weight: 500;
            padding: 0.5rem 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .emergency-contacts {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 1rem;
        }

        .contact-card {
            background: #f8fafc;
            padding: 1rem;
            border-radius: 0.5rem;
        }

        .info-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid #e5e7eb;
            font-size: 0.875rem;
            color: #6b7280;
        }

        .verification-badge {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .edit-btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            cursor: pointer;
        }

        .dropdown-divider {
            height: 1px;
            background-color: var(--gray);
            margin: 0.5rem 0;
        }

        @media (max-width: 768px) {
            .dashboard {
                grid-template-columns: 1fr;
            }
            .sidebar {
                height: auto;
                position: static;
            }
            .stats-grid {
                grid-template-columns: 1fr;
            }
            .documents-grid {
                grid-template-columns: 1fr;
            }
            .info-grid {
                grid-template-columns: 1fr;
            }
            .emergency-contacts {
                grid-template-columns: 1fr;
            }
            .info-footer {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <!-- Sidebar Navigation -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>MediConnect</h2>
            </div>
            <nav class="sidebar-nav">
                <div class="nav-item">
                    <a href="#dashboard" class="nav-link active">
                        <i>📊</i> Dashboard
                    </a>
                </div>
                <div class="nav-item">
                    <a href="#appointments" class="nav-link">
                        <i>📅</i> Appointments
                    </a>
                </div>
                <div class="nav-item">
                    <a href="#personal-info" class="nav-link">
                        <i>👤</i> Personal Info
                    </a>
                </div>
                <div class="nav-item">
                    <a href="#medical-history" class="nav-link">
                        <i>🏥</i> Medical History
                    </a>
                </div>
                <div class="nav-item">
                    <a href="#current-treatment" class="nav-link">
                        <i>💊</i> Current Treatment
                    </a>
                </div>
                <div class="nav-item">
                    <a href="#documents" class="nav-link">
                        <i>📄</i> Documents
                    </a>
                </div>
                <div class="nav-item">
                    <a href="#medicines" class="nav-link">
                        <i>💊</i> Medicines
                    </a>
                </div>
            </nav>
        </div>
        
        <!-- Main Content Area -->
        <div class="main-content">
            <header class="header">
                <div></div> <!-- Empty div for spacing -->
                <div class="profile-dropdown">
                    <button class="profile-btn">
                        <div class="profile-img"><?php echo $initials; ?></div>
                        <span><?php echo htmlspecialchars($full_name); ?></span>
                    </button>
                    <div class="dropdown-menu">
                        <a href="#edit-profile" class="dropdown-item">Edit Profile</a>
                        <a href="#settings" class="dropdown-item">Settings</a>
                        <a href="#privacy" class="dropdown-item">Privacy</a>
                        <div class="dropdown-divider"></div>
                        <a href="logout.php" class="dropdown-item">Logout</a>
                    </div>
                </div>
            </header>
            
            <div class="content">
                <!-- Dashboard Overview -->
                <div id="dashboard" class="tab-content active">
                    <h2 class="section-title">Dashboard Overview</h2>
                    
                    <div class="stats-grid">
                        <div class="stat-card">
                            <h3>Next Appointment</h3>
                            <p>Tomorrow, 10:30 AM</p>
                        </div>
                        <div class="stat-card">
                            <h3>Active Prescriptions</h3>
                            <p>3</p>
                        </div>
                        <div class="stat-card">
                            <h3>Recent Reports</h3>
                            <p>5</p>
                        </div>
                        <div class="stat-card">
                            <h3>Health Status</h3>
                            <p>Good</p>
                        </div>
                    </div>
                    
                    <h3 class="section-title">Recent Activity</h3>
                    <table class="appointments-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Activity</th>
                                <th>Type</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>2023-11-15</td>
                                <td>Blood Test Results</td>
                                <td>Lab Report</td>
                                <td><span class="badge badge-success">Completed</span></td>
                            </tr>
                            <tr>
                                <td>2023-11-10</td>
                                <td>Dr. Smith Consultation</td>
                                <td>Appointment</td>
                                <td><span class="badge badge-success">Completed</span></td>
                            </tr>
                            <tr>
                                <td>2023-11-05</td>
                                <td>X-Ray Scan</td>
                                <td>Diagnostic</td>
                                <td><span class="badge badge-success">Completed</span></td>
                            </tr>
                            <tr>
                                <td>2023-11-20</td>
                                <td>Follow-up with Dr. Johnson</td>
                                <td>Appointment</td>
                                <td><span class="badge badge-warning">Upcoming</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Appointments -->
                <div id="appointments" class="tab-content">
                    <h2 class="section-title">Appointments</h2>
                    <table class="appointments-table">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>Doctor</th>
                                <th>Specialization</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>2023-11-20, 10:30 AM</td>
                                <td>Dr. Johnson</td>
                                <td>Cardiology</td>
                                <td>City Hospital</td>
                                <td><span class="badge badge-warning">Confirmed</span></td>
                                <td>
                                    <button>Reschedule</button>
                                    <button>Cancel</button>
                                </td>
                            </tr>
                            <tr>
                                <td>2023-11-25, 2:00 PM</td>
                                <td>Dr. Smith</td>
                                <td>General Physician</td>
                                <td>Main Clinic</td>
                                <td><span class="badge badge-success">Completed</span></td>
                                <td>
                                    <button>View Details</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
               <!-- Personal Information -->
<div id="personal-info" class="tab-content">
    <h2 class="section-title">Personal Information</h2>
    <div class="info-card">
        <div class="info-header">
            <h3>Basic Details</h3>
            <button class="edit-btn">Edit</button>
        </div>
        
        <div class="info-grid">
            <!-- Row 1 -->
            <div class="info-group">
                <label>First Name</label>
                <p><?php echo htmlspecialchars($user['first_name']); ?></p>
            </div>
            <div class="info-group">
                <label>Middle Name</label>
                <p><?php echo htmlspecialchars($user['middle_name'] ?: 'N/A'); ?></p>
            </div>
            <div class="info-group">
                <label>Last Name</label>
                <p><?php echo htmlspecialchars($user['last_name']); ?></p>
            </div>
            
            <!-- Row 2 -->
            <div class="info-group">
                <label>Aadhaar Number</label>
                <p><?php echo htmlspecialchars($user['aadhaar']); ?></p>
            </div>
            <div class="info-group">
                <label>Date of Birth</label>
                <p><?php echo date('d/m/Y', strtotime($user['dob'])); ?></p>
            </div>
            <div class="info-group">
                <label>Gender</label>
                <p><?php echo ucfirst(htmlspecialchars($user['gender'])); ?></p>
            </div>
            
            <!-- Row 3 -->
            <div class="info-group">
                <label>Marital Status</label>
                <p><?php echo ucfirst(htmlspecialchars($user['marital_status'] ?: 'N/A')); ?></p>
            </div>
            <div class="info-group">
                <label>Nationality</label>
                <p><?php echo htmlspecialchars($user['nationality']); ?></p>
            </div>
            <div class="info-group">
                <label>Blood Group</label>
                <p><?php echo htmlspecialchars($user['blood_group'] ?: 'N/A'); ?></p>
            </div>
        </div>
        
        <div class="info-header" style="margin-top: 2rem;">
            <h3>Contact Information</h3>
        </div>
        
        <div class="info-grid">
            <!-- Row 4 -->
            <div class="info-group">
                <label>Complete Address</label>
                <p><?php echo htmlspecialchars($user['address']); ?></p>
            </div>
            <div class="info-group">
                <label>Email Address</label>
                <p><?php echo htmlspecialchars($user['email']); ?></p>
            </div>
            <div class="info-group">
                <label>Phone Number</label>
                <p><?php echo htmlspecialchars($user['phone']); ?></p>
            </div>
        </div>
        
        <div class="info-header" style="margin-top: 2rem;">
            <h3>Health Information</h3>
        </div>
        
        <div class="info-grid">
            <!-- Row 5 -->
            <div class="info-group">
                <label>Height</label>
                <p><?php echo htmlspecialchars($user['height'] ? $user['height'] . ' cm' : 'N/A'); ?></p>
            </div>
            <div class="info-group">
                <label>Weight</label>
                <p><?php echo htmlspecialchars($user['weight'] ? $user['weight'] . ' kg' : 'N/A'); ?></p>
            </div>
            <div class="info-group">
                <label>Known Allergies</label>
                <p><?php echo htmlspecialchars($user['allergies'] ?: 'None'); ?></p>
            </div>
            
            <!-- Row 6 -->
            <div class="info-group">
                <label>Current Medications</label>
                <p><?php echo htmlspecialchars($user['current_medications'] ?: 'None'); ?></p>
            </div>
            <div class="info-group">
                <label>Insurance Provider</label>
                <p><?php echo htmlspecialchars($user['insurance_provider'] ?: 'None'); ?></p>
            </div>
            <div class="info-group">
                <label>Insurance Status</label>
                <p><?php echo $user['has_insurance'] == 'yes' ? 'Insured' : 'Not Insured'; ?></p>
            </div>
        </div>
        
        <div class="info-header" style="margin-top: 2rem;">
            <h3>Emergency Contacts</h3>
        </div>
        
        <div class="emergency-contacts">
            <!-- Emergency Contact 1 -->
            <div class="contact-card">
                <h4>Primary Emergency Contact</h4>
                <div class="info-grid">
                    <div class="info-group">
                        <label>Name</label>
                        <p><?php echo htmlspecialchars($user['emergency_name1']); ?></p>
                    </div>
                    <div class="info-group">
                        <label>Relationship</label>
                        <p><?php echo ucfirst(htmlspecialchars($user['emergency_relation1'])); ?></p>
                    </div>
                    <div class="info-group">
                        <label>Phone</label>
                        <p><?php echo htmlspecialchars($user['emergency_phone1']); ?></p>
                    </div>
                    <div class="info-group">
                        <label>Email</label>
                        <p><?php echo htmlspecialchars($user['emergency_email1'] ?: 'N/A'); ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Emergency Contact 2 -->
            <div class="contact-card">
                <h4>Secondary Emergency Contact</h4>
                <div class="info-grid">
                    <div class="info-group">
                        <label>Name</label>
                        <p><?php echo htmlspecialchars($user['emergency_name2'] ?: 'N/A'); ?></p>
                    </div>
                    <div class="info-group">
                        <label>Relationship</label>
                        <p><?php echo ucfirst(htmlspecialchars($user['emergency_relation2'] ?: 'N/A')); ?></p>
                    </div>
                    <div class="info-group">
                        <label>Phone</label>
                        <p><?php echo htmlspecialchars($user['emergency_phone2'] ?: 'N/A'); ?></p>
                    </div>
                    <div class="info-group">
                        <label>Email</label>
                        <p><?php echo htmlspecialchars($user['emergency_email2'] ?: 'N/A'); ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="info-footer">
            <p>Last updated: <?php echo date('d/m/Y', strtotime($user['updated_at'])); ?></p>
            <div class="verification-badge">
                <input type="checkbox" id="verified" checked>
                <label for="verified">Information verified with hospital records</label>
            </div>
        </div>
    </div>
</div>                
                <!-- Medical History -->
                <div id="medical-history" class="tab-content">
                    <h2 class="section-title">Medical History</h2>
                    <div class="stat-card">
                        <h3>Past Treatments</h3>
                        <table class="appointments-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Treatment</th>
                                    <th>Hospital/Clinic</th>
                                    <th>Doctor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>2023-05-10</td>
                                    <td>Appendectomy</td>
                                    <td>City General Hospital</td>
                                    <td>Dr. Williams</td>
                                </tr>
                                <tr>
                                    <td>2022-11-15</td>
                                    <td>Flu Vaccination</td>
                                    <td>Local Clinic</td>
                                    <td>Dr. Smith</td>
                                </tr>
                            </tbody>
                        </table>
                        <button style="margin-top: 1rem;">Add New Treatment</button>
                    </div>
                </div>
                
                <!-- Current Treatment -->
                <div id="current-treatment" class="tab-content">
                    <h2 class="section-title">Current Treatment</h2>
                    <div class="stat-card">
                        <div class="form-group">
                            <label>
                                <input type="checkbox" checked>
                                Currently admitted in hospital
                            </label>
                        </div>
                        
                        <div id="current-treatment-details" style="margin-top: 1rem;">
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Hospital Name</label>
                                    <p>City General Hospital</p>
                                </div>
                                <div class="form-group">
                                    <label>Admission Date</label>
                                    <p>2023-11-01</p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Treatment Description</label>
                                <p>Post-operative care after appendectomy</p>
                            </div>
                            <div class="form-group">
                                <label>Attending Doctor</label>
                                <p>Dr. Williams</p>
                            </div>
                        </div>
                        
                        <h3 style="margin-top: 2rem;">Current Medications</h3>
                        <table class="appointments-table">
                            <thead>
                                <tr>
                                    <th>Medication</th>
                                    <th>Dosage</th>
                                    <th>Frequency</th>
                                    <th>Prescribed By</th>
                                    <th>Until</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Amoxicillin</td>
                                    <td>500mg</td>
                                    <td>3 times daily</td>
                                    <td>Dr. Williams</td>
                                    <td>2023-11-20</td>
                                </tr>
                                <tr>
                                    <td>Paracetamol</td>
                                    <td>650mg</td>
                                    <td>As needed</td>
                                    <td>Dr. Williams</td>
                                    <td>2023-11-15</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Documents & Reports -->
                <div id="documents" class="tab-content">
                    <h2 class="section-title">Documents & Reports</h2>
                    
                    <div class="document-categories">
                        <button class="category-btn active" data-category="all">All Documents</button>
                        <button class="category-btn" data-category="xray">X-Ray</button>
                        <button class="category-btn" data-category="ct">CT Scan</button>
                        <button class="category-btn" data-category="mri">MRI</button>
                        <button class="category-btn" data-category="endoscopy">Endoscopy</button>
                        <button class="category-btn" data-category="prescriptions">Prescriptions</button>
                    </div>
                    
                    <div class="upload-section">
                        <h3>Upload New Document</h3>
                        <div class="upload-area" id="uploadArea">
                            <p>Drag & drop files here or click to browse</p>
                            <input type="file" id="fileInput" style="display: none;">
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Document Type</label>
                                <select id="documentType">
                                    <option value="xray">X-Ray</option>
                                    <option value="ct">CT Scan</option>
                                    <option value="mri">MRI</option>
                                    <option value="endoscopy">Endoscopy</option>
                                    <option value="prescriptions">Prescription</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Date</label>
                                <input type="date" id="documentDate">
                            </div>
                        </div>
                        <button class="btn">Upload Document</button>
                    </div>
                    
                    <div class="documents-grid">
                        <div class="document-card">
                            <div class="document-preview">
                                <p>X-Ray Preview</p>
                            </div>
                            <div class="document-info">
                                <h4>Chest X-Ray</h4>
                                <div class="document-meta">
                                    <span>2023-11-05</span>
                                    <span>X-Ray</span>
                                </div>
                            </div>
                        </div>
                        <div class="document-card">
                            <div class="document-preview">
                                <p>Prescription Preview</p>
                            </div>
                            <div class="document-info">
                                <h4>Dr. Smith Prescription</h4>
                                <div class="document-meta">
                                    <span>2023-11-10</span>
                                    <span>Prescription</span>
                                </div>
                            </div>
                        </div>
                        <!-- More documents would be listed here -->
                    </div>
                </div>
                
                <!-- Medicines Used -->
                <div id="medicines" class="tab-content">
                    <h2 class="section-title">Medicines Used</h2>
                    <table class="appointments-table">
                        <thead>
                            <tr>
                                <th>Medicine</th>
                                <th>Prescribed Date</th>
                                <th>Duration</th>
                                <th>Purpose</th>
                                <th>Prescribed By</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Amoxicillin</td>
                                <td>2023-11-01</td>
                                <td>20 days</td>
                                <td>Post-surgery infection prevention</td>
                                <td>Dr. Williams</td>
                            </tr>
                            <tr>
                                <td>Paracetamol</td>
                                <td>2023-11-01</td>
                                <td>15 days</td>
                                <td>Pain management</td>
                                <td>Dr. Williams</td>
                            </tr>
                            <tr>
                                <td>Ibuprofen</td>
                                <td>2023-05-15</td>
                                <td>7 days</td>
                                <td>Fever reduction</td>
                                <td>Dr. Smith</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Tab switching functionality
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Remove active class from all links
                document.querySelectorAll('.nav-link').forEach(el => {
                    el.classList.remove('active');
                });
                
                // Add active class to clicked link
                this.classList.add('active');
                
                // Hide all tab contents
                document.querySelectorAll('.tab-content').forEach(tab => {
                    tab.classList.remove('active');
                });
                
                // Show the selected tab content
                const tabId = this.getAttribute('href');
                document.querySelector(tabId).classList.add('active');
            });
        });
        
        // Profile dropdown toggle
        const profileBtn = document.querySelector('.profile-btn');
        const dropdownMenu = document.querySelector('.dropdown-menu');
        
        profileBtn.addEventListener('click', function() {
            dropdownMenu.classList.toggle('show');
        });
        
        // Close dropdown when clicking outside
        window.addEventListener('click', function(e) {
            if (!e.target.closest('.profile-dropdown')) {
                dropdownMenu.classList.remove('show');
            }
        });
        
        // Document category filtering
        document.querySelectorAll('.category-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.category-btn').forEach(b => {
                    b.classList.remove('active');
                });
                this.classList.add('active');
                
                // Filter functionality would go here
            });
        });
        
        // File upload interaction
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('fileInput');
        
        uploadArea.addEventListener('click', function() {
            fileInput.click();
        });
        
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.style.borderColor = '#2563eb';
            this.style.backgroundColor = '#f0f7ff';
        });
        
        uploadArea.addEventListener('dragleave', function() {
            this.style.borderColor = '#e5e7eb';
            this.style.backgroundColor = 'transparent';
        });
        
        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            this.style.borderColor = '#e5e7eb';
            this.style.backgroundColor = 'transparent';
            
            if (e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                // You would handle the file upload here
            }
        });
    </script>
</body>
</html>