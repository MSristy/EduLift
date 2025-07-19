<?php
session_start();
if (!isset($_SESSION['lender_id'])) {
    header("Location: lenderlogin.php");
    exit();
}
include('db_connect.php');

$lender_id = $_SESSION['lender_id'];

// Handle form submission for profile update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $student_id = $_POST['student_id'];
    $versity_email = $_POST['versity_email'];
    $mobile = $_POST['mobile'];
    $about = $_POST['about'];
    
    // Handle profile picture update if new image is uploaded
    if (!empty($_FILES['profile_pic']['name'])) {
        $profile_img = $_FILES['profile_pic']['name'];
        $tmp_name = $_FILES['profile_pic']['tmp_name'];
        $upload_dir = "images/";
        move_uploaded_file($tmp_name, $upload_dir . $profile_img);
        
        $sql = "UPDATE lenders_signup SET 
                first_name = ?, 
                student_id = ?, 
                versity_email = ?, 
                mobile = ?, 
                about = ?, 
                profile_img = ? 
                WHERE id = ?";
                
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $first_name, $student_id, $versity_email, $mobile, $about, $profile_img, $lender_id);
    } else {
        $sql = "UPDATE lenders_signup SET 
                first_name = ?, 
                student_id = ?, 
                versity_email = ?, 
                mobile = ?, 
                about = ? 
                WHERE id = ?";
                
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $first_name, $student_id, $versity_email, $mobile, $about, $lender_id);
    }
    
    if ($stmt->execute()) {
        $success_message = "Profile updated successfully!";
    } else {
        $error_message = "Error updating profile: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch current lender information
$sql = "SELECT * FROM lenders_signup WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $lender_id);
$stmt->execute();
$result = $stmt->get_result();
$lender = $result->fetch_assoc();
$stmt->close();

// Set default values if fields are not set
$lender['first_name'] = $lender['first_name'] ?? '';
$lender['student_id'] = $lender['student_id'] ?? '';
$lender['versity_email'] = $lender['versity_email'] ?? '';
$lender['mobile'] = $lender['mobile'] ?? '';
$lender['about'] = $lender['about'] ?? '';
$lender['profile_img'] = $lender['profile_img'] ?? 'default.jpg';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lender Profile - EduLift</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .profile-container {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .profile-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .profile-picture {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin: 0 auto 20px;
            display: block;
            object-fit: cover;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        textarea {
            height: 100px;
            resize: vertical;
        }

        .btn {
            background-color: #15421a;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }

        .btn:hover {
            background-color: #0f2f14;
        }

        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .profile-info {
            margin-bottom: 30px;
        }

        .profile-info p {
            margin: 10px 0;
            font-size: 16px;
        }

        .profile-info strong {
            color: #15421a;
        }
    </style>
</head>
<body>
    <?php include('templates/lenderHeader.php'); ?>

    <div class="profile-container">
        <div class="profile-header">
            <img src="images/<?php echo htmlspecialchars($lender['profile_img']); ?>" alt="Profile Picture" class="profile-picture">
            <h2><?php echo htmlspecialchars($lender['first_name']); ?>'s Profile</h2>
        </div>

        <?php if (isset($success_message)): ?>
            <div class="message success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="message error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="first_name">Full Name</label>
                <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($lender['first_name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="student_id">Student ID</label>
                <input type="text" id="student_id" name="student_id" value="<?php echo htmlspecialchars($lender['student_id']); ?>" required>
            </div>

            <div class="form-group">
                <label for="versity_email">University Email</label>
                <input type="email" id="versity_email" name="versity_email" value="<?php echo htmlspecialchars($lender['versity_email']); ?>" required>
            </div>

            <div class="form-group">
                <label for="mobile">Phone Number</label>
                <input type="tel" id="mobile" name="mobile" value="<?php echo htmlspecialchars($lender['mobile']); ?>" required>
            </div>

            <div class="form-group">
                <label for="about">About Me</label>
                <textarea id="about" name="about"><?php echo htmlspecialchars($lender['about']); ?></textarea>
            </div>

            <div class="form-group">
                <label for="profile_pic">Update Profile Picture</label>
                <input type="file" id="profile_pic" name="profile_pic" accept="image/*">
            </div>

            <button type="submit" class="btn">Update Profile</button>
        </form>
    </div>

    <?php include('templates/footer.php'); ?>
</body>
</html>
