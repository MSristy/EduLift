<?php
session_start();
if (!isset($_SESSION['lender_id'])) {
    header("Location: lenderlogin.php");
    exit();
}
include('db_connect.php');

$lender_id = $_SESSION['lender_id'];

// Fetch current lender information
$sql = "SELECT first_name, student_id, versity_email, mobile FROM lenders_signup WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $lender_id);
$stmt->execute();
$result = $stmt->get_result();
$lender = $result->fetch_assoc();
$stmt->close();

$lender['first_name'] = $lender['first_name'] ?? '';
$lender['student_id'] = $lender['student_id'] ?? '';
$lender['versity_email'] = $lender['versity_email'] ?? '';
$lender['mobile'] = $lender['mobile'] ?? '';

// Fetch borrower info if borrower_id is provided
$borrower_info = null;
if (isset($_GET['borrower_id'])) {
    $borrower_id = intval($_GET['borrower_id']);
    $b_sql = "SELECT student_name, student_id, loan_amount, loan_purpose, loan_description FROM borrower_posts WHERE id = ?";
    $b_stmt = $conn->prepare($b_sql);
    if ($b_stmt) {
        $b_stmt->bind_param("i", $borrower_id);
        $b_stmt->execute();
        $b_result = $b_stmt->get_result();
        $borrower_info = $b_result->fetch_assoc();
        $b_stmt->close();
    }
}

// Handle form submission
$success_message = $error_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $student_id = trim($_POST['student_id']);
    $versity_email = trim($_POST['versity_email']);
    $mobile = trim($_POST['mobile']);
    $interest_rate = trim($_POST['interest_rate']);
    $duration = trim($_POST['duration']);
    $description = trim($_POST['description']);
    $borrower_id_to_save = isset($_GET['borrower_id']) ? intval($_GET['borrower_id']) : null;

    // Basic validation
    if ($full_name && $student_id && $versity_email && $mobile && $interest_rate && $duration && $description) {
        if ($borrower_id_to_save) {
            $sql = "INSERT INTO lender_lend (lender_id, borrower_id, full_name, student_id, versity_email, mobile, interest_rate, duration, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("iissssdss", $lender_id, $borrower_id_to_save, $full_name, $student_id, $versity_email, $mobile, $interest_rate, $duration, $description);
                if ($stmt->execute()) {
                    $success_message = "Lend information submitted successfully!";
                } else {
                    $error_message = "Database error: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $error_message = "Database prepare error: " . $conn->error;
            }
        } else {
            // fallback if no borrower_id
            $sql = "INSERT INTO lender_lend (lender_id, full_name, student_id, versity_email, mobile, interest_rate, duration, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("issssdss", $lender_id, $full_name, $student_id, $versity_email, $mobile, $interest_rate, $duration, $description);
                if ($stmt->execute()) {
                    $success_message = "Lend information submitted successfully!";
                } else {
                    $error_message = "Database error: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $error_message = "Database prepare error: " . $conn->error;
            }
        }
    } else {
        $error_message = "Please fill in all required fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lend Now - EduLift</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            font-family: 'Roboto', Arial, sans-serif;
            background: linear-gradient(120deg,rgb(249, 250, 249) 0%, #f4f4f4 100%);
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }
        .form-container {
            max-width: 520px;
            margin: 40px auto 30px auto;
            background: #fff;
            padding: 40px 35px 30px 35px;
            border-radius: 18px;
            box-shadow: 0 8px 32px 0 rgba(60, 120, 60, 0.15);
            position: relative;
        }
        .form-container h2 {
            text-align: center;
            color: #15421a;
            margin-bottom: 28px;
            font-weight: 700;
            letter-spacing: 1px;
        }
        .form-row {
            display: flex;
            gap: 18px;
        }
        .form-group {
            margin-bottom: 22px;
            position: relative;
            flex: 1;
        }
        label {
            display: block;
            margin-bottom: 7px;
            font-weight: 600;
            color: #1a3d2b;
            letter-spacing: 0.5px;
        }
        .input-icon {
            position: absolute;
            left: 12px;
            top: 38px;
            color: #7bbf7b;
            font-size: 1.1em;
        }
        input[type="text"], input[type="email"], input[type="tel"], select, textarea {
            width: 100%;
            padding: 10px 12px 10px 38px;
            border: 1.5px solid #cde7d8;
            border-radius: 7px;
            font-size: 16px;
            background: #f8fefb;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }
        textarea {
            min-height: 80px;
            resize: vertical;
        }
        input:focus, select:focus, textarea:focus {
            border-color: #7bbf7b;
            box-shadow: 0 0 0 2px #e0ffe7;
        }
        input[readonly], input[readonly]:focus {
            background: #f0f0f0;
            color: #888;
            border-color: #e0e0e0;
        }
        .btn {
            background: linear-gradient(90deg, #1a7f3c 0%, #4ad66d 100%);
            color: white;
            padding: 14px 0;
            border: none;
            border-radius: 7px;
            cursor: pointer;
            font-size: 18px;
            font-weight: 700;
            width: 100%;
            margin-top: 10px;
            box-shadow: 0 2px 8px 0 rgba(60, 120, 60, 0.10);
            transition: background 0.2s, transform 0.1s;
        }
        .btn:hover {
            background: linear-gradient(90deg, #15421a 0%, #1a7f3c 100%);
            transform: translateY(-2px) scale(1.02);
        }
        @media (max-width: 600px) {
            .form-container {
                padding: 18px 6vw;
            }
            .form-row {
                flex-direction: column;
                gap: 0;
            }
        }
    </style>
</head>
<body>
    <?php include('templates/lenderHeader.php'); ?>
    <div class="form-container">
        <h2><i class="fa-solid fa-hand-holding-dollar"></i> Lend Now</h2>
        <?php if ($borrower_info): ?>
            <div style="background:#f8fefb;border:2px solid #27ae60;padding:18px 22px;border-radius:12px;margin-bottom:22px;">
                <h3 style="margin-top:0;color:#15421a;">To:</h3>
                <p><b>Name:</b> <?php echo htmlspecialchars($borrower_info['student_name']); ?></p>
                <p><b>Student ID:</b> <?php echo htmlspecialchars($borrower_info['student_id']); ?></p>
                <p><b>Loan Amount Needed:</b> ৳<?php echo htmlspecialchars($borrower_info['loan_amount']); ?></p>
                <p><b>Loan Purpose:</b> <?php echo htmlspecialchars($borrower_info['loan_purpose']); ?></p>
                <p><b>Description:</b> <?php echo nl2br(htmlspecialchars($borrower_info['loan_description'])); ?></p>
            </div>
        <?php endif; ?>
        <h3 style="margin-bottom:10px;color:#1a3d2b;">From:</h3>
        <?php if ($success_message): ?>
            <div style="background:#e6f9ed;color:#1a7f3c;padding:12px 18px;border-radius:7px;margin-bottom:18px;text-align:center;font-weight:600;">
                <?php echo $success_message; ?>
            </div>
        <?php elseif ($error_message): ?>
            <div style="background:#ffeaea;color:#b30000;padding:12px 18px;border-radius:7px;margin-bottom:18px;text-align:center;font-weight:600;">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        <?php if ($success_message): ?>
            <script>
                alert('<?php echo addslashes($success_message); ?>');
            </script>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-row">
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <span class="input-icon"><i class="fa fa-user"></i></span>
                    <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($lender['first_name']); ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="student_id">Student ID</label>
                    <span class="input-icon"><i class="fa fa-id-card"></i></span>
                    <input type="text" id="student_id" name="student_id" value="<?php echo htmlspecialchars($lender['student_id']); ?>" readonly>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="versity_email">University Email</label>
                    <span class="input-icon"><i class="fa fa-envelope"></i></span>
                    <input type="email" id="versity_email" name="versity_email" value="<?php echo htmlspecialchars($lender['versity_email']); ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="mobile">Phone Number</label>
                    <span class="input-icon"><i class="fa fa-phone"></i></span>
                    <input type="tel" id="mobile" name="mobile" value="<?php echo htmlspecialchars($lender['mobile']); ?>" readonly>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="interest_rate">Interest Rate (%)</label>
                    <span class="input-icon"><i class="fa fa-percent"></i></span>
                    <input type="text" id="interest_rate" name="interest_rate" required>
                </div>
                <div class="form-group">
                    <label for="duration">Duration</label>
                    <span class="input-icon"><i class="fa fa-clock"></i></span>
                    <select id="duration" name="duration" required style="padding-left:38px;">
                        <option value="">Select Duration</option>
                        <option value="monthly">Monthly</option>
                        <option value="yearly">Yearly</option>
                        <option value="end">At the end</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <span class="input-icon" style="top:34px;"><i class="fa fa-align-left"></i></span>
                <textarea id="description" name="description" rows="4" required style="padding-left:38px;"></textarea>
            </div>
            <button type="submit" class="btn"><i class="fa fa-paper-plane"></i> Submit</button>
        </form>
    </div>
    <?php include('templates/footer.php'); ?>
</body>
</html>