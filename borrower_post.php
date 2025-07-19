<?php 
session_start();
include('db_connect.php');

// Handle Accept button click
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['accept_lender'])) {
    $lender_offer_id = $_POST['lender_id'];
    $borrower_id = $_POST['borrower_id'];

    // Insert into accepted_loans
    $insertQuery = "INSERT INTO accepted_loans (lender_offer_id, borrower_id) VALUES ('$lender_offer_id', '$borrower_id')";
    $conn->query($insertQuery);

    // Set accepted lender in session to show accepted state
    $_SESSION['accepted_lender_' . $lender_offer_id . '_' . $borrower_id] = true;

    // Optionally show success message
    echo "<script>window.location.href='borrower_post.php';</script>";
    exit();
}

// Handle Decline button click
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['decline_lender'])) {
    $lender_offer_id = $_POST['lender_id'];
    $borrower_id = $_POST['borrower_id'];
    // Remove the lender's offer for this borrower
    $deleteQuery = "DELETE FROM lender_lend WHERE id = '$lender_offer_id' AND borrower_id = '$borrower_id'";
    $conn->query($deleteQuery);
    echo "<script>window.location.href='borrower_post.php';</script>";
    exit();
}

$loan_image_path = null;
if (isset($_FILES['loan_image']) && $_FILES['loan_image']['error'] == 0) {
    $target_dir = "images/loan_images/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    $loan_image_path = $target_dir . basename($_FILES["loan_image"]["name"]);
    move_uploaded_file($_FILES["loan_image"]["tmp_name"], $loan_image_path);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_name = $_POST['student_name'];
    $student_id = $_POST['student_id'];
    $loan_amount = $_POST['loan_amount'];
    $loan_purpose = $_POST['loan_purpose'];
    $loan_description = $_POST['loan_description'];
    $financial_wellbeing = $_POST['financial_wellbeing'];

    // Handle loan image upload
    $loan_image = null;
    if (!empty($_FILES['loan_image']['name'])) {
        $loan_image = uniqid() . '_' . basename($_FILES['loan_image']['name']);
        $target_dir = "images/";
        move_uploaded_file($_FILES['loan_image']['tmp_name'], $target_dir . $loan_image);
    }

    $stmt = $conn->prepare("INSERT INTO borrower_posts (student_name, student_id, loan_amount, loan_purpose, loan_description, financial_wellbeing, loan_image) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ssdssss", $student_name, $student_id, $loan_amount, $loan_purpose, $loan_description, $financial_wellbeing, $loan_image);

    if ($stmt->execute()) {
        echo "<script>alert('Loan request submitted successfully!'); window.location.href='borrower_post.php';</script>";
    } else {
        echo "Execution failed: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}

// Handle logout redirect
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: borrowerlogin.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrower Home</title>
    <style>
        body { font-family: Arial, sans-serif; background-color:rgb(247, 247, 247); margin: 0; padding: 0; text-align: center; }
        h1, h2 { margin-top: 20px; color: #b85c00; }
        h1 { color: #b85c00; }
        .post-container, .lender-container { width: 90%; margin: 20px auto; display: flex; flex-wrap: wrap; gap: 28px; justify-content: center; }
        .post-card, .lender-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 18px 0 rgba(255, 140, 0, 0.10);
            padding: 0 0 18px 0;
            width: 340px;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
            transition: box-shadow 0.2s, transform 0.2s;
            border: 2px solid #ffd699;
        }
        .post-card:hover, .lender-card:hover {
            box-shadow: 0 8px 32px 0 rgba(255, 140, 0, 0.18);
            transform: translateY(-4px) scale(1.02);
        }
        .post-header {
            background: linear-gradient(90deg, #ffe5b4 0%, #ffd699 100%);
            padding: 18px 0 10px 0;
            border-radius: 16px 16px 0 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .post-header .icon {
            font-size: 2.2em;
            color: #ff9800;
            margin-bottom: 6px;
        }
        .post-header .student-name {
            font-size: 1.25em;
            font-weight: 700;
            color: #000;
        }
        .post-header .student-id {
            font-size: 1em;
            color: #000;
            margin-bottom: 2px;
        }
        .post-info-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 10px 22px 0 22px;
            font-size: 1.05em;
            color: #000;
        }
        .post-info-label {
            font-weight: 600;
            color: #000;
            min-width: 90px;
        }
        .loan-amount {
            color: #000;
            font-weight: 700;
            font-size: 1.15em;
        }
        .loan-purpose {
            color: #000;
            font-weight: 600;
        }
        .loan-description {
            margin: 12px 22px 0 22px;
            color: #000;
            font-size: 1em;
            background: #fff3e0;
            border-left: 4px solid #ff9800;
            padding: 10px 14px;
            border-radius: 7px;
        }
        .financial-status {
            margin: 14px 22px 0 22px;
            padding: 7px 0;
            border-radius: 6px;
            font-weight: 600;
            font-size: 1em;
            color: #000;
        }
        .financial-status.good { background: #fffbe6; }
        .financial-status.average { background: #ffe5b4; }
        .financial-status.poor { background: #ffeaea; color: #b30000; }
        .post-footer {
            margin: 18px 22px 0 22px;
            text-align: right;
            color: #000;
            font-size: 0.95em;
        }
        /* Lender Card */
        .lender-card {
            background: linear-gradient(120deg, #eaffea 0%, #fffbe6 100%);
            border: 2px solid #b6e2b6;
            box-shadow: 0 6px 24px 0 rgba(60, 120, 60, 0.10);
            padding: 0 0 18px 0;
            width: 340px;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
            transition: box-shadow 0.2s, transform 0.2s;
            border-radius: 18px;
            margin-bottom: 18px;
        }
        .lender-header {
            background: linear-gradient(90deg, #d4f7d4 0%, #fffbe6 100%);
            padding: 18px 0 10px 0;
            border-radius: 18px 18px 0 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            box-shadow: 0 2px 8px 0 rgba(60, 120, 60, 0.06);
        }
        .lender-header .icon {
            font-size: 2em;
            color: #1a7f3c;
            margin-bottom: 4px;
        }
        .lender-header .lender-name {
            font-size: 1.15em;
            font-weight: 700;
            color: #15421a;
        }
        .lender-info-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 8px 22px 0 22px;
            font-size: 1em;
            color: #1a3d2b;
        }
        .lender-info-label {
            font-weight: 600;
            color: #1a7f3c;
            min-width: 90px;
        }
        .lender-description {
            margin: 10px 22px 0 22px;
            color: #15421a;
            font-size: 1em;
            background: #f8fefb;
            border-left: 4px solid #7bbf7b;
            padding: 10px 14px;
            border-radius: 7px;
        }
        .accept-decline-group {
            display: flex;
            gap: 14px;
            justify-content: center;
            margin-top: 18px;
        }
        .accept-button, .decline-button {
            padding: 10px 28px;
            border: none;
            border-radius: 25px;
            font-size: 1.08em;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s, box-shadow 0.2s, transform 0.1s;
            box-shadow: 0 2px 8px 0 rgba(60, 120, 60, 0.10);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .accept-button {
            background: linear-gradient(90deg, #1a7f3c 0%, #4ad66d 100%);
            color: #fff;
        }
        .accept-button:hover {
            background: linear-gradient(90deg, #15421a 0%, #1a7f3c 100%);
            transform: translateY(-2px) scale(1.04);
            box-shadow: 0 4px 16px 0 rgba(60, 120, 60, 0.18);
        }
        .decline-button {
            background: linear-gradient(90deg, #ff5858 0%, #ff9800 100%);
            color: #fff;
        }
        .decline-button:hover {
            background: linear-gradient(90deg, #b30000 0%, #ff5858 100%);
            transform: translateY(-2px) scale(1.04);
            box-shadow: 0 4px 16px 0 rgba(255, 88, 88, 0.18);
        }
    </style>
</head>
<body>

<?php include('templates/header1.php'); ?> 
<?php include('templates/borrowerHeader.php'); ?>

<h1>Your Loan Posts</h1>

<div class="post-container">
<?php
// Only show the most recent post for the current logged-in borrower
$current_student_id = isset($_SESSION['student_id']) ? $_SESSION['student_id'] : '';
$current_name = isset($_SESSION['first_name']) ? $_SESSION['first_name'] : '';
// Fetch all posts for this borrower
$query = "SELECT * FROM borrower_posts WHERE student_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $current_student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "\n    <div class='post-card'>\n        <div class='post-header'>\n            <span class='icon'><i class='fa fa-user-graduate'></i></span>\n            <span class='student-name'>" . htmlspecialchars($row['student_name']) . "</span>\n            <span class='student-id'>Student ID: " . htmlspecialchars($row['student_id']) . "</span>\n        </div>";
        if (!empty($row['loan_image'])) {
            echo "<div style='margin:10px 0;'><img src='images/" . htmlspecialchars($row['loan_image']) . "' alt='Loan Image' style='max-width:90%;max-height:180px;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.08);'></div>";
        }
        echo "\n        <div class='post-info-row'><span class='post-info-label'>Loan:</span> <span class='loan-amount'>৳" . htmlspecialchars($row['loan_amount']) . "</span></div>\n        <div class='post-info-row'><span class='post-info-label'>Purpose:</span> <span class='loan-purpose'>" . htmlspecialchars($row['loan_purpose']) . "</span></div>\n        <div class='loan-description'><b>Description:</b><br>" . nl2br(htmlspecialchars($row['loan_description'])) . "</div>\n        <div class='financial-status " . strtolower(htmlspecialchars($row['financial_wellbeing'])) . "'><span class='post-info-label'>My financial status :</span> " . htmlspecialchars($row['financial_wellbeing']) . "</div>\n    </div>\n    ";
        // Save the latest post id for lender requests below
        if (!isset($latest_post_id)) {
            $latest_post_id = $row['id'];
            $latest_post_row = $row;
        }
    }
} else {
    echo "<p>No borrower posts available.</p>";
    $latest_post_id = null;
}
?>
</div>

<h2>Requested Lenders</h2>

<div class="lender-container">
<?php
// Only show lenders for the latest post of the current borrower
if (!empty($latest_post_id)) {
    echo "<div style='margin-bottom:30px;'>";
    echo "<h3 style='color:#b85c00;'>For Borrower: " . htmlspecialchars($latest_post_row['student_name']) . " (ID: " . htmlspecialchars($latest_post_row['student_id']) . ")</h3>";
    // Fetch lenders for this borrower post
    $lender_query = "SELECT * FROM lender_lend WHERE borrower_id = ? ORDER BY created_at DESC";
    $lender_stmt = $conn->prepare($lender_query);
    $lender_stmt->bind_param("i", $latest_post_id);
    $lender_stmt->execute();
    $lender_result = $lender_stmt->get_result();
    if ($lender_result && $lender_result->num_rows > 0) {
        while ($lender = $lender_result->fetch_assoc()) {
            echo "<div class='lender-card'>";
            echo "<div class='lender-header'><span class='icon'><i class='fa fa-hand-holding-dollar'></i></span><span class='lender-name'>" . htmlspecialchars($lender['full_name']) . "</span></div>";
            echo "<div class='lender-info-row'><span class='lender-info-label'>Student ID:</span> <span>" . htmlspecialchars($lender['student_id']) . "</span></div>";
            echo "<div class='lender-info-row'><span class='lender-info-label'>Email:</span> <span>" . htmlspecialchars($lender['versity_email']) . "</span></div>";
            echo "<div class='lender-info-row'><span class='lender-info-label'>Phone:</span> <span>" . htmlspecialchars($lender['mobile']) . "</span></div>";
            echo "<div class='lender-info-row'><span class='lender-info-label'>Interest Rate:</span> <span>" . htmlspecialchars($lender['interest_rate']) . "%</span></div>";
            echo "<div class='lender-info-row'><span class='lender-info-label'>Duration:</span> <span>" . htmlspecialchars($lender['duration']) . "</span></div>";
            echo "<div class='lender-description'><b>Description:</b><br>" . nl2br(htmlspecialchars($lender['description'])) . "</div>";
            echo "<div class='created'><i class='fa fa-clock'></i> " . date('M d, Y h:i A', strtotime($lender['created_at'])) . "</div>";
            // Add Accept and Decline buttons
            $accepted_key = 'accepted_lender_' . $lender['id'] . '_' . $latest_post_id;
            $is_accepted = isset($_SESSION[$accepted_key]) && $_SESSION[$accepted_key];
            echo "<div class='accept-decline-group'>";
            if ($is_accepted) {
                echo "<button class='accept-button' style='background: #1a7f3c;cursor:default;' disabled><i class='fa fa-check-circle'></i> Accepted</button>";
            } else {
                echo "<form method='POST' style='display:inline-block;'>";
                echo "<input type='hidden' name='accept_lender' value='1'>";
                echo "<input type='hidden' name='lender_id' value='" . htmlspecialchars($lender['id']) . "'>";
                echo "<input type='hidden' name='borrower_id' value='" . htmlspecialchars($latest_post_id) . "'>";
                echo "<button type='submit' class='accept-button'><i class='fa fa-check-circle'></i> Accept</button>";
                echo "</form> ";
                echo "<form method='POST' style='display:inline-block;'>";
                echo "<input type='hidden' name='decline_lender' value='1'>";
                echo "<input type='hidden' name='lender_id' value='" . htmlspecialchars($lender['id']) . "'>";
                echo "<input type='hidden' name='borrower_id' value='" . htmlspecialchars($latest_post_id) . "'>";
                echo "<button type='submit' class='decline-button'><i class='fa fa-times-circle'></i> Decline</button>";
                echo "</form>";
            }
            echo "</div>";
            echo "</div>";
        }
    } else {
        echo "<div style='color:#888;'>No lender requests for this borrower yet.</div>";
    }
    echo "</div>";
}
?>
</div>

<?php include('templates/footer.php'); ?>
</body>
</html>
