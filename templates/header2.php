<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EdLift - Header</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
         body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            padding-top: 50px;
        }

        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000; 
            background: linear-gradient(to right, #e7cba0 0%, #b97a3a 100%); /* light-darkish official gradient */
            padding: 4px 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .navbar-content {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 1200px;
            margin: 0 auto;
            min-height: 48px;
        }
        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 22px;
            font-weight: bold;
            color: #7a3e13;
            text-shadow: 0 1px 2px #e7cba0;
        }
        .logo img {
            height: 46px;
        }
        .nav-links {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            justify-content: center;
            gap: 50px;
        }
        .nav-links li {
            display: inline;
        }
        .nav-links li :hover {
            color: #fff;
        .nav-links a:hover {
            color: #fff;
        }
        }
        .nav-links a {
            text-decoration: none;
            color: black;
            font-size: 13px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: color 0.2s;
            padding-top: 2px;
            padding-bottom: 2px;
            position: relative;
        }
        .nav-links a.active,
        .nav-links a:active {
            color: #fff !important;
            background: none;
        }
        /* Removed underline for active nav link */
        .nav-links a i {
            font-size: 18px;
        }
        .auth-buttons {
            display: flex;
            gap: 15px;
        }
        .auth-btn {
            background: #b96c2a;
            color: white !important;
            border: none;
            border-radius: 20px;
            padding: 4px 14px;
            font-size: 13px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: background 0.2s;
        }
        .auth-btn:hover {
            background: #a0521d;
        }
        .auth-buttons a.auth-btn:first-child {
            background: none !important;
            color: #fff !important;
            border: none;
            box-shadow: none;
            padding: 4px 0;
            border-radius: 0;
        }
        .auth-buttons a.auth-btn:first-child:hover {
            background: none !important;
            color: #ff9800 !important;
        }
        @media (max-width: 900px) {
            .navbar-content { flex-direction: column; gap: 10px; }
            .nav-links { gap: 20px; }
        }
    </style>

    <script>
    // Highlight active nav link with underline
    document.addEventListener('DOMContentLoaded', function() {
        var links = document.querySelectorAll('.nav-links a');
        var current = window.location.pathname.split('/').pop();
        links.forEach(function(link) {
            if(link.getAttribute('href') === current) {
                link.classList.add('active');
            }
        });
    });
    </script>
</head>
<body>

<header>
    <nav class="navbar">
      <div class="navbar-content">
        <div class="logo">
            <img src="images/edulogo.png" alt="EduLift Logo">
            EduLift
        </div>
        <ul class="nav-links">
            <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="friendsloan.php"><i class="fas fa-phone"></i> Friends Loan</a></li>
            <li><a href="about-us.php"><i class="fas fa-users"></i> About Us</a></li>
            <li><a href="faq.php"><i class="fas fa-question-circle"></i> Faq</a></li>
            <li><a href="index1.php"><i class="fas fa-hand-holding-usd"></i> CrowdFunding</a></li>
        </ul>
        <div class="auth-buttons">
            <a href="lenderlogin.php" class="auth-btn"><i class="fas fa-user"></i> Sign in</a>
            <a href="signUp.php" class="auth-btn"><i class="fas fa-user-plus"></i> Sign Up</a>
        </div>
      </div>
    </nav>
</header>

</body>
</html>
