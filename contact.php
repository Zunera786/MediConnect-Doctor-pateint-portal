<?php include 'includes/navbar.php'; ?>
<div class="container">
  <h2 style="color:var(--primary)">Contact Us</h2>
  <div class="contact-wrap">
    <div class="contact-card">
      <h3>Get in touch</h3>
      <p class="small">Address: MediConnect HQ, Bengaluru, India</p>
      <p class="small">Email: support@mediconnect.com</p>
      <p class="small">Phone: +91-9876543210</p>
    </div>
    <div class="contact-card">
      <h3>Send a message</h3>
      <form>
        <div class="form-row">
          <div style="flex:1">
            <label class="label">Name</label>
            <input class="input" placeholder="Your name">
          </div>
          <div style="width:220px">
            <label class="label">Phone</label>
            <input class="input" placeholder="+91">
          </div>
        </div>
        <div style="margin-top:12px">
          <label class="label">Email</label>
          <input class="input" placeholder="you@example.com">
        </div>
        <div style="margin-top:12px">
          <label class="label">Message</label>
          <textarea class="input" rows="4" placeholder="How can we help?"></textarea>
        </div>
        <div style="margin-top:12px">
          <button class="btn green" type="button">Send Message</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php include 'includes/footer.php'; ?>