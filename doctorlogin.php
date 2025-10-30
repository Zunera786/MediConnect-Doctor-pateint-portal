<?php include 'includes/navbar.php';
require_once 'includes/db_connect.php'; 

// --- Start of page logic using the $conn variable ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check login credentials using $conn...
} ?>
<div class="container form-page">
  <div class="form-visual">
    <div style="font-size:3rem">🩺</div>
    <div style="font-weight:700">Doctor Portal</div>
    <div class="small">Secure access for healthcare professionals</div>
  </div>
  <div class="form-card">
    <h3 style="color:var(--primary)">🩺 Doctor Login</h3>
    <form>
      <div class="form-section">
        <label class="label">Aadhaar Number</label>
        <input class="input" placeholder="Enter Aadhaar number">
      </div>
      <div class="form-section">
        <label class="label">Password</label>
        <input class="input" type="password" placeholder="Enter password">
      </div>
      <div style="display:flex;gap:12px;align-items:center;justify-content:space-between">
        <a class="small" href="#">Forgot Password?</a>
        <button class="btn" type="button">🔐 Login</button>
      </div>
      <div style="margin-top:12px" class="small">Don't have an account? <a href="register.php">Register</a></div>
    </form>
  </div>
</div>
<?php include 'includes/footer.php'; ?>