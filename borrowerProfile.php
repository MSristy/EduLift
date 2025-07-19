<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: borrowerlogin.php");
    exit();
}
include('db_connect.php');

$student_id = $_SESSION['student_id'];

// Handle form submission for profile update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $university_email = $_POST['university_email'];
    $dob = $_POST['dob'];
    $parent_number = $_POST['parent_number'];
    $city = $_POST['city'];
    $gender = $_POST['gender'];
    $nid = $_POST['nid'];
    $personal_email = $_POST['personal_email'];
    $mobile = $_POST['mobile'];
    
    // Handle profile image update if new image is uploaded
    $profile_img = null;
    if (!empty($_FILES['profile_img']['name'])) {
        $profile_img = $_FILES['profile_img']['name'];
        $tmp_name = $_FILES['profile_img']['tmp_name'];
        $upload_dir = "images/";
        move_uploaded_file($tmp_name, $upload_dir . $profile_img);
    }
    // Handle student ID card image update if new image is uploaded
    $student_id_card_img = null;
    if (!empty($_FILES['student_id_card_img']['name'])) {
        $student_id_card_img = $_FILES['student_id_card_img']['name'];
        $tmp_name_id = $_FILES['student_id_card_img']['tmp_name'];
        $upload_dir = "images/";
        move_uploaded_file($tmp_name_id, $upload_dir . $student_id_card_img);
    }

    // Build SQL dynamically based on which images are updated
    $sql = "UPDATE borrower_signup SET first_name=?, last_name=?, university_email=?, dob=?, parent_number=?, city=?, gender=?, nid=?, personal_email=?, mobile=?";
    $params = [$first_name, $last_name, $university_email, $dob, $parent_number, $city, $gender, $nid, $personal_email, $mobile];
    $types = "ssssssssss";
    if ($profile_img) {
        $sql .= ", profile_img=?";
        $params[] = $profile_img;
        $types .= "s";
    }
    if ($student_id_card_img) {
        $sql .= ", student_id_card_img=?";
        $params[] = $student_id_card_img;
        $types .= "s";
    }
    $sql .= " WHERE student_id=?";
    $params[] = $student_id;
    $types .= "s";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        $success_message = "Profile updated successfully!";
    } else {
        $error_message = "Error updating profile: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch current borrower information
$sql = "SELECT * FROM borrower_signup WHERE student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$borrower = $result->fetch_assoc();
$stmt->close();

// Set default values if fields are not set
$borrower['first_name'] = $borrower['first_name'] ?? '';
$borrower['last_name'] = $borrower['last_name'] ?? '';
$borrower['university_email'] = $borrower['university_email'] ?? '';
$borrower['dob'] = $borrower['dob'] ?? '';
$borrower['parent_number'] = $borrower['parent_number'] ?? '';
$borrower['city'] = $borrower['city'] ?? '';
$borrower['gender'] = $borrower['gender'] ?? '';
$borrower['nid'] = $borrower['nid'] ?? '';
$borrower['personal_email'] = $borrower['personal_email'] ?? '';
$borrower['mobile'] = $borrower['mobile'] ?? '';
$borrower['profile_img'] = $borrower['profile_img'] ?? 'default.jpg';
$borrower['student_id_card_img'] = $borrower['student_id_card_img'] ?? 'default_id.jpg';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrower Profile - EduLift</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        :root {
            --primary-orange: #F97316;
            --primary-orange-dark: #EA580C;
            --light-bg: #FFF7ED;
            --text-dark: #1F2937;
            --text-light: #4B5563;
            --border-color: #E5E7EB;
            --card-bg: #FFFFFF;
            --success-bg: #D1FAE5;
            --success-text: #065F46;
            --error-bg: #FEE2E2;
            --error-text: #991B1B;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
            background-color: var(--light-bg);
            color: var(--text-dark);
            margin: 0;
            padding: 20px;
        }

        .profile-container {
            max-width: 800px;
            margin: 40px auto;
            background: var(--card-bg);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
        }

        .profile-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 30px;
        }
        
        .profile-header h2 {
            color: var(--primary-orange);
            font-size: 2rem;
            font-weight: 700;
            margin-top: 10px;
            margin-bottom: 15px;
        }

        .profile-picture {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin: 0 auto 10px;
            display: block;
            object-fit: cover;
            border: 5px solid var(--primary-orange);
            box-shadow: 0 0 15px rgba(249, 115, 22, 0.4);
        }

        .id-card-picture {
            width: 200px;
            height: auto;
            border-radius: 10px;
            margin: 10px auto 0;
            display: block;
            object-fit: cover;
            border: 2px solid var(--border-color);
        }
        
        .form-group {
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-light);
            font-size: 0.9rem;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="date"],
        textarea,
        select,
        input[type="file"] {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 16px;
            box-sizing: border-box; /* Important for padding and width */
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        input[type="file"] {
            padding: 8px;
            border: 1px dashed var(--border-color);
            background-color: #FAFAFA;
        }
        
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--primary-orange);
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.2);
        }
        
        input[readonly] {
            background-color: #F3F4F6;
            cursor: not-allowed;
            color: var(--text-light);
        }

        .gender-group {
            display: flex;
            gap: 30px;
            align-items: center;
            height: 48px; /* match input height */
        }

        .gender-group label {
            font-weight: normal;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 0;
        }
        
        input[type="radio"] {
            width: auto;
            accent-color: var(--primary-orange);
        }
        
        /* Custom styles for file inputs */
        input[type="file"]::file-selector-button {
            background-color: var(--primary-orange);
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        input[type="file"]::file-selector-button:hover {
            background-color: var(--primary-orange-dark);
        }

        .btn {
            background-color: var(--primary-orange);
            color: white;
            padding: 15px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 18px;
            font-weight: 600;
            width: 100%;
            transition: background-color 0.2s, transform 0.1s;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn:hover {
            background-color: var(--primary-orange-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(234, 88, 12, 0.3);
        }
        
        .btn:active {
            transform: translateY(0);
            box-shadow: none;
        }

        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            text-align: center;
            font-weight: 500;
            border-left: 5px solid;
        }

        .success {
            background-color: var(--success-bg);
            color: var(--success-text);
            border-left-color: #10B981;
        }

        .error {
            background-color: var(--error-bg);
            color: var(--error-text);
            border-left-color: #EF4444;
        }

        /* Form Layout */
        .form-section {
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border-color);
        }
        .form-section:last-of-type {
            border-bottom: none;
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--primary-orange);
            display: inline-block;
        }
        .form-row {
            display: flex;
            gap: 30px;
        }
        .form-row .form-group {
            flex: 1;
        }

        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
                gap: 0;
            }
        }
    </style>
</head>
<body>
    <?php include('templates/borrowerHeader.php'); ?>
    <div class="profile-container">
        <div class="profile-header">
            <img src="images/<?php echo htmlspecialchars($borrower['profile_img']); ?>" alt="Profile Picture" class="profile-picture">
            <h2><?php echo htmlspecialchars($borrower['first_name'] . ' ' . $borrower['last_name']); ?>'s Profile</h2>
            <img src="images/<?php echo htmlspecialchars($borrower['student_id_card_img']); ?>" alt="Student ID Card" class="id-card-picture">
        </div>
        <?php if (isset($success_message)): ?>
            <div class="message success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if (isset($error_message)): ?>
            <div class="message error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">

            <div class="form-section">
                <h3 class="section-title">Personal Information</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($borrower['first_name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($borrower['last_name']); ?>" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="dob">Date of Birth</label>
                        <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($borrower['dob']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Gender</label>
                        <div class="gender-group">
                            <label><input type="radio" name="gender" value="Male" <?php if($borrower['gender']==='Male') echo 'checked'; ?>> Male</label>
                            <label><input type="radio" name="gender" value="Female" <?php if($borrower['gender']==='Female') echo 'checked'; ?>> Female</label>
                        </div>
                    </div>
                </div>
                 <div class="form-group">
                    <label for="nid">NID Number</label>
                    <input type="text" id="nid" name="nid" value="<?php echo htmlspecialchars($borrower['nid']); ?>" required>
                </div>
            </div>

            <div class="form-section">
                <h3 class="section-title">Academic & Contact Details</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="student_id">Student ID</label>
                        <input type="text" id="student_id" name="student_id" value="<?php echo htmlspecialchars($borrower['student_id']); ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="university_email">University Email</label>
                        <input type="email" id="university_email" name="university_email" value="<?php echo htmlspecialchars($borrower['university_email']); ?>" required>
                    </div>
                </div>
                <div class="form-row">
                     <div class="form-group">
                        <label for="mobile">Mobile Number</label>
                        <input type="tel" id="mobile" name="mobile" value="<?php echo htmlspecialchars($borrower['mobile']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="personal_email">Personal Email</label>
                        <input type="email" id="personal_email" name="personal_email" value="<?php echo htmlspecialchars($borrower['personal_email']); ?>" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="parent_number">Parent's Number</label>
                        <input type="text" id="parent_number" name="parent_number" value="<?php echo htmlspecialchars($borrower['parent_number']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="city">City</label>
                        <select id="city" name="city" required>
                            <option value="">Select City</option>
                            <option value="Dhaka" <?php if($borrower['city']==='Dhaka') echo 'selected'; ?>>Dhaka</option>
                            <option value="Chattogram" <?php if($borrower['city']==='Chattogram') echo 'selected'; ?>>Chattogram</option>
                            <option value="Rajshahi" <?php if($borrower['city']==='Rajshahi') echo 'selected'; ?>>Rajshahi</option>
                            <option value="Barisal" <?php if($borrower['city']==='Barisal') echo 'selected'; ?>>Barisal</option>
                            <option value="Sylhet" <?php if($borrower['city']==='Sylhet') echo 'selected'; ?>>Sylhet</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3 class="section-title">Document Uploads</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="profile_img">Update Profile Picture</label>
                        <input type="file" id="profile_img" name="profile_img" accept="image/*">
                    </div>
                    <div class="form-group">
                        <label for="student_id_card_img">Update Student ID Card Image</label>
                        <input type="file" id="student_id_card_img" name="student_id_card_img" accept="image/*">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn">Update Profile</button>
        </form>
    </div>
    <?php include('templates/footer.php'); ?>
</body>
</html>
