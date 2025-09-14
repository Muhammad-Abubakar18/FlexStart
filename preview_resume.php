<?php
// You may need to sanitize and validate input in production!
$name = $_POST['fullName'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$address = $_POST['address'] ?? '';
$summary = $_POST['summary'] ?? '';
$profilePic = $_FILES['profile_pic']['tmp_name'] ?? '';
$jobField = $_POST['jobField'] ?? '';

// Skills
$skills = $_POST['skills'] ?? [];
// Experience
$jobTitles = $_POST['jobTitle'] ?? [];
$companies = $_POST['company'] ?? [];
$durations = $_POST['duration'] ?? [];
$descriptions = $_POST['description'] ?? [];
// Education
$degrees = $_POST['degree'] ?? [];
$universities = $_POST['university'] ?? [];
$locations = $_POST['location'] ?? [];
$gradYears = $_POST['grad-year'] ?? [];
// Languages
$languages = $_POST['languages'] ?? [];
$levels = $_POST['language-level'] ?? [];
// Activities
$activities = $_POST['activities'] ?? [];

// Handle profile picture
$profile_pic_filename = 'uploads/profilepic1.jpeg'; // Default profile picture

// If a new profile picture was uploaded
if (!empty($_FILES['profile_pic']['tmp_name'])) {
    $uploadDir = 'uploads/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    // Generate unique filename
    $fileName = time() . '_' . basename($_FILES['profile_pic']['name']);
    $uploadFile = $uploadDir . $fileName;
    
    // Move uploaded file
    if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $uploadFile)) {
        $profile_pic_filename = $uploadFile;
    }
}

ob_start();
?>
<div class="resume-template">
  <div class="resume-sidebar">
    <div class="profile-pic">
      <?php echo '<!-- PROFILE PIC PATH: ' . $profile_pic_filename . ' -->'; ?>
      <img src="<?php echo htmlspecialchars($profile_pic_filename); ?>" alt="Profile Picture">
    </div>
    <h2><?php echo htmlspecialchars($name); ?></h2>
    <h4><?php echo htmlspecialchars($jobField); ?></h4>
    <ul class="contact-info">
      <li><i class="fa fa-envelope"></i> <?php echo htmlspecialchars($email); ?></li>
      <li><i class="fa fa-phone"></i> <?php echo htmlspecialchars($phone); ?></li>
      <li><i class="fa fa-map-marker-alt"></i> <?php echo htmlspecialchars($address); ?></li>
    </ul>
    <div class="skills">
      <h5>Relevant Skills</h5>
      <ul>
        <?php foreach ($skills as $skill) echo "<li>".htmlspecialchars($skill)."</li>"; ?>
      </ul>
    </div>
    <div class="skills">
      <h5>Languages</h5>
      <ul>
        <?php foreach ($languages as $i => $lang) echo "<li>".htmlspecialchars($lang)." (".htmlspecialchars($levels[$i] ?? '').")</li>"; ?>
      </ul>
    </div>
    <div class="skills">
      <h5>Activities</h5>
      <ul>
        <?php foreach ($activities as $act) echo "<li>".htmlspecialchars($act)."</li>"; ?>
      </ul>
    </div>
  </div>
  <div class="resume-main">
    <div class="section">
      <h3>Professional Summary</h3>
      <p><?php echo nl2br(htmlspecialchars($summary)); ?></p>
    </div>
    <div class="section">
      <h3>Work Experience</h3>
      <?php
      for ($i = 0; $i < count($jobTitles); $i++) {
        echo "<div>
          <strong>".htmlspecialchars($jobTitles[$i])."</strong> at ".htmlspecialchars($companies[$i])."<br>
          <em>".htmlspecialchars($durations[$i])."</em>
          <p>".nl2br(htmlspecialchars($descriptions[$i]))."</p>
        </div>";
      }
      ?>
    </div>
    <div class="section">
      <h3>Education History</h3>
      <?php
      for ($i = 0; $i < count($degrees); $i++) {
        echo "<div>
          <strong>".htmlspecialchars($degrees[$i])."</strong><br>
          ".htmlspecialchars($universities[$i]).", ".htmlspecialchars($locations[$i])." (".htmlspecialchars($gradYears[$i]).")
        </div>";
      }
      ?>
    </div>
  </div>
</div>
<input type="hidden" id="profile_pic_url" name="profile_pic_url" value="">
<?php
echo ob_get_clean();
?>
