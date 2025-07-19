<?php 
    session_start();
    include('db_connect.php');
    
    // Redirect to login if not logged in
    if (!isset($_SESSION['student_id'])) {
        header("Location: borrowerlogin.php");
        exit();
    }

    // Handle logout
    if (isset($_GET['logout'])) {
        session_destroy();
        header("Location: borrowerlogin.php");
        exit();
    }

    // Fetch borrower details
    $student_id_session = $_SESSION['student_id'];
    $sql_borrower = "SELECT first_name, last_name, student_id FROM borrower_signup WHERE student_id = ?";
    $stmt_borrower = $conn->prepare($sql_borrower);
    $stmt_borrower->bind_param("s", $student_id_session);
    $stmt_borrower->execute();
    $result_borrower = $stmt_borrower->get_result();
    $borrower = $result_borrower->fetch_assoc();
    $stmt_borrower->close();

    $borrower_full_name = htmlspecialchars($borrower['first_name'] . ' ' . $borrower['last_name']);
    $borrower_student_id = htmlspecialchars($borrower['student_id']);

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

        // Debugging: Print variables
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
    
        // Prepare and bind the statement
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
        background:rgb(252, 244, 240);
        margin: 0;
        padding: 0;

    } 
    .bform{
        margin:20px 0 20px 0;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;

    }

    .form-container {
        background: url('images/lender signup2.jpg') center/cover no-repeat;
        padding: 30px;
        width: 80%;
        border-radius: 10px;
        text-align: center;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
    }

    h2 {
        color:rgb(150, 54, 6);
        font-size: 24px;
        margin-bottom: 15px;
        font-family:cursive;
    }

    form {
        background: rgba(255, 255, 255, 0.3);
        padding: 20px;
        border-radius: 10px;
    }

    label {
        display: block;
        margin-top: 10px;
        font-weight: bold;
    }

    input, select, textarea {
        width: 70%;
        padding: 10px;
        margin-top: 5px;
        border-radius: 5px;
        border: 1px solid #ccc;
    }

    input[readonly] {
        background-color: #e9ecef;
        cursor: not-allowed;
    }

    .radio-group {
        display: flex;
        justify-content: space-around;
        margin: 10px 0;
    }

    .submit-button {
        background: orange;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        font-size: 18px;
        cursor: pointer;
        margin-top: 15px;
    }

    .submit-button:hover {
        background: darkorange;
    }


    </style>
</head>
<body>
    <?php include('templates/header1.php'); ?> 
    <?php include('templates/borrowerHeader.php'); ?>

    <div class="bform">

        <div class="form-container">
            <h2>Fill Up the form to borrow from your mates!!</h2>
            <form method="POST" enctype="multipart/form-data">
                <label>Student Name *</label>
                <input type="text" name="student_name" value="<?php echo $borrower_full_name; ?>" required readonly>

                <label>Student ID *</label>
                <input type="text" name="student_id" value="<?php echo $borrower_student_id; ?>" required readonly>

                <label>Loan amount *</label>
                <input type="number" name="loan_amount" required>

                <label>Loan Image</label>
                <input type="file" name="loan_image" accept="image/*">

                <label>Loan Purpose *</label>
                <select name="loan_purpose" required>
                    <option value="">Select your loan type</option>
                    <option value="Tuition Fees">Tuition Fees</option>
                    <option value="Books and Supplies">Books and Supplies</option>
                    <option value="Living Expenses">Living Expenses</option>
                    <option value="Others">Others</option>
                </select>

                <label>Tell about your Loan *</label>
                <textarea name="loan_description" required></textarea>

                <label>My financial wellbeing is</label>
                <div class="radio-group">
                    <input type="radio" name="financial_wellbeing" value="Excellent" required> Excellent
                    <input type="radio" name="financial_wellbeing" value="Very Good"> Very Good
                    <input type="radio" name="financial_wellbeing" value="Okay"> Okay
                    <input type="radio" name="financial_wellbeing" value="Not Good"> Not Good
                </div>

                <button type="submit" class="submit-button">SUBMIT</button>
            </form>
        </div>

    </div>



    <?php include('templates/footer.php'); ?>
</body>
</html>
