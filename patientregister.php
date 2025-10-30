<?php include 'includes/navbar.php';
require_once 'includes/db_connect.php'; 

// --- Start of page logic using the $conn variable ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check login credentials using $conn...
} ?>
<div class="container form-page">
  <div class="form-visual">
    <div style="font-size:3rem">👤</div>
    <div style="font-weight:700">Patient Registration</div>
    <div class="small">Quick, secure sign-up to manage your health online</div>
  </div>
  <div class="form-card">
    <h3 style="color:var(--primary)">Personal Information</h3>
    <form>
      <div class="form-row form-section">
        <div style="flex:1">
          <label class="label">Full Name</label>
          <input class="input" placeholder="Jane Doe">
        </div>
        <div style="width:160px">
          <label class="label">Gender</label>
          <select class="input"><option>Female</option><option>Male</option><option>Other</option></select>
        </div>
      </div>

      <div class="form-row form-section">
        <div style="width:160px">
          <label class="label">DOB</label>
          <input class="input" type="date">
        </div>
        <div style="flex:1">
          <label class="label">Aadhaar Number</label>
          <input class="input" placeholder="1234-5678-9012">
        </div>
      </div>

      <div class="form-row form-section">
        <div style="flex:1">
          <label class="label">Email</label>
          <input class="input" placeholder="you@example.com">
        </div>
        <div style="width:160px">
          <label class="label">Phone</label>
          <input class="input" placeholder="+91">
        </div>
      </div>

      <div class="form-section">
        <label class="label">Address</label>
        <input class="input" placeholder="Street, City, State">
      </div>

      <h4 style="color:var(--primary);margin-top:8px">Health Details</h4>
      <div class="form-row form-section">
        <div style="width:160px">
          <label class="label">Blood Group</label>
          <select class="input"><option>A+</option><option>B+</option><option>O+</option><option>AB+</option></select>
        </div>
        <div style="flex:1">
          <label class="label">Allergies</label>
          <input class="input" placeholder="E.g., Penicillin, Pollen">
        </div>
      </div>

      <div class="form-section">
        <label class="label">Recent Health Issues</label>
        <textarea class="input" rows="3" placeholder="Describe recent conditions or concerns"></textarea>
      </div>

      <div class="form-section">
        <label class="label">Current Prescriptions</label>
        <textarea class="input" rows="2" placeholder="List current medications and dosages"></textarea>
      </div>

      <h4 style="color:var(--primary);margin-top:8px">Account Security</h4>
      <div class="form-row form-section">
        <div style="flex:1">
          <label class="label">Password</label>
          <input class="input" type="password" placeholder="Create a password">
        </div>
        <div style="flex:1">
          <label class="label">Confirm Password</label>
          <input class="input" type="password" placeholder="Confirm password">
        </div>
      </div>

      <div style="margin-top:12px;display:flex;justify-content:space-between;align-items:center">
        <div class="small">Already have an account? <a href="login.php">Login</a></div>
        <button class="btn green" type="button">🧾 Register as Patient</button>
      </div>
    </form>
  </div>
</div>
<?php include 'includes/footer.php'; ?>