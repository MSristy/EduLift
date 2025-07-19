<?php
// Database connection
$host = "localhost";
$username = "root";
$password = ""; // Default for XAMPP
$database = "loan";

$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $student_id = $_POST['student_id'];
    $versity_email = $_POST['versity_email'];
    $mobile = $_POST['mobile'];
    $about = $_POST['about'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Handle profile image
    $profile_img = $_FILES['profile']['name'];
    $tmp_name = $_FILES['profile']['tmp_name'];
    $upload_dir = "images/";
    move_uploaded_file($tmp_name, $upload_dir . $profile_img);

    $sql = "INSERT INTO lenders_signup (first_name, student_id, versity_email, mobile, profile_img, about, password)
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("sssssss", $first_name, $student_id, $versity_email, $mobile, $profile_img, $about, $password);

    if ($stmt->execute()) {
        echo "<script>('Registration successful!'); window.location.href='lenderlogin.php';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register As A Lender</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Roboto', Arial, sans-serif;
      min-height: 100vh;
      background: linear-gradient(120deg, #e0f7fa 0%, #e6f4ea 100%);
    }
    .form-container {
      max-width: 430px;
      margin: 60px auto;
      background: #fff;
      padding: 38px 32px 28px 32px;
      border-radius: 18px;
      box-shadow: 0 8px 32px rgba(21,66,26,0.13), 0 1.5px 8px rgba(0,0,0,0.08);
      position: relative;
      z-index: 2;
      overflow: hidden;
    }
    .accent-bar {
      height: 7px;
      width: 60px;
      background: linear-gradient(90deg, #0b8d3c 60%, #003366 100%);
      border-radius: 6px;
      margin: 0 auto 18px auto;
      display: block;
    }
    h2 {
      text-align: center;
      color: #003366;
      font-weight: 700;
      margin-bottom: 6px;
      letter-spacing: 0.5px;
    }
    .subtitle {
      text-align: center;
      color: #0b8d3c;
      font-weight: 500;
      margin-bottom: 24px;
      font-size: 1.05rem;
    }
    form {
      display: flex;
      flex-direction: column;
      gap: 18px;
    }
    .row {
      display: flex;
      gap: 14px;
    }
    .row input, textarea {
      flex: 1;
      padding: 12px 12px 12px 40px;
      border: 1.5px solid #cbe3d3;
      border-radius: 7px;
      font-size: 1rem;
      background: #f6fef9;
      transition: border-color 0.2s, box-shadow 0.2s;
      outline: none;
      box-sizing: border-box;
    }
    .row input:focus, textarea:focus {
      border-color: #0b8d3c;
      box-shadow: 0 0 0 2px #cbe3d3;
      background: #fff;
    }
    textarea {
      resize: vertical;
      min-height: 80px;
      max-height: 180px;
    }
    .button-row {
      display: flex;
      align-items: center;
      gap: 14px;
    }
    .button-row input[type="file"] {
      flex: 1;
      padding: 10px;
      border-radius: 7px;
      font-size: 1rem;
      border: 1.5px solid #cbe3d3;
      background: #f6fef9;
    }
    .profile-preview {
      width: 48px;
      height: 48px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid #cbe3d3;
      margin-right: 10px;
      display: none;
    }
    .submit-btn {
      width: 100%;
      padding: 13px;
      background: linear-gradient(90deg, #003366 60%, #0b8d3c 100%);
      color: #fff;
      font-size: 1.1rem;
      border: none;
      border-radius: 7px;
      cursor: pointer;
      font-weight: 600;
      letter-spacing: 0.5px;
      box-shadow: 0 2px 8px rgba(21,66,26,0.10);
      transition: background 0.2s, box-shadow 0.2s, transform 0.1s;
      margin-top: 0.5rem;
    }
    .submit-btn:hover {
      background: linear-gradient(90deg, #0b8d3c 60%, #003366 100%);
      box-shadow: 0 4px 16px rgba(21,66,26,0.13);
      transform: translateY(-2px) scale(1.03);
    }
    .input-container {
      position: relative;
      flex: 2;
      display: flex;
      align-items: center;
    }
    .input-container i {
      position: absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      font-size: 1.1rem;
      color: #0b8d3c;
      pointer-events: none;
    }
    .toggle-password {
      position: absolute;
      right: 14px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: #003366;
      font-size: 1.1rem;
      cursor: pointer;
      z-index: 2;
    }
    @media (max-width: 600px) {
      .form-container {
        padding: 18px 6vw 18px 6vw;
        margin: 30px 0;
      }
      .row {
        flex-direction: column;
        gap: 8px;
      }
    }
  </style>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

<?php include('templates/header2.php'); ?>

<div class="form-container">
  <span class="accent-bar"></span>
  <h2>Register As A Lender</h2>
  <div class="subtitle">Lend Money to Verified Borrowers and Earn Good Returns</div>
  <form action="" method="POST" enctype="multipart/form-data" autocomplete="off">
    <div class="row">
      <div class="input-container">
        <i class="fas fa-user"></i>
        <input type="text" name="first_name" placeholder="Full Name" required>
      </div>
    </div>
    <div class="row">
      <div class="input-container">
        <i class="fas fa-id-card"></i>
        <input type="text" name="student_id" placeholder="Student ID" required>
      </div>
      <div class="input-container">
        <i class="fas fa-envelope"></i>
        <input type="email" name="versity_email" placeholder="University Email" required>
      </div>
    </div>
    <div class="row">
      <div class="input-container">
        <i class="fas fa-phone"></i>
        <input type="text" name="mobile" placeholder="Mobile No" required>
      </div>
    </div>
    <div class="row">
      <div class="input-container" style="position:relative;">
        <i class="fas fa-lock"></i>
        <input type="password" name="password" id="password" placeholder="Password" required>
        <button type="button" class="toggle-password" onclick="togglePassword('password', this)"><i class="fa fa-eye"></i></button>
      </div>
      <div class="input-container" style="position:relative;">
        <i class="fas fa-lock"></i>
        <input type="password" name="confirm_password" id="confirm_password" placeholder="Re-type Password" required>
        <button type="button" class="toggle-password" onclick="togglePassword('confirm_password', this)"><i class="fa fa-eye"></i></button>
      </div>
    </div>
    <div class="button-row">
      <img src="#" alt="Profile Preview" class="profile-preview" id="profilePreview">
      <input type="file" name="profile" id="profileInput" accept="image/*" required>
    </div>
    <textarea name="about" placeholder="About Yourself" required></textarea>
    <input type="submit" class="submit-btn" value="SUBMIT">
  </form>
</div>

<?php include('templates/footer.php'); ?>

<script>
// Password visibility toggle
function togglePassword(id, btn) {
  const input = document.getElementById(id);
  if (input.type === 'password') {
    input.type = 'text';
    btn.innerHTML = '<i class="fa fa-eye-slash"></i>';
  } else {
    input.type = 'password';
    btn.innerHTML = '<i class="fa fa-eye"></i>';
  }
}
// Profile image preview
const profileInput = document.getElementById('profileInput');
const profilePreview = document.getElementById('profilePreview');
profileInput.addEventListener('change', function(e) {
  const file = e.target.files[0];
  if (file) {
    profilePreview.src = URL.createObjectURL(file);
    profilePreview.style.display = 'block';
  } else {
    profilePreview.src = '#';
    profilePreview.style.display = 'none';
  }
});
</script>
</body>
</html>
