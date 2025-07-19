<?php
session_start();

$host = "localhost";
$username = "root";
$password = "";
$database = "loan";

$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = trim($_POST['user']); // input: student_id or university_email
    $input_password = $_POST['password'];

    $sql = "SELECT * FROM lenders_signup WHERE student_id = ? OR versity_email = ?";
    $stmt = $conn->prepare($sql);

    // Check if prepare failed
    if (!$stmt) {
        die("SQL error: " . $conn->error);  // Debug info for development
    }

    $stmt->bind_param("ss", $user, $user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $hashed_password = $row['password'];

        if (password_verify($input_password, $hashed_password)) {
            $_SESSION['lender_id'] = $row['id'];
            echo "<script>alert('Login Successful'); window.location.href='lenderHome.php';</script>";
            exit();
        } else {
            echo "<script>alert('Incorrect password');</script>";
        }
    } else {
        echo "<script>alert('User not found');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lender Login</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: url('https://accordfinancial.com/wp-content/uploads/2020/09/accord-services-lender-financing-hero.jpg') no-repeat center center fixed;
            background-size: cover;
        }

        .login-container {
            width: 90%;
            max-width: 950px;
            margin: 60px auto;
            display: flex;
            background: rgba(221, 243, 222, 0.8);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        .login-left {
            width: 30%;
            background: #0a2c47;
            padding: 40px 20px;
            display: flex;
            flex-direction: column;
            gap: 30px;
            justify-content: center;
            align-items: flex-start;
            color: white;
            opacity: 0.9;
        }

        .login-option {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 17px;
            font-weight: 500;
            transition: all 0.3s ease-in-out;
            cursor: pointer;
        }

        .login-option:hover {
            transform: translateX(5px);
            color: orange;
        }

        .circle {
            width: 28px;
            height: 28px;
            background: orange;
            color: white;
            border-radius: 50%;
            text-align: center;
            line-height: 28px;
            font-weight: bold;
            font-size: 14px;
        }

        .login-right {
            width: 70%;
            padding: 50px 60px;
        }

        .login-right h2 {
            margin-bottom: 5px;
            font-size: 38px;
            color: #222;
        }

        .subtext {
            color: rgb(167, 46, 9);
            margin-bottom: 25px;
            font-size: 14px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 5px;
            font-weight: 600;
            font-size: 15px;
        }

        input {
            padding: 10px 12px;
            margin-bottom: 18px;
            border-radius: 8px;
            border: 1.5px solid #ccc;
            font-size: 15px;
            transition: all 0.3s ease;
        }

        input:hover,
        input:focus {
            border-color: orange;
            background-color: #fff9f0;
            box-shadow: 0 0 5px rgba(255, 165, 0, 0.3);
            outline: none;
            transform: scale(1.01);
        }

        button {
            align-self: flex-end;
            background: orange;
            border: none;
            padding: 10px 25px;
            border-radius: 30px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            color: white;
            transition: background 0.3s, transform 0.2s;
        }

        button:hover {
            background: #ff6a00;
            transform: scale(1.05);
        }

        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                margin: 30px 10px;
            }

            .login-left, .login-right {
                width: 100%;
                padding: 30px;
            }

            .login-left {
                align-items: center;
            }
        }
    </style>
</head>
<body>
<?php include('templates/header2.php'); ?>

<div class="login-container">
    <div class="login-left">
        <div class="login-option" onclick="location.href='borrowerlogin.php'">
            <span class="circle">01</span> Borrower Login
        </div>
        <div class="login-option" onclick="location.href='lenderlogin.php'">
            <span class="circle">02</span> Lender Login
        </div>
    </div>

    <div class="login-right">
        <h2>Lender Login</h2>
        <p class="subtext">Login with your Student ID or University Email</p>
        <form method="POST">
            <label>Student ID or University Email</label>
            <input type="text" name="user" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <button type="submit">Log In</button>
        </form>
    </div>
</div>

<?php include('templates/footer.php'); ?>
</body>
</html>
