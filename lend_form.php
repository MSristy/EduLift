<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "loan";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $lender_name = $_POST['lender_name'] ?? '';
    $interest_rate = $_POST['interest_rate'] ?? '';
    $payable_system = $_POST['payable_system'] ?? '';
    $loan_duration = $_POST['loan_duration'] ?? '';
    $description = $_POST['description'] ?? '';

    if ($lender_name !== '' && $interest_rate !== '' && $payable_system && $loan_duration) {
        $stmt = $conn->prepare("INSERT INTO loan_offers (lender_name, interest_rate, payable_system, loan_duration, description) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sdsss", $lender_name, $interest_rate, $payable_system, $loan_duration, $description);

        if ($stmt->execute()) {
            echo "<script>alert('Loan offer submitted successfully!'); window.location.href='lenderhome.php';</script>";
        } else {
            echo "<script>alert('Error: " . $stmt->error . "');</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('Please fill all required fields.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lend Offer Form</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f4f8;
            
            margin: 0;
        }
        .form-container {
    background: #fff;
    padding: 35px 45px 0 45px;
    border-radius: 20px;
    box-shadow: 0 6px 25px rgba(0, 0, 0, 0.1);
    width: 470px;
    
    margin: 50px auto; /* Add this line */
}


        .form-container h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #111827;
        }
        .form-group {
            margin-bottom: 22px;
        }
        label {
            font-weight: 600;
            display: block;
            margin-bottom: 8px;
            color: #374151;
        }
        input[type="text"],
        input[type="number"],
        select,
        textarea {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #d1d5db;
            border-radius: 10px;
            background: #f9fafb;
            transition: border-color 0.3s;
        }
        input[type="text"]:focus,
        input[type="number"]:focus,
        select:focus,
        textarea:focus {
            border-color: #3b82f6;
            outline: none;
            background: #fff;
        }
        textarea {
            resize: vertical;
            min-height: 100px;
        }
        button {
            width: 100%;
            padding: 14px;
            background: #065f46;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 17px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover {
            background: #047857;
        }
    </style>
</head>
<body>

    <?php include('templates/header1.php'); ?> 
    <?php include('templates/lenderHeader.php'); ?>

<div class="form-container">
    <h2>Create Lend Offer</h2>
    <form action="lend_form.php" method="POST">
        <div class="form-group">
            <label for="lender_name">Your Name</label>
            <input type="text" id="lender_name" name="lender_name" required>
        </div>

        <div class="form-group">
            <label for="interest_rate">Interest Rate (%)</label>
            <input type="number" id="interest_rate" name="interest_rate" min="0" step="0.01" required>
        </div>

        <div class="form-group">
            <label for="loan_duration">Loan Duration (e.g., 6 months, 1 year)</label>
            <input type="text" id="loan_duration" name="loan_duration" required>
        </div>

        <div class="form-group">
            <label for="payable_system">Payable System</label>
            <select id="payable_system" name="payable_system" required>
                <option value="">Select Payable System</option>
                <option value="Monthly">Monthly</option>
                <option value="Quarterly">Quarterly</option>
                <option value="Bi-Annually">Bi-Annually</option>
                <option value="Yearly">Yearly</option>
            </select>
        </div>

        <div class="form-group">
            <label for="description">Additional Description</label>
            <textarea id="description" name="description" placeholder="Any extra conditions or information..."></textarea>
        </div>

        <button type="submit">Submit Offer</button>
    </form>
</div>
<?php include('templates/footer.php'); ?>
</body>
</html>
