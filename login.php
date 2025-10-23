<?php
include 'config.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_type'] === 'doctor') {
        header("Location: doctor_dashboard.php");
    } else {
        header("Location: dashboard.php");
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $user_type = sanitize($_POST['user_type']);
    
    if ($user_type === 'patient') {
        // Patient login
        $query = "SELECT * FROM patients WHERE email = '$email'";
        $result = mysqli_query($con, $query);
        
        if ($result && mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);
            
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_type'] = 'patient';
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                
                header("Location: dashboard.php");
                exit();
            }
        }
    } else {
        // Doctor login
        $query = "SELECT * FROM doctors WHERE email = '$email' AND is_verified = TRUE";
        $result = mysqli_query($con, $query);
        
        if ($result && mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);
            
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_type'] = 'doctor';
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                
                header("Location: doctor_dashboard.php");
                exit();
            }
        }
    }
    
    $error = "Invalid email or password";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MediConnect</title>
    <style>
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
        }
        body { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            min-height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
        }
        .login-container { 
            background: white; 
            padding: 2rem; 
            border-radius: 10px; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.1); 
            width: 100%; 
            max-width: 400px; 
        }
        .logo { 
            text-align: center; 
            margin-bottom: 2rem; 
            color: #2563eb; 
            font-size: 2rem; 
            font-weight: bold; 
        }
        .user-type-toggle {
            display: flex;
            margin-bottom: 1.5rem;
            border: 1px solid #d1d5db;
            border-radius: 5px;
            overflow: hidden;
        }
        .user-type-btn {
            flex: 1;
            padding: 0.75rem;
            background: white;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
        }
        .user-type-btn.active {
            background: #2563eb;
            color: white;
        }
        .form-group { 
            margin-bottom: 1rem; 
        }
        label { 
            display: block; 
            margin-bottom: 0.5rem; 
            color: #374151; 
            font-weight: 500; 
        }
        input[type="email"], 
        input[type="password"] { 
            width: 100%; 
            padding: 0.75rem; 
            border: 1px solid #d1d5db; 
            border-radius: 5px; 
            font-size: 1rem; 
        }
        .btn { 
            width: 100%; 
            padding: 0.75rem; 
            background: #2563eb; 
            color: white; 
            border: none; 
            border-radius: 5px; 
            font-size: 1rem; 
            cursor: pointer; 
            transition: background 0.3s;
        }
        .btn:hover { 
            background: #1d4ed8; 
        }
        .error { 
            background: #fee2e2; 
            color: #dc2626; 
            padding: 0.75rem; 
            border-radius: 5px; 
            margin-bottom: 1rem; 
            text-align: center;
        }
        .register-link {
            text-align: center;
            margin-top: 1rem;
        }
        .register-link a {
            color: #2563eb;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">MediConnect</div>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="user-type-toggle">
                <button type="button" class="user-type-btn active" data-type="patient">Patient</button>
                <button type="button" class="user-type-btn" data-type="doctor">Doctor</button>
            </div>
            <input type="hidden" name="user_type" id="user_type" value="patient">
            
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>
        
        <div class="register-link">
            <p>Don't have an account? <a href="register.php">Register here</a></p>
        </div>
    </div>

    <script>
        document.querySelectorAll('.user-type-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.user-type-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                document.getElementById('user_type').value = this.dataset.type;
            });
        });
    </script>
</body>
</html>