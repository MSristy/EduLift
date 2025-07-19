<?php
session_start();
if (!isset($_SESSION['lender_id'])) {
    header("Location: lenderlogin.php");
    exit();
}
include('db_connect.php');
$lender_id = $_SESSION['lender_id'];

// Fetch all lend requests for this lender
$sql = "SELECT * FROM lender_lend WHERE lender_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $lender_id);
$stmt->execute();
$result = $stmt->get_result();
$lends = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Lend Requests - EduLift</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            font-family: 'Roboto', Arial, sans-serif;
            background: linear-gradient(120deg, #f4f4f4 0%,rgb(244, 248, 242) 100%);
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }
        .container {
            max-width: 900px;
            margin: 40px auto 30px auto;
            padding: 0 15px;
            
        }
        .title {
            text-align: center;
            color: #15421a;
            margin-bottom: 30px;
            font-size: 2.1em;
            font-weight: 700;
            letter-spacing: 1px;
        }
        .lend-list {
            display: flex;
            flex-wrap: wrap;
            gap: 28px;
            justify-content: center;
        }
        .lend-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 18px 0 rgba(60, 120, 60, 0.10);
            padding: 28px 26px 22px 26px;
            min-width: 320px;
            max-width: 370px;
            flex: 1 1 320px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            position: relative;
            transition: box-shadow 0.2s, transform 0.2s;
            border: 2px solid green;
        }
        .lend-card:hover {
            box-shadow: 0 8px 32px 0 rgba(60, 120, 60, 0.18);
            transform: translateY(-4px) scale(1.02);
        }
        .lend-card .card-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 8px;
        }
        .lend-card .card-header .icon {
            font-size: 1.7em;
            color: #1a7f3c;
        }
        .lend-card .field-label {
            font-weight: 600;
            color: #1a3d2b;
            font-size: 1em;
        }
        .lend-card .field-value {
            color: #2b2b2b;
            font-size: 1.08em;
            margin-bottom: 2px;
        }
        .lend-card .description {
            background: #f8fefb;
            border-left: 4px solid #7bbf7b;
            padding: 10px 14px;
            border-radius: 7px;
            color: #1a3d2b;
            font-size: 1em;
            margin-top: 8px;
        }
        .lend-card .created {
            text-align: right;
            color: #888;
            font-size: 0.95em;
            margin-top: 8px;
        }
        @media (max-width: 700px) {
            .lend-list {
                flex-direction: column;
                gap: 18px;
            }
            .lend-card {
                max-width: 100%;
                min-width: 0;
            }
        }
    </style>
</head>
<body>
    <?php include('templates/lenderHeader.php'); ?>
    <div class="container">
        <div class="title"><i class="fa fa-paper-plane"></i> Your Lend Requests</div>
        <div class="lend-list">
            <?php if (count($lends) === 0): ?>
                <div style="text-align:center;width:100%;color:#888;font-size:1.2em;">No lend requests found.</div>
            <?php else: ?>
                <?php foreach ($lends as $lend): ?>
                    <div class="lend-card">
                        <div class="card-header">
                            <span class="icon"><i class="fa fa-hand-holding-dollar"></i></span>
                            <span style="font-size:1.15em;font-weight:700;">Lend #<?php echo $lend['id']; ?></span>
                        </div>
                        <div><span class="field-label">Full Name:</span> <span class="field-value"><?php echo htmlspecialchars($lend['full_name']); ?></span></div>
                        <div><span class="field-label">Student ID:</span> <span class="field-value"><?php echo htmlspecialchars($lend['student_id']); ?></span></div>
                        <div><span class="field-label">University Email:</span> <span class="field-value"><?php echo htmlspecialchars($lend['versity_email']); ?></span></div>
                        <div><span class="field-label">Phone Number:</span> <span class="field-value"><?php echo htmlspecialchars($lend['mobile']); ?></span></div>
                        <div><span class="field-label">Interest Rate:</span> <span class="field-value"><?php echo htmlspecialchars($lend['interest_rate']); ?>%</span></div>
                        <div><span class="field-label">Duration:</span> <span class="field-value"><?php echo ucfirst(htmlspecialchars($lend['duration'])); ?></span></div>
                        <div class="description"><span class="field-label">Description:</span><br><?php echo nl2br(htmlspecialchars($lend['description'])); ?></div>
                        <div class="created"><i class="fa fa-clock"></i> <?php echo date('M d, Y h:i A', strtotime($lend['created_at'])); ?></div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <?php include('templates/footer.php'); ?>
</body>
</html>
