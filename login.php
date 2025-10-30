<?php include 'includes/navbar.php';
require_once 'includes/db_connect.php'; 

// --- Start of page logic using the $conn variable ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check login credentials using $conn...
} ?>
<div class="container">
  <div class="card" style="text-align:center;padding:26px">
    <h2 style="color:var(--primary)">Login to MediConnect</h2>
    <p class="small">Choose your role to sign in</p>
    <div style="display:flex;gap:12px;justify-content:center;margin-top:18px">
      <a class="btn green" href="patientlogin.php">👤 Patient Login</a>
      <a class="btn" href="doctorlogin.php">🩺 Doctor Login</a>
      <a class="btn" style="background:linear-gradient(90deg,#04506b,#008c6e);" href="adminlogin.php">🛡 Admin Login</a>
    </div>
  </div>
</div>
<?php include 'includes/footer.php'; ?>