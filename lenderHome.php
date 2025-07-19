<?php 
session_start();
if (!isset($_SESSION['lender_id'])) {
    header("Location: lenderlogin.php");
    exit();
}
include('templates/header1.php');
include('templates/lenderHeader.php');
include('db_connect.php');

// PHP Search: filter by Student ID if provided
$search_id = isset($_GET['search_id']) ? trim($_GET['search_id']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up | Edulift</title>
    <style>
           * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                font-family: Arial, sans-serif;
            }
            body{
                background-color:rgb(253, 253, 253);
            }
            .section-container {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 20px;
            }
            .animated-text {
                width: 50%;
                font-size: 25px;
                font-weight: bold;
                text-align: center;
                background: linear-gradient(45deg, #ff0000, #ff7300, #ffeb00, #47ff00, #00ffee, #2b65ff, #8000ff);
                -webkit-background-clip: text;
                color: transparent;
                animation: animateColor 5s linear infinite;
            }
            @keyframes animateColor {
                0% { filter: hue-rotate(0deg); }
                100% { filter: hue-rotate(360deg); }
            }
            .image-container {
                width: 50%;
                display: flex;
                justify-content: center;
               
            }
            .image-container img {
                width: 80%;
                clip-path: polygon(25% 0%, 75% 0%, 100% 25%, 100% 75%, 75% 100%, 25% 100%, 0% 75%, 0% 25%);
                border-radius: 30px;
            }
            .button-container {
                display: flex;
                justify-content: center;
                margin-top: 20px;
                gap: 10px;
            }
            .range-button {
                background:rgb(14, 37, 14);
                color: white;
                border: none;
                padding: 10px 20px;
                border-radius: 10px;
                cursor: pointer;
                font-size: 16px;
                font-weight: bold;
            }
            .range-button:hover {
                background: #5a7b5a;
            }


            /* Borrower Post Container */
        .borrower-container {
           
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin-top: 30px;
            gap: 20px;
        }

        /* Borrower Card */
        .borrower-card {
            width: 350px;
            background-color: #FFF5E9;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: left;
            border: 2px solid #4CAF50;
            transition: transform 0.3s ease-in-out;
            position: relative;
            overflow: hidden;
        }

        .borrower-card:hover {
            transform: scale(1.05);
        }

        .borrower-card h2 {
            font-size: 18px;
            color:rgb(8, 36, 5);
            font-weight: bold;
            font-family:cursive;
            margin-bottom: 10px;
        }

        .borrower-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 10px;
        }

        .borrower-card p {
            font-size: 14px;
            color:rgb(1, 19, 0);;
            margin-bottom: 8px;
        }

        .loan-amount {
            font-size: 18px;
            font-weight: bold;
            color: #27ae60;
            margin-bottom: 10px;
        }

        .loan-purpose {
            font-size: 15px;
            font-weight: bold;
            color: #333;
        }

        .loan-description {
            font-size: 13px;
            color: #666;
            margin-bottom: 10px;
        }

        /* Button styles */
        .lend-button {
            display: block;
            width: 100%;
            padding: 10px;
            font-size: 14px;
            text-align: center;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
            text-decoration: none;
        }

        .help-button {
            background-color: #8B0000;
            color: white;
        }

        .lend-now {
            background-color:rgb(7, 34, 8);
            color: white;
        }

        .completed {
            background: linear-gradient(90deg, #ff8800 0%, #ff5500 100%);
            color: #fff;
            cursor: default;
            position: relative;
            font-size: 16px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: none;
            box-shadow: 0 2px 8px 0 rgba(255, 136, 0, 0.18);
        }

        .lend-button {
            display: block;
            width: 100%;
            padding: 10px;
            font-size: 14px;
            text-align: center;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
            text-decoration: none;
        }
        .help-button { background-color: #8B0000; color: white; }
        .lend-now { background-color: rgb(7, 34, 8); color: white; }
        .already-booked { background-color: red; color: white; }
    </style>
</head>

<body>
    <div class="section-container">
        <div class="animated-text">
            Every hand you lend writes a new story of hope—<br>
            <span style="font-size: 50px;">Be the author of someone's brighter tomorrow!</span>
        </div>
        <div class="image-container">
            <img src="images/campus.jpg" alt="University Campus">
        </div>
    </div>
    <div class="button-container">
        <button class="range-button">Lower Range<br><span style='font-size:13px;'>(৳0 - ৳20,000)</span></button>
        <button class="range-button">Middle Range<br><span style='font-size:13px;'>(৳20,001 - ৳70,000)</span></button>
        <button class="range-button">Higher Range<br><span style='font-size:13px;'>(Above ৳70,000)</span></button>
    </div>






    
    <!-- Borrower Posts -->
    <div class="borrower-container" id="borrowerContainer">
<?php
// Use search filter if provided
if ($search_id !== '') {
    $stmt = $conn->prepare("SELECT * FROM borrower_posts WHERE student_id LIKE ? ORDER BY created_at DESC");
    $like = "%$search_id%";
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $query = "SELECT * FROM borrower_posts ORDER BY created_at DESC";
    $result = $conn->query($query);
}

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Check if already accepted by this lender
        $borrowerId = $row['id'];
        $lenderId = $_SESSION['lender_id'];
        $acceptedQuery = "SELECT * FROM accepted_loans WHERE borrower_id = '$borrowerId' AND lender_offer_id IN (SELECT id FROM lender_lend WHERE lender_id = '$lenderId' AND borrower_id = '$borrowerId')";
        $acceptedResult = $conn->query($acceptedQuery);

        if ($acceptedResult->num_rows > 0) {
            $buttonClass = "completed";
            $buttonText = "Lend Completed";
        } else {
            $buttonClass = "lend-now";
            $buttonText = "Lend Now";
        }

        echo "\n        <div class='borrower-card'>";
        if (!empty($row['loan_image'])) {
            echo "<img src='images/" . htmlspecialchars($row['loan_image']) . "' alt='Loan Image'>";
        } else {
            echo "<img src='images/md2.png' alt='Student Image'>";
        }
        echo "\n            <h2>Student Loan Request for Education Support</h2>\n            <p><b>Name:</b> {$row['student_name']}</p>\n            <p><b>Student ID:</b> {$row['student_id']}</p>\n            <p class='loan-purpose'><b>Loan Purpose:</b> {$row['loan_purpose']}</p>\n            <p class='loan-amount'><b>Loan Amount Needed:</b> ৳{$row['loan_amount']}</p>\n            <p class='loan-description'><b>Brief Description:</b> {$row['loan_description']}</p>\n            <a href='lenderLend.php?borrower_id={$row['id']}' class='lend-button $buttonClass' ".($buttonClass=="completed"?"style='pointer-events:none;opacity:0.8;'":"").">".($buttonClass=="completed"?"<i class='fa fa-check-circle'></i> ":"")."$buttonText</a>\n        </div>\n        ";
    }
} else {
    echo "<p>No borrower posts available.</p>";
}

$conn->close();
?>
</div>




    <?php include('templates/footer.php'); ?>
<script>
// JavaScript Live Search
const searchInput = document.getElementById('searchInput');
const borrowerCards = document.querySelectorAll('.borrower-card');
if (searchInput && borrowerCards.length > 0) {
    searchInput.addEventListener('input', function() {
        const filter = searchInput.value.trim().toLowerCase();
        let anyVisible = false;
        borrowerCards.forEach(card => {
            const studentIdElem = card.querySelector('p:nth-of-type(2)'); // <p><b>Student ID:</b> ...</p>
            const studentId = studentIdElem ? studentIdElem.textContent.toLowerCase() : '';
            if (studentId.includes(filter)) {
                card.style.display = '';
                anyVisible = true;
            } else {
                card.style.display = 'none';
            }
        });
        // Optionally, show a message if none are visible
        const container = document.getElementById('borrowerContainer');
        let noResult = document.getElementById('noResultMsg');
        if (!anyVisible) {
            if (!noResult) {
                noResult = document.createElement('p');
                noResult.id = 'noResultMsg';
                noResult.textContent = 'No borrower posts available.';
                container.appendChild(noResult);
            }
        } else {
            if (noResult) noResult.remove();
        }
    });
}

// Range filter logic
const rangeButtons = document.querySelectorAll('.range-button');
rangeButtons.forEach((btn, idx) => {
    btn.addEventListener('click', function() {
        let min = 0, max = Infinity;
        if (idx === 0) { min = 0; max = 20000; } // Lower Range
        else if (idx === 1) { min = 20001; max = 70000; } // Middle Range
        else if (idx === 2) { min = 70001; max = Infinity; } // Higher Range
        let anyVisible = false;
        borrowerCards.forEach(card => {
            const amountElem = card.querySelector('.loan-amount');
            if (!amountElem) { card.style.display = 'none'; return; }
            // Extract number from text (e.g., '৳10000.00')
            const amountText = amountElem.textContent.replace(/[^\d.]/g, '');
            const amount = parseFloat(amountText);
            if (!isNaN(amount) && amount >= min && amount <= max) {
                card.style.display = '';
                anyVisible = true;
            } else {
                card.style.display = 'none';
            }
        });
        // Optionally, show a message if none are visible
        const container = document.getElementById('borrowerContainer');
        let noResult = document.getElementById('noResultMsg');
        if (!anyVisible) {
            if (!noResult) {
                noResult = document.createElement('p');
                noResult.id = 'noResultMsg';
                noResult.textContent = 'No borrower posts available.';
                container.appendChild(noResult);
            }
        } else {
            if (noResult) noResult.remove();
        }
    });
});
</script>
</body>
</html>
