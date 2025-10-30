<?php include 'includes/navbar.php';
require_once 'includes/db_connect.php'; 

// --- Start of page logic using the $conn variable ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check login credentials using $conn...
} ?>
<div class="container">
  <div class="card" style="text-align:center;padding:26px">
    <h2 style="color:var(--primary)">Create an Account</h2>
    <p class="small">Register as a Patient or a Doctor</p>
    <div style="display:flex;gap:12px;justify-content:center;margin-top:18px">
      <a class="btn green" href="patientregister.php">👤 Register as Patient</a>
      <a class="btn" href="doctorregister.php">🩺 Register as Doctor</a>
    </div>
  </div>
</div>
<?php include 'includes/footer.php'; ?>