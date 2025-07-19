<?php
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
                background: linear-gradient(to right,rgb(243, 220, 171),rgb(248, 168, 77));
                padding: 8px 18px;
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
                font-size: 14px;
                font-weight: bold;
            }
            .navbar ul li a {
                text-decoration: none;
                color: black;
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
                <li><a href="borrowerHome.php"><i class="fa fa-home"></i> Home</a></li>
                <li><a href="index1.php"><i class="fa fa-hand-holding-usd"></i> Crowdfunding</a></li>
                <li><a href="faq.php"><i class="fa fa-question-circle"></i> FAQ</a></li>
                <li><a href="borrower_post.php"><i class=" "></i> Your Post</a></li>
            </ul>
            <div class="profile-section">
                <a href="borrowerProfile.php" style="text-decoration:none; color:black;"><i class="fa fa-user"></i> Profile</a>
                <a href="?logout=1" class="logout-btn" style="text-decoration:none;"><i class="fa fa-sign-out-alt"></i> Logout</a>
            </div>
        </nav>

</body>
</html>
