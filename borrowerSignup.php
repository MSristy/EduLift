<?php
// Database connection
$host = "localhost";
$username = "root";
$password = ""; 
$database = "loan";

$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset(
        $_POST['first_name'], $_POST['last_name'], $_POST['student_id'], $_POST['email_verify'], 
        $_POST['dob'], $_POST['parent_number'], $_POST['city'], $_POST['gender'], 
        $_POST['nid'], $_POST['email_personal'], $_POST['mobile'], $_POST['password']
    ) || !isset($_FILES['profile_image']) || !isset($_FILES['student_id_card'])) {
        die("Error: One or more required fields are missing.");
    }

    // Collect form data
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $student_id = $_POST['student_id'];
    $email_verify = $_POST['email_verify'];
    $dob = $_POST['dob'];
    $parent_number = $_POST['parent_number'];
    $city = $_POST['city'];
    $gender = $_POST['gender'];
    $nid = $_POST['nid'];
    $email_personal = $_POST['email_personal'];
    $mobile = $_POST['mobile'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Handle profile image upload
    if ($_FILES['profile_image']['error'] == 0) {
        $profile_img = $_FILES['profile_image']['name'];
        $tmp_name_profile = $_FILES['profile_image']['tmp_name'];
        move_uploaded_file($tmp_name_profile, "images/" . $profile_img);
    } else {
        die("Error: Profile image upload failed.");
    }

    // Handle student ID card image upload
    if ($_FILES['student_id_card']['error'] == 0) {
        $student_id_card_img = $_FILES['student_id_card']['name'];
        $tmp_name_id_card = $_FILES['student_id_card']['tmp_name'];
        move_uploaded_file($tmp_name_id_card, "images/" . $student_id_card_img);
    } else {
        die("Error: Student ID Card upload failed.");
    }

    // Insert into database
    $sql = "INSERT INTO borrower_signup 
            (first_name, last_name, student_id, university_email, dob, parent_number, city, gender, nid, personal_email, mobile, profile_img, student_id_card_img, password) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param(
        "ssssssssssssss",
        $first_name, $last_name, $student_id, $email_verify, $dob, $parent_number, 
        $city, $gender, $nid, $email_personal, $mobile, $profile_img, $student_id_card_img, $password
    );

    if ($stmt->execute()) {
        echo "<script>alert('Registration successful!'); window.location.href='borrowerlogin.php';</script>";
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
    <title>Apply for Personal Loan</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
      * { box-sizing: border-box; }
      body {
        margin: 0;
        font-family: 'Segoe UI', sans-serif;
        background: linear-gradient(135deg, #e0eafc 0%, #cfdef3 100%);
        min-height: 100vh;
      }
      .container {
        display: flex;
        min-height: 90vh;
        max-width: 1100px;
        margin: 40px auto;
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.18);
        overflow: hidden;
      }
      .left-side {
        background: linear-gradient(135deg, #25314d 60%, #3e537c 100%);
        color: white;
        padding: 40px 25px;
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        min-width: 320px;
      }
      .profile-icon-container {
        display: flex;
        align-items: center;
        margin-bottom: 30px;
      }
      .profile-icon {
        width: 90px;
        height: 90px;
        background: linear-gradient(135deg, #fff 60%, #e0eafc 100%);
        color: #25314d;
        border-radius: 50%;
        font-size: 40px;
        display: flex;
        justify-content: center;
        align-items: center;
        margin-right: 18px;
        box-shadow: 0 2px 12px 0 rgba(31, 38, 135, 0.10);
      }
      .upload-label {
        font-size: 14px;
        margin-bottom: 7px;
        color: #f68b1f;
        font-weight: 500;
      }
      .upload-input input[type="file"] {
        font-size: 13px;
        color: #fff;
        background-color: transparent;
        border: none;
        cursor: pointer;
      }
      .left-side h2 {
        font-size: 18px;
        margin-bottom: 18px;
        margin-left: 3px;
        border-bottom: 2px solid #f68b1f;
        padding-bottom: 7px;
        width: 100%;
        letter-spacing: 1px;
      }
      .left-form {
        width: 100%;
      }
      .left-form .input-box {
        position: relative;
        margin-bottom: 18px;
      }
      .left-form .input-box i {
        position: absolute;
        top: 12px;
        left: 10px;
        color: #666;
        font-size: 15px;
      }
      .left-form input,
      .left-form select {
        width: 80%;
        padding: 12px 12px 12px 38px;
        border-radius: 8px;
        border: none;
        font-size: 15px;
        background-color: #f7f3f0;
        color: #222;
        transition: 0.3s box-shadow, 0.3s border;
        box-shadow: 0 1px 4px 0 rgba(31, 38, 135, 0.04);
      }
      .left-form input:focus,
      .left-form select:focus,
      .input-box input:focus,
      .input-box select:focus {
        outline: none;
        border: 2px solid #f68b1f;
        box-shadow: 0 0 8px 0 #f68b1f33;
        background-color: #fff;
      }
      .left-form input:hover,
      .left-form select:hover,
      .input-box input:hover,
      .input-box select:hover {
        border: 2px solid #f68b1f;
        background-color: #fff;
      }
      .right-side {
        flex: 2;
        padding: 50px 40px;
        display: flex;
        flex-direction: column;
        background: #f7fafc;
        min-width: 350px;
      }
      .form-title {
        font-size: 2.2em;
        font-weight: bold;
        color: #25314d;
        margin-bottom: 18px;
        letter-spacing: 1px;
        text-align: center;
      }
      .form-section {
        width: 100%;
        background: #fff;
        padding: 38px 30px 30px 30px;
        border-radius: 14px;
        box-shadow: 0 4px 18px 0 rgba(31, 38, 135, 0.07);
        margin-top: 10px;
        animation: fadeIn 0.7s;
      }
      @keyframes fadeIn {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
      }
      .form-subtitle {
        color: #f68b1f;
        font-size: 1.1em;
        margin-bottom: 10px;
        text-align: center;
        font-weight: 600;
      }
      .form-note {
        font-size: 15px;
        color: #777;
        margin-bottom: 22px;
        text-align: center;
      }
      .input-group {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 18px;
        margin-bottom: 18px;
      }
      .input-box {
        position: relative;
        background: #f7f3f0;
        border-radius: 8px;
        box-shadow: 0 1px 4px 0 rgba(31, 38, 135, 0.04);
        padding: 0 0 0 0;
        transition: box-shadow 0.3s;
      }
      .input-box:focus-within {
        box-shadow: 0 0 8px 0 #f68b1f33;
      }
      .input-box i {
        position: absolute;
        top: 12px;
        left: 10px;
        color: #888;
        font-size: 15px;
      }
      .input-box input,
      .input-box select {
        width: 100%;
        padding: 12px 12px 12px 38px;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        outline: none;
        font-size: 15px;
        background: transparent;
        transition: border 0.3s, box-shadow 0.3s;
      }
      .input-box input:focus,
      .input-box select:focus {
        border: 2px solid #f68b1f;
        box-shadow: 0 0 8px 0 #f68b1f33;
        background: #fff;
      }
      .gender-box {
        display: flex;
        gap: 18px;
        margin-top: 10px;
      }
      .gender-box label {
        font-weight: 500;
        color: #25314d;
      }
      .submit-btn {
        margin-top: 30px;
        background: linear-gradient(90deg, #f68b1f 0%, #25314d 100%);
        color: white;
        padding: 14px 40px;
        border: none;
        border-radius: 8px;
        font-weight: bold;
        cursor: pointer;
        display: block;
        margin-left: auto;
        margin-right: auto;
        font-size: 1.15em;
        box-shadow: 0 2px 8px 0 rgba(31, 38, 135, 0.10);
        letter-spacing: 1px;
        transition: background 0.3s, transform 0.2s;
      }
      .submit-btn:hover {
        background: linear-gradient(90deg, #25314d 0%, #f68b1f 100%);
        color: #fff;
        transform: translateY(-2px) scale(1.03);
        box-shadow: 0 4px 16px 0 rgba(31, 38, 135, 0.18);
      }
      .toggle-password {
        position: absolute;
        top: 12px;
        right: 10px;
        cursor: pointer;
        font-size: 15px;
        color: #888;
        transition: color 0.2s;
      }
      .toggle-password:hover {
        color: #f68b1f;
      }
      @media (max-width: 900px) {
        .container {
          flex-direction: column;
          max-width: 98vw;
        }
        .left-side, .right-side {
          min-width: unset;
          width: 100%;
          border-radius: 0;
        }
        .right-side {
          padding: 30px 10px;
        }
      }
      @media (max-width: 600px) {
        .container {
          margin: 10px 0;
        }
        .form-section {
          padding: 18px 5px 18px 5px;
        }
        .form-title {
          font-size: 1.3em;
        }
      }
    </style>
  </head>
  <body>

  <?php include('templates/header2.php'); ?>

  <form method="POST" action="borrowerSignup.php" enctype="multipart/form-data">
    <div class="container">
      <div class="left-side">
        <div class="profile-icon-container">
          <div class="profile-icon"><i class="fas fa-user"></i></div>
          <div class="upload-input">
            <div class="upload-label">Choose your profile image</div>
            <input type="file" name="profile_image" accept="images/*">
          </div>
        </div>

        
        
        <h2>Borrower Information</h2>
        <div class="left-form">
        <div class="input-box">
      <i class="fas fa-id-badge"></i>
      <input type="text" name="student_id" id="student_id" placeholder="Student Id "
            pattern="^(011|111|021)\d{3}\d{3,5}$"
            title="Type a valid student ID (e.g. 011 221 482)"
            required>
    </div>

    <div class="input-box">
      <i class="fas fa-envelope"></i>
      <input type="email" name="email_verify" id="email_verify"
            placeholder="e.g. 221@uiu.ac.bd"
            pattern="^[a-zA-Z0-9]+@uiu\.ac\.bd$"
            title="Must be a UIU email "
            required>
    </div>

    <div class="input-box">
          <i class="fas fa-calendar"></i>
          <input 
            type="text" 
            name="dob" 
            id="dob" 
            placeholder="Date of Birth" 
            onfocus="this.type='date'" 
            onblur="if(this.value===''){this.type='text'}" 
            required>
          
</div>
          <div class="input-box"><i class="fas fa-phone"></i><input type="text" name="parent_number" placeholder="Parent's Number" required></div>
          <div class="input-box"><i class="fas fa-city"></i>
            <select name="city" required>
              <option value="">Select City</option>
              <option value="Dhaka">Dhaka</option>
              <option value="Chattogram">Chattogram</option>
              <option value="Dhaka">Rajshahi</option>
              <option value="Dhaka">Barisal</option>
              <option value="Dhaka">Sylhet</option>
            </select>
          </div>
         
                    <label class="upload-label" style="color:#f68b1f;"> Upload Student ID Card</label>
                    <div class="input-box"> <i class="fas fa-id-card"></i><input type="file" name="student_id_card" accept="image/*" required>
                  </div>

        </div>
      </div>

      <div class="right-side">
        <div class="form-title">Apply for Personal Loan</div>
        <div class="form-section">
          <div class="form-subtitle">Get Personal Loan at Attractive Interest Rates</div>
          <div class="form-note">Create Borrower Profile</div>
          <div class="input-group">
            <div class="input-box"><i class="fas fa-user"></i><input type="text" name="first_name" placeholder="First Name" required></div>
            
            <div class="input-box"><i class="fas fa-user"></i><input type="text" name="last_name" placeholder="Last Name" required></div>
            <div class="input-box">
              <label>Gender:</label>
              <div class="gender-box">
                <label><input type="radio" name="gender" value="Male" required> Male</label>
                <label><input type="radio" name="gender" value="Female"> Female</label>
              </div>
            </div>
            <div class="input-box">
              <i class="fas fa-lock"></i>
              <input type="password" name="password" id="password" placeholder="Password" required>
              <span class="toggle-password" onclick="togglePassword('password')">
            </div>
            <div class="input-box">
              <i class="fas fa-lock"></i>
              <input type="password" id="confirm_password" placeholder="Retype Password" required>
              <span class="toggle-password" onclick="togglePassword('confirm_password')">
            </div>
            <div class="input-box"><i class="fas fa-id-card"></i><input type="text" name="nid" placeholder="NID Number" required></div>
            <div class="input-box"><i class="fas fa-envelope"></i><input type="email" name="email_personal" placeholder="Email-id (Personal)" required></div>
            <div class="input-box"><i class="fas fa-phone"></i><input type="text" name="mobile" placeholder="Mobile Number" required></div>

            
          </div>
          <button type="submit" class="submit-btn">SUBMIT</button>
        </div>
      </div>
  </div>
</form>

<script>
document.querySelector("form").addEventListener("submit", function (e) {
  const id = document.getElementById("student_id").value.trim();
  const email = document.getElementById("email_verify").value.trim().toLowerCase();
  
  const fname = document.querySelector("input[name='first_name']").value.trim().toLowerCase();
  const mname = document.querySelector("input[name='middle_name']").value.trim().toLowerCase();
  const lname = document.querySelector("input[name='last_name']").value.trim().toLowerCase();

  const idParts = id.split(" ");
  if (idParts.length !== 3) {
    alert("Student ID format should be like '011 221 482'");
    e.preventDefault();
    return;
  }

  

  

  if (!email.startsWith(shortForm + admitYear) || !email.endsWith("@uiu.ac.bd")) {
    alert("Email must follow the format: [admitYear]@uiu.ac.bd (e.g. mah221@uiu.ac.bd)");
    e.preventDefault();
  }
});
</script>
<?php include('templates/footer.php'); ?>

</body>
</html>
