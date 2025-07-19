<?php
if (!isset($_SESSION['lender_id'])) {
    header("Location: lenderlogin.php");
    exit();
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

// header.php - Navigation Bar for Edulift
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                font-family: Arial, sans-serif;
            }
            .navbar {
                display: flex;
                align-items: center;
                justify-content: space-between;
                background: linear-gradient(to right, #f0f8e2, #a0b694);
                padding: 10px 20px;
                border-radius: 5px;
            }
            .navbar :hover{
                color:rgb(38, 83, 13);
            }
            .navbar ul {
                list-style: none;
                display: flex;
            }
            .navbar ul li {
                margin: 0 15px;
                font-size: 16px;
                font-weight: bold;
            }
            .navbar ul li a {
                text-decoration: none;
                color: black;
            }
            .search-bar {
                display: flex;
                border: 1px solid black;
                border-radius: 20px;
                overflow: hidden;
            }
            .search-bar input {
                border: none;
                padding: 5px 10px;
                outline: none;
            }
            .search-bar button {
                background: green;
                border: none;
                padding: 5px 10px;
                cursor: pointer;
            }
            .profile-section {
                display: flex;
                align-items: center;
            }
            .logout-btn {
                background-color: green;
                color: white;
                border: none;
                padding: 5px 15px;
                border-radius: 10px;
                margin-left: 15px;
                cursor: pointer;
            }
        </style>
    </head>
    <body>
        <nav class="navbar">
            <ul>
                <li><a href="lenderHome.php"><i class="fa fa-home"></i> Home</a></li>
                <li><a href="index1.php"><i class="fa fa-hand-holding-usd"></i> Crowdfunding</a></li>
                <li><a href="faq.php"><i class="fa fa-question-circle"></i> FAQ</a></li>
                <li><a href="about-us.php"><i class="fa fa-info-circle"></i> About Us</a></li>
                <li><a href="lenderRequest.php"><i class="fa fa-paper-plane"></i> Your Request</a></li>

            </ul>
            <div class="search-bar">
                <form method="GET" action="lenderHome.php" style="display:flex;align-items:center;">
                    <input type="text" name="search_id" id="searchInput" placeholder="Student Id" style="border:none;padding:5px 10px;outline:none;">
                    <button type="submit" style="background:green;border:none;padding:5px 10px;cursor:pointer;"><i class="fa fa-search"></i> Search</button>
                </form>
            </div>
            <div class="profile-section">
                <a href="lenderProfile.php" style="text-decoration:none; color:black;"><i class="fa fa-user"></i> Profile</a>
                <a href="?logout=1" class="logout-btn" style="text-decoration:none;"><i class="fa fa-sign-out-alt"></i> Logout</a>
            </div>
        </nav>

</body>
</html>
