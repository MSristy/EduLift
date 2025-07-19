<?php 
    include('db_connect.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up | Edulift</title>
    <link rel="stylesheet" href="signup-style.css">
    <style>

        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background: url('images/UIU-Campus-1.jpg') no-repeat center center/cover;
            text-align: center;
            color: #000;
            min-height: 100vh;
        }

        /* Overlay for readability */
        .overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(255,255,255,0.75);
            z-index: 0;
        }

        /* Signup Container */
        .signup-container {
            position: relative;
            z-index: 1;
            padding: 60px 20px 40px 20px;
            max-width: 700px;
            margin: 60px auto 0 auto;
            background: rgba(255,255,255,0.65);
            border-radius: 18px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.18);
        }

        h1 {
            font-size: 2.3rem;
            margin-bottom: 18px;
            font-weight: bold;
            color: #25314d;
            letter-spacing: 1px;
        }

        .roles {
            display: flex;
            justify-content: center;
            gap: 50px;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        /* Role Box */
        .role {
            text-align: center;
            background: #f7f3f0;
            border-radius: 14px;
            box-shadow: 0 2px 12px 0 rgba(31, 38, 135, 0.08);
            padding: 32px 24px 28px 24px;
            min-width: 220px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .role:hover {
            transform: translateY(-8px) scale(1.045);
            box-shadow: 0 12px 32px 0 rgba(31, 38, 135, 0.18), 0 0 0 2px #f68b1f33;
        }

        /* Role Headings */
        .borrower {
            color: #FF540B;
            font-size: 1.3em;
            margin-bottom: 10px;
        }

        .lender {
            color: #15421a !important;
            font-size: 1.3em;
            margin-bottom: 10px;
        }

        /* Icons */
        .icon {
            width: 120px;
            height: 120px;
            background: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 10px auto 18px auto;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.10);
        }

        .icon img {
            width: 70px;
        }

        .borrower-icon{
            background:#FF540B;
        }
        .lender-icon{
            background:#15421a;
        }

        /* Buttons */
        button {
            border: none;
            padding: 12px 32px;
            font-size: 1.08rem;
            border-radius: 22px;
            cursor: pointer;
            margin-top: 10px;
            transition: 0.3s;
            font-weight: bold;
            letter-spacing: 1px;
            box-shadow: 0 2px 8px 0 rgba(31, 38, 135, 0.10);
        }

        .borrower-btn {
            background: linear-gradient(90deg, #FF540B 0%, #f68b1f 100%);
            color: white;
        }

        .borrower-btn:hover {
            background: linear-gradient(90deg, #f68b1f 0%, #FF540B 100%);
            color: #fff;
        }

        .lender-btn {
            background: linear-gradient(90deg, #15421a 0%, #1a7f3c 100%);
            color: white;
        }

        .lender-btn:hover {
            background: linear-gradient(90deg, #1a7f3c 0%, #15421a 100%);
            color: #fff;
        }

        /* Description */
        .description {
            margin-top: 38px;
            font-size: 1.15rem;
            background: rgba(255, 255, 255, 0.7);
            display: inline-block;
            padding: 14px 32px;
            border-radius: 12px;
            color: #25314d;
            font-weight: 500;
            box-shadow: 0 1px 4px 0 rgba(31, 38, 135, 0.04);
        }

        @media (max-width: 900px) {
            .roles {
                flex-direction: column;
                gap: 30px;
            }
            .signup-container {
                padding: 30px 5vw 20px 5vw;
            }
        }
        @media (max-width: 600px) {
            .signup-container {
                margin: 18px 0 0 0;
                padding: 18px 2vw 10px 2vw;
            }
            .role {
                min-width: unset;
                padding: 18px 5px 18px 5px;
            }
            h1 {
                font-size: 1.3em;
            }
        }

        .role.borrower {
            background: linear-gradient(135deg, rgba(255, 120, 10, 0.38) 60%, rgba(255, 180, 80, 0.32) 100%);
            backdrop-filter: blur(6px);
            border-radius: 22px;
            border: 1.5px solid rgba(255, 120, 10, 0.38);
            box-shadow: 0 4px 18px 0 rgba(255, 120, 10, 0.13);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .role.lender {
            background: linear-gradient(135deg, rgba(34, 139, 34, 0.32) 60%, rgba(21, 66, 26, 0.28) 100%);
            backdrop-filter: blur(6px);
            border-radius: 22px;
            border: 1.5px solid rgba(34, 139, 34, 0.32);
            box-shadow: 0 4px 18px 0 rgba(21, 66, 26, 0.13);
            transition: transform 0.2s, box-shadow 0.2s;
        }

    </style>
</head>
<body>
   <?php include('templates/header2.php'); ?>

    
    <div class="signup-container">
        <h1>Sign Up As</h1>
        <div class="roles">
            <div class="role">
                <h2 class="borrower">Borrower</h2>
                <div class="icon borrower-icon">
                    <img src="images/lend.png" alt="Borrower">
                </div>
                <button class="borrower-btn"><a href="borrowerSignup.php" style="text-decoration:none; color:white;">For Personal Loan</a></button>
            </div>
            <div class="role">
                <h2 class="lender" style="color:white;">Lender</h2>
                <div class="icon lender-icon">
                    <img src="images/borrow-removebg-preview.png" alt="Lender">
                </div>
                <button class="lender-btn"><a href="LenderSignup.php" style="text-decoration:none; color:white;">To Start Lending</button>
            </div>
        </div>
        <p class="description">Join now to lend or borrow with ease – secure, simple, and hassle-free!</p>
    </div>


    <?php include('templates/footer.php'); ?>
</body>
</body>
</html>