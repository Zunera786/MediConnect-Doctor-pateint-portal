<?php include 'includes/navbar.php';
require_once 'includes/db_connect.php'; 

// --- Start of page logic using the $conn variable ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check login credentials using $conn...
} ?>
<div class="container">
  <h2 style="color:var(--primary)">💊 Prescriptions</h2>
  <div class="card"><p class="small">Prescription history and active meds will appear here.</p></div>
</div>
<?php include 'includes/footer.php'; ?>