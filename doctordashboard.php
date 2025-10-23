<?php
include 'config.php';
checkAuth('doctor');

$doctor_id = $_SESSION['user_id'];

// Get doctor data
$doctor_query = "SELECT * FROM doctors WHERE id = '$doctor_id'";
$doctor_result = mysqli_query($con, $doctor_query);
$doctor = mysqli_fetch_assoc($doctor_result);

// Get today's appointments
$today = date('Y-m-d');
$appointments_query = "
    SELECT a.*, p.first_name, p.last_name, p.phone, p.email 
    FROM appointments a 
    LEFT JOIN patients p ON a.patient_id = p.id 
    WHERE a.doctor_id = '$doctor_id' AND a.appointment_date = '$today' 
    ORDER BY a.appointment_time
";
$appointments_result = mysqli_query($con, $appointments_query);

// Get appointment statistics
$stats_query = "
    SELECT 
        COUNT(*) as total_appointments,
        SUM(CASE WHEN status = 'scheduled' AND appointment_date >= '$today' THEN 1 ELSE 0 END) as upcoming_appointments,
        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_appointments,
        COUNT(DISTINCT patient_id) as total_patients
    FROM appointments 
    WHERE doctor_id = '$doctor_id'
";
$stats_result = mysqli_query($con, $stats_query);
$stats = mysqli_fetch_assoc($stats_result);

// Handle appointment status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $appointment_id = sanitize($_POST['appointment_id']);
    $status = sanitize($_POST['status']);
    
    $update_query = "UPDATE appointments SET status = '$status' WHERE id = '$appointment_id' AND doctor_id = '$doctor_id'";
    if (mysqli_query($con, $update_query)) {
        $success = "Appointment status updated successfully!";
        header("Location: doctor_dashboard.php");
        exit();
    } else {
        $error = "Error updating appointment status.";
    }
}

// Handle prescription creation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_prescription'])) {
    $patient_id = sanitize($_POST['patient_id']);
    $medicines = sanitize($_POST['medicines']);
    $dosage = sanitize($_POST['dosage']);
    $instructions = sanitize($_POST['instructions']);
    $duration = sanitize($_POST['duration']);
    
    $prescription_query = "
        INSERT INTO prescriptions (doctor_id, patient_id, medicines, dosage, instructions, duration, created_at) 
        VALUES ('$doctor_id', '$patient_id', '$medicines', '$dosage', '$instructions', '$duration', NOW())
    ";
    
    if (mysqli_query($con, $prescription_query)) {
        $success = "Prescription created successfully!";
    } else {
        $error = "Error creating prescription.";
    }
}

// Get all patients
$patients_query = "
    SELECT DISTINCT p.* 
    FROM patients p 
    INNER JOIN appointments a ON p.id = a.patient_id 
    WHERE a.doctor_id = '$doctor_id' 
    ORDER BY p.first_name
";
$patients_result = mysqli_query($con, $patients_query);

// Get all prescriptions
$prescriptions_query = "
    SELECT pr.*, p.first_name, p.last_name 
    FROM prescriptions pr 
    LEFT JOIN patients p ON pr.patient_id = p.id 
    WHERE pr.doctor_id = '$doctor_id' 
    ORDER BY pr.created_at DESC
";
$prescriptions_result = mysqli_query($con, $prescriptions_query);

$full_name = $doctor['first_name'] . ' ' . $doctor['last_name'];
$initials = strtoupper(substr($doctor['first_name'], 0, 1) . substr($doctor['last_name'], 0, 1));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard - MediConnect</title>
    <style>
        :root {
            --sidebar-width: 280px;
            --header-height: 70px;
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
            padding: 1.5rem 0;
        }
        
        .sidebar-header {
            padding: 0 1.5rem 1.5rem;
            border-bottom: 1px solid var(--gray);
            margin-bottom: 1rem;
            text-align: center;
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
            padding: 0.5rem;
            border-radius: 0.5rem;
            transition: background-color 0.3s;
        }
        
        .profile-btn:hover {
            background-color: var(--light);
        }
        
        .profile-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.1rem;
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
            z-index: 1000;
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
            font-size: 1.5rem;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            border-left: 4px solid var(--primary);
        }
        
        .stat-card h3 {
            font-size: 0.875rem;
            color: #6b7280;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .stat-card p {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
        }
        
        .appointments-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 0.75rem;
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
            color: var(--text);
        }
        
        .badge {
            display: inline-block;
            padding: 0.35rem 0.75rem;
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
        
        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 0.375rem;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            font-size: 0.875rem;
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: var(--secondary);
        }
        
        .btn-success {
            background-color: var(--success);
            color: white;
        }
        
        .btn-warning {
            background-color: var(--warning);
            color: white;
        }
        
        .btn-outline {
            background-color: transparent;
            border: 1px solid var(--gray);
            color: var(--text);
        }
        
        .btn-outline:hover {
            background-color: var(--light);
        }
        
        .dropdown-divider {
            height: 1px;
            background-color: var(--gray);
            margin: 0.5rem 0;
        }
        
        .welcome-section {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 2rem;
            border-radius: 1rem;
            margin-bottom: 2rem;
        }
        
        .welcome-section h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        
        .welcome-section p {
            opacity: 0.9;
            font-size: 1.1rem;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--gray);
            border-radius: 0.375rem;
            font-size: 1rem;
        }
        
        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .alert-success {
            background-color: #dcfce7;
            color: var(--success);
            border: 1px solid #bbf7d0;
        }
        
        .alert-error {
            background-color: #fee2e2;
            color: var(--error);
            border: 1px solid #fecaca;
        }
        
        .card {
            background: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            margin-bottom: 1.5rem;
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
            
            .form-row {
                grid-template-columns: 1fr;
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
                <p style="color: var(--primary); font-weight: 600; margin-top: 0.5rem;">Doctor Portal</p>
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
                    <a href="#patients" class="nav-link">
                        <i>👥</i> Patients
                    </a>
                </div>
                <div class="nav-item">
                    <a href="#prescriptions" class="nav-link">
                        <i>💊</i> Prescriptions
                    </a>
                </div>
                <div class="nav-item">
                    <a href="#schedule" class="nav-link">
                        <i>⏰</i> Schedule
                    </a>
                </div>
                <div class="nav-item">
                    <a href="#profile" class="nav-link">
                        <i>👤</i> Profile
                    </a>
                </div>
            </nav>
        </div>
        
        <!-- Main Content Area -->
        <div class="main-content">
            <header class="header">
                <div style="font-weight: 600; color: var(--primary);">Doctor Dashboard</div>
                <div class="profile-dropdown">
                    <button class="profile-btn">
                        <div class="profile-img"><?php echo $initials; ?></div>
                        <span style="font-weight: 500;">Dr. <?php echo htmlspecialchars($full_name); ?></span>
                    </button>
                    <div class="dropdown-menu">
                        <a href="#profile" class="dropdown-item">My Profile</a>
                        <a href="#settings" class="dropdown-item">Settings</a>
                        <a href="#availability" class="dropdown-item">Availability</a>
                        <div class="dropdown-divider"></div>
                        <a href="logout.php" class="dropdown-item">Logout</a>
                    </div>
                </div>
            </header>
            
            <div class="content">
                <!-- Welcome Section -->
                <div class="welcome-section">
                    <h1>Welcome, Dr. <?php echo htmlspecialchars($full_name); ?>!</h1>
                    <p><?php echo htmlspecialchars($doctor['specialization']); ?> • <?php echo htmlspecialchars($doctor['hospital'] ?: 'MediConnect Hospital'); ?></p>
                </div>

                <!-- Success/Error Messages -->
                <?php if (isset($success)): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <!-- Dashboard Overview -->
                <div id="dashboard" class="tab-content active">
                    <h2 class="section-title">Today's Overview</h2>
                    
                    <div class="stats-grid">
                        <div class="stat-card">
                            <h3>Today's Appointments</h3>
                            <p><?php echo mysqli_num_rows($appointments_result); ?></p>
                        </div>
                        <div class="stat-card">
                            <h3>Total Patients</h3>
                            <p><?php echo $stats['total_patients']; ?></p>
                        </div>
                        <div class="stat-card">
                            <h3>Upcoming Appointments</h3>
                            <p><?php echo $stats['upcoming_appointments']; ?></p>
                        </div>
                        <div class="stat-card">
                            <h3>Completed</h3>
                            <p><?php echo $stats['completed_appointments']; ?></p>
                        </div>
                    </div>
                    
                    <h3 class="section-title">Today's Appointments</h3>
                    <?php if (mysqli_num_rows($appointments_result) > 0): ?>
                        <table class="appointments-table">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Patient Name</th>
                                    <th>Contact</th>
                                    <th>Reason</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($appointment = mysqli_fetch_assoc($appointments_result)): ?>
                                    <tr>
                                        <td><?php echo date('h:i A', strtotime($appointment['appointment_time'])); ?></td>
                                        <td><?php echo htmlspecialchars($appointment['first_name'] . ' ' . $appointment['last_name']); ?></td>
                                        <td><?php echo htmlspecialchars($appointment['phone']); ?></td>
                                        <td><?php echo htmlspecialchars($appointment['reason']); ?></td>
                                        <td>
                                            <span class="badge badge-<?php 
                                                echo $appointment['status'] == 'completed' ? 'success' : 
                                                     ($appointment['status'] == 'cancelled' ? 'error' : 'warning'); 
                                            ?>">
                                                <?php echo ucfirst($appointment['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                                <select name="status" onchange="this.form.submit()" style="padding: 0.25rem; border-radius: 0.25rem; border: 1px solid #d1d5db;">
                                                    <option value="scheduled" <?php echo $appointment['status'] == 'scheduled' ? 'selected' : ''; ?>>Scheduled</option>
                                                    <option value="completed" <?php echo $appointment['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                                    <option value="cancelled" <?php echo $appointment['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                                </select>
                                                <input type="hidden" name="update_status" value="1">
                                            </form>
                                            <button class="btn btn-primary" onclick="openPrescriptionModal(<?php echo $appointment['patient_id']; ?>, '<?php echo $appointment['first_name'] . ' ' . $appointment['last_name']; ?>')">Prescribe</button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="card" style="text-align: center; color: #6b7280;">
                            <p>No appointments scheduled for today.</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Appointments Tab -->
                <div id="appointments" class="tab-content">
                    <h2 class="section-title">All Appointments</h2>
                    <div class="card">
                        <h3>Appointment Management</h3>
                        <p>View and manage all your appointments across different dates.</p>
                        <!-- Add appointment calendar view here -->
                    </div>
                </div>
                
                <!-- Patients Tab -->
                <div id="patients" class="tab-content">
                    <h2 class="section-title">Patient Management</h2>
                    <?php if (mysqli_num_rows($patients_result) > 0): ?>
                        <table class="appointments-table">
                            <thead>
                                <tr>
                                    <th>Patient Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Last Visit</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($patient = mysqli_fetch_assoc($patients_result)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?></td>
                                        <td><?php echo htmlspecialchars($patient['email']); ?></td>
                                        <td><?php echo htmlspecialchars($patient['phone']); ?></td>
                                        <td><?php echo date('M j, Y', strtotime($patient['created_at'])); ?></td>
                                        <td>
                                            <button class="btn btn-primary" onclick="openPrescriptionModal(<?php echo $patient['id']; ?>, '<?php echo $patient['first_name'] . ' ' . $patient['last_name']; ?>')">Create Prescription</button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="card" style="text-align: center; color: #6b7280;">
                            <p>No patients found.</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Prescriptions Tab -->
                <div id="prescriptions" class="tab-content">
                    <h2 class="section-title">Prescriptions</h2>
                    <?php if (mysqli_num_rows($prescriptions_result) > 0): ?>
                        <table class="appointments-table">
                            <thead>
                                <tr>
                                    <th>Patient Name</th>
                                    <th>Medicines</th>
                                    <th>Dosage</th>
                                    <th>Duration</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($prescription = mysqli_fetch_assoc($prescriptions_result)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($prescription['first_name'] . ' ' . $prescription['last_name']); ?></td>
                                        <td><?php echo htmlspecialchars($prescription['medicines']); ?></td>
                                        <td><?php echo htmlspecialchars($prescription['dosage']); ?></td>
                                        <td><?php echo htmlspecialchars($prescription['duration']); ?></td>
                                        <td><?php echo date('M j, Y', strtotime($prescription['created_at'])); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="card" style="text-align: center; color: #6b7280;">
                            <p>No prescriptions created yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Schedule Tab -->
                <div id="schedule" class="tab-content">
                    <h2 class="section-title">Schedule Management</h2>
                    <div class="card">
                        <h3>Working Hours</h3>
                        <p>Manage your availability and working schedule.</p>
                        <!-- Add schedule management interface here -->
                    </div>
                </div>
                
                <!-- Profile Tab -->
                <div id="profile" class="tab-content">
                    <h2 class="section-title">Doctor Profile</h2>
                    <div class="card">
                        <div class="form-row">
                            <div class="form-group">
                                <label>First Name</label>
                                <p><?php echo htmlspecialchars($doctor['first_name']); ?></p>
                            </div>
                            <div class="form-group">
                                <label>Last Name</label>
                                <p><?php echo htmlspecialchars($doctor['last_name']); ?></p>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Email</label>
                                <p><?php echo htmlspecialchars($doctor['email']); ?></p>
                            </div>
                            <div class="form-group">
                                <label>Phone</label>
                                <p><?php echo htmlspecialchars($doctor['phone']); ?></p>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Specialization</label>
                                <p><?php echo htmlspecialchars($doctor['specialization']); ?></p>
                            </div>
                            <div class="form-group">
                                <label>Qualification</label>
                                <p><?php echo htmlspecialchars($doctor['qualification']); ?></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Hospital</label>
                            <p><?php echo htmlspecialchars($doctor['hospital']); ?></p>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Experience</label>
                                <p><?php echo htmlspecialchars($doctor['experience']); ?> years</p>
                            </div>
                            <div class="form-group">
                                <label>License Number</label>
                                <p><?php echo htmlspecialchars($doctor['license_number']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Prescription Modal -->
    <div id="prescriptionModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 2000; align-items: center; justify-content: center;">
        <div style="background: white; padding: 2rem; border-radius: 0.75rem; width: 90%; max-width: 600px; max-height: 90vh; overflow-y: auto;">
            <h3 style="margin-bottom: 1.5rem; color: var(--primary);">Create Prescription</h3>
            <form method="POST" id="prescriptionForm">
                <input type="hidden" name="patient_id" id="modal_patient_id">
                <div class="form-group">
                    <label>Patient Name</label>
                    <input type="text" id="modal_patient_name" readonly style="background: #f9fafb;">
                </div>
                <div class="form-group">
                    <label>Medicines *</label>
                    <input type="text" name="medicines" required placeholder="Enter medicine names">
                </div>
                <div class="form-group">
                    <label>Dosage *</label>
                    <input type="text" name="dosage" required placeholder="e.g., 500mg, 1 tablet">
                </div>
                <div class="form-group">
                    <label>Instructions</label>
                    <textarea name="instructions" placeholder="Usage instructions..."></textarea>
                </div>
                <div class="form-group">
                    <label>Duration *</label>
                    <input type="text" name="duration" required placeholder="e.g., 7 days, 2 weeks">
                </div>
                <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                    <button type="submit" name="create_prescription" class="btn btn-primary">Create Prescription</button>
                    <button type="button" class="btn btn-outline" onclick="closePrescriptionModal()">Cancel</button>
                </div>
            </form>
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

        // Prescription Modal Functions
        function openPrescriptionModal(patientId, patientName) {
            document.getElementById('modal_patient_id').value = patientId;
            document.getElementById('modal_patient_name').value = patientName;
            document.getElementById('prescriptionModal').style.display = 'flex';
        }

        function closePrescriptionModal() {
            document.getElementById('prescriptionModal').style.display = 'none';
            document.getElementById('prescriptionForm').reset();
        }

        // Close modal when clicking outside
        document.getElementById('prescriptionModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closePrescriptionModal();
            }
        });
    </script>
</body>
</html>