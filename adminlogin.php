<?php include 'includes/navbar.php'; ?>
<div class="container form-page">
  <div class="form-visual">
    <div style="font-size:3rem">🛡</div>
    <div style="font-weight:700">Admin Console</div>
    <div class="small">Administrative access to manage the platform</div>
  </div>
  <div class="form-card">
    <h3 style="color:var(--primary)">🛡 Admin Login</h3>
    <form>
      <div class="form-section">
        <label class="label">Email or Username</label>
        <input class="input" placeholder="admin@domain.com">
      </div>
      <div class="form-section">
        <label class="label">Password</label>
        <input class="input" type="password" placeholder="Enter password">
      </div>
      <div style="display:flex;gap:12px;align-items:center;justify-content:space-between">
        <a class="small" href="#">Forgot Password?</a>
        <button class="btn" style="background:linear-gradient(90deg,#04506b,#008c6e);color:white" type="button">🔐 Login</button>
      </div>
    </form>
  </div>
</div>
<?php include 'includes/footer.php'; ?>