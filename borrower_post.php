<?php  session_start(); include('db_connect.php');

// Handle Accept button click
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['accept_lender'])) {
    $lender_offer_id = $_POST['lender_id'];
    $borrower_id = $_POST['borrower_id'];

    $insertQuery = "INSERT INTO accepted_loans (lender_offer_id, borrower_id) VALUES ('$lender_offer_id', '$borrower_id')";
    $conn->query($insertQuery);

    $_SESSION['accepted_lender_' . $lender_offer_id . '_' . $borrower_id] = true;

    echo "<script>window.location.href='borrower_post.php';</script>";
    exit();
}

// Handle Decline button click
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['decline_lender'])) {
    $lender_offer_id = $_POST['lender_id'];
    $borrower_id = $_POST['borrower_id'];
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
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .post-wrapper {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 24px;
        }
        .post-card {
            background: #fff;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
            border-radius: 12px;
            width: 350px;
            padding: 20px;
            transition: transform 0.3s;
        }
        .post-card:hover {
            transform: translateY(-5px);
        }
        .post-header, .lender-header {
            font-weight: bold;
            margin-bottom: 10px;
            color: #007BFF;
        }
        .loan-description, .lender-description {
            margin-top: 10px;
            font-size: 14px;
            color: #555;
            
        }
        .lender-request-list {
            margin-top: 16px;
        }
        .lender-card {
            background-color: #e6f3ff;
            border-left: 4px solid #007BFF;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 8px;
        }
        .accept-button, .decline-button {
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
        }
        .accept-button {
            background-color: #28a745;
            color: white;
        }
        .decline-button {
            background-color: #dc3545;
            color: white;
        }
        .created {
            font-size: 12px;
            color: #666;
            margin-top: 6px;
        }
    </style>
</head>
<body>

<?php include('templates/header1.php'); ?>
<?php include('templates/borrowerHeader.php'); ?>


<h1 style="text-align:center;margin-top:40px;margin-bottom:48px;">Your Loan Posts</h1>
<div class="post-wrapper">
<?php 
$current_student_id = isset($_SESSION['student_id']) ? $_SESSION['student_id'] : '';
$query = "SELECT * FROM borrower_posts WHERE student_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $current_student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div class='post-card'>";
        echo "<div class='post-header'>" . htmlspecialchars($row['student_name']) . " | Student ID: " . htmlspecialchars($row['student_id']) . "</div>";
        if (!empty($row['loan_image'])) {
            echo "<img src='images/" . htmlspecialchars($row['loan_image']) . "' style='width:100%;max-height:180px;border-radius:8px;'>";
        }
        echo "<div><strong>Loan Amount:</strong> ৳" . htmlspecialchars($row['loan_amount']) . "</div>";
        echo "<div><strong>Purpose:</strong> " . htmlspecialchars($row['loan_purpose']) . "</div>";
        echo "<div class='loan-description'><span style='font-weight:bold;color:#000;'>Description:</span><br>" . nl2br(htmlspecialchars($row['loan_description'])) . "</div>";
        echo "<div><strong>Financial Status:</strong> " . htmlspecialchars($row['financial_wellbeing']) . "</div>";

        echo "<div class='lender-request-list'>";
        $lender_query = "SELECT * FROM lender_lend WHERE borrower_id = ? ORDER BY created_at DESC";
        $lender_stmt = $conn->prepare($lender_query);
        $lender_stmt->bind_param("i", $row['id']);
        $lender_stmt->execute();
        $lender_result = $lender_stmt->get_result();
        if ($lender_result && $lender_result->num_rows > 0) {
            while ($lender = $lender_result->fetch_assoc()) {
                echo "<div class='lender-card'>";
                echo "<div class='lender-header'>" . htmlspecialchars($lender['full_name']) . "</div>";
                echo "<div>Email: " . htmlspecialchars($lender['versity_email']) . "</div>";
                echo "<div>Phone: " . htmlspecialchars($lender['mobile']) . "</div>";
                echo "<div>Interest Rate: " . htmlspecialchars($lender['interest_rate']) . "%</div>";
                $durationValue = strtolower(trim($lender['duration']));
                if ($durationValue === 'no duration' || $durationValue === '' || $durationValue === null) {
                    $durationLabel = 'No Duration';
                } else if ($durationValue === 'end') {
                    $durationLabel = 'At the end';
                } else if ($durationValue === 'monthly') {
                    $durationLabel = 'Monthly';
                } else if ($durationValue === 'yearly') {
                    $durationLabel = 'Yearly';
                } else {
                    $durationLabel = htmlspecialchars($lender['duration']);
                }
                echo "<div>Duration: " . $durationLabel . "</div>";
                echo "<div class='lender-description'><strong>Description:</strong><br>" . nl2br(htmlspecialchars($lender['description'])) . "</div>";
                echo "<div class='created'>" . date('M d, Y h:i A', strtotime($lender['created_at'])) . "</div>";

                $accepted_key = 'accepted_lender_' . $lender['id'] . '_' . $row['id'];
                $is_accepted = isset($_SESSION[$accepted_key]) && $_SESSION[$accepted_key];

                echo "<div class='accept-decline-group'>";
                if ($is_accepted) {
                    echo "<button class='accept-button' style='background: #1a7f3c;' disabled>Accepted</button>";
                } else {
                    echo "<form method='POST' style='display:inline-block;'>";
                    echo "<input type='hidden' name='accept_lender' value='1'>";
                    echo "<input type='hidden' name='lender_id' value='" . htmlspecialchars($lender['id']) . "'>";
                    echo "<input type='hidden' name='borrower_id' value='" . htmlspecialchars($row['id']) . "'>";
                    echo "<button type='submit' class='accept-button'>Accept</button>";
                    echo "</form> ";
                    echo "<form method='POST' style='display:inline-block;'>";
                    echo "<input type='hidden' name='decline_lender' value='1'>";
                    echo "<input type='hidden' name='lender_id' value='" . htmlspecialchars($lender['id']) . "'>";
                    echo "<input type='hidden' name='borrower_id' value='" . htmlspecialchars($row['id']) . "'>";
                    echo "<button type='submit' class='decline-button'>Decline</button>";
                    echo "</form>";
                }
                echo "</div>";
                echo "</div>";
            }
        } else {
            echo "<div style='color:#888;'>No lender responses yet.</div>";
        }
        echo "</div>";
        echo "</div>";
    }
} else {
    echo "<p>No borrower posts available.</p>";
}
?>
</div>

<?php include('templates/footer.php'); ?>
</body>
</html>
