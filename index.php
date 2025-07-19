<?php 
    include('db_connect.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduLift</title>
    <link rel="stylesheet" href="indexStyles.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">

</head>
<body>

   <?php include('templates/header1.php'); ?>
   <?php include('templates/header2.php'); ?>

 <!-- Hero Section -->
<section class="hero">
    <video autoplay muted loop id="bg-video">
        <source src="images/vdo.mp4" type="video/mp4">
    </video>
    <div class="hero-content">
        <h1 class="animate-text" style="color:orange;">Get Student Loans <br> Without the Hassle!</h1>
        <h2 style="color:rgb(170, 66, 6);">Borrow & lend money instantly, securely, and with zero stress.</h2>
        <a href="signup.php" class="btn pulse" style="text-decoration: none;">Join Now</a>
    </div>
</section>

<!-- Why EdLift? -->
<section class="why-edlift">
    <h2>Why Choose EduLift?</h2>
    <div class="features">
        <div class="feature-card">📌 No Hidden Fees</div>
        <div class="feature-card">⚡ Instant Approvals</div>
        <div class="feature-card">🔒 Secure Transactions</div>
        <div class="feature-card">💳 Flexible Payments</div>
    </div>
</section>


    <div class="text">
        <p><h2>Looking to lend a hand or need one? Join our lending & borrowing system!</h2></p>

    </div>


    
    <!-- Category Buttons -->
    <div class="categories">
        <button class="btn category-btn active" data-category="all">All</button>
        <button class="btn category-btn" data-category="living">Living Expense</button>
        <button class="btn category-btn" data-category="books">Books and Supplies</button>
        <button class="btn category-btn" data-category="tuition">Tuition Fees</button>
        <button class="btn category-btn" data-category="others">Others</button>
    </div>


    
    <!-- Loan Cards -->
    <section class="ln-section">
        <?php
        // Fetch all borrower posts
        $query = "SELECT * FROM borrower_posts ORDER BY created_at DESC";
        $result = $conn->query($query);
        
        function getCategoryFromPurpose($purpose) {
            $purpose = strtolower($purpose);
            if (strpos($purpose, 'tuition') !== false || strpos($purpose, 'semester') !== false) {
                return 'tuition';
            } elseif (strpos($purpose, 'living') !== false || strpos($purpose, 'rent') !== false || strpos($purpose, 'hostel') !== false || strpos($purpose, 'food') !== false) {
                return 'living';
            } elseif (strpos($purpose, 'book') !== false || strpos($purpose, 'suppl') !== false || strpos($purpose, 'material') !== false) {
                return 'books';
            } else {
                return 'others';
            }
        }
        
        $catColors = [
            'tuition' => '#ff9800',
            'living' => '#e75480',
            'books' => '#4caf50',
            'others' => '#888'
        ];
        $catIcons = [
            'tuition' => '📖',
            'living' => '🍽',
            'books' => '🏠',
            'others' => '💳'
        ];
        if (
            $result && $result->num_rows > 0
        ) {
            while ($row = $result->fetch_assoc()) {
                $category = getCategoryFromPurpose($row['loan_purpose']);
                $badgeColor = $catColors[$category];
                $badgeIcon = $catIcons[$category];
                // Check if this loan is accepted (lend completed)
                $loanAccepted = false;
                $loan_id = $row['id'];
                $acceptedQuery = "SELECT * FROM accepted_loans WHERE borrower_id = '$loan_id' LIMIT 1";
                $acceptedResult = $conn->query($acceptedQuery);
                if ($acceptedResult && $acceptedResult->num_rows > 0) {
                    $loanAccepted = true;
                }
                ?>
                <div class="ln-card" data-category="<?php echo $category; ?>" style="position:relative; box-shadow:0 6px 24px 0 rgba(255, 140, 0, 0.10); border:2px solid <?php echo $badgeColor; ?>;">
                    <!-- Category Badge -->
                    <span style="position:absolute;top:12px;left:12px;background:<?php echo $badgeColor; ?>;color:white;padding:5px 15px;border-radius:20px;font-size:0.95em;font-weight:bold;box-shadow:0 2px 8px rgba(0,0,0,0.08);z-index:2;letter-spacing:1px;">
                        <?php echo $badgeIcon . ' ' . ucfirst($category); ?>
                    </span>
                    <div class="card-header" style="margin-top:30px;">
                        <img src="images/icon.png" alt="Loan Icon">
                        <h3 style="font-size:1.3rem; color:#15421a; letter-spacing:1px; margin-left:8px;"> <?php echo htmlspecialchars($row['student_name']); ?> </h3>
                    </div>
                    <p style="margin:8px 0 0 0; color:#555;"><strong>ID:</strong> <?php echo htmlspecialchars($row['student_id']); ?></p>
                    <div class="progress-bar" style="margin:10px 0 10px 0;"><div class="filled" style="width:0%"></div></div>
                    <?php if (!empty($row['loan_image'])): ?>
                        <img class="student-img" src="images/<?php echo htmlspecialchars($row['loan_image']); ?>" alt="Student" style="max-height:120px;object-fit:cover;box-shadow:0 2px 8px rgba(0,0,0,0.08);margin-bottom:10px;">
                    <?php else: ?>
                        <img class="student-img" src="images/md2.png" alt="Student" style="max-height:120px;object-fit:cover;box-shadow:0 2px 8px rgba(0,0,0,0.08);margin-bottom:10px;">
                    <?php endif; ?>
                    <p style="font-size:1.1em;margin:8px 0 0 0;"><b>৳<?php echo htmlspecialchars($row['loan_amount']); ?></b> <span style="color:#888;font-size:0.95em;">for</span> <span style="color:#c8642a;font-weight:bold;"> <?php echo htmlspecialchars($row['loan_purpose']); ?> </span></p>
                    <div style="background:#fff3e0;border-left:4px solid <?php echo $badgeColor; ?>;padding:10px 14px;border-radius:7px;margin:12px 0 0 0;min-height:48px;text-align:left;">
                        <span style="font-weight:600;color:#b85c00;">Description:</span><br>
                        <span style="color:#333;"> <?php echo nl2br(htmlspecialchars($row['loan_description'])); ?> </span>
                    </div>
                    <div style="margin:12px 0 0 0;">
                        <span style="font-weight:600;color:#15421a;">Financial Status:</span>
                        <span style="background:#e1f5c8;padding:3px 10px;border-radius:12px;font-weight:bold;color:#0a270c;margin-left:5px;">
                            <?php echo htmlspecialchars($row['financial_wellbeing']); ?>
                        </span>
                    </div>
                    <div style="margin:14px 0 0 0;text-align:right;color:#888;font-size:0.95em;">
                        <span style="font-size:1em;">🕒</span> <?php echo date('M d, Y', strtotime($row['created_at'])); ?>
                    </div>
                    <?php if ($loanAccepted): ?>
                        <button class="btn lend" style="margin-top:18px;width:90%;font-size:1.1em;background:#e75480;color:white;cursor:default;box-shadow:0 2px 8px rgba(231,84,128,0.18);border:2px solid #b3005c;" disabled>LEND COMPLETED</button>
                    <?php else: ?>
                        <a href="LenderSignup.php"><button class="btn lend" style="margin-top:18px;width:90%;font-size:1.1em;box-shadow:0 2px 8px rgba(76,175,80,0.12);">LEND NOW</button></a>
                    <?php endif; ?>
                </div>
                <?php
            }
        } else {
            echo '<p>No borrower posts available.</p>';
        }
        ?>
    </section>


    <div class="text">
    <a href="borrowerSignup.php"> <p><h2><button class="btn0">Post to borrow!</button></h2></p></a>

    </div>



<!-- Live Loan Requests -->
<section class="live-loans">
    <marquee behavior="scroll" direction="right" scrollamount="3">
        <p>📌 Ali needs 2500 for rent.</p>
        <p>📌 Sarah is requesting 3000 for books.</p>
        <p>📌 Mushfiq wants 7000 for tuition.</p>
    </marquee>
</section>

<!-- Student Loan Options -->
<section class="loan-options">
    <h2 class="fancy-font-heading">Student Loan Options</h2><br><br>
    <div class="loan-cards">
        <div class="loan-card">
            <div class="loan-front"><b>📖 Education Loan</b></div>
            <div class="loan-back">Cover tuition & academic expenses.</div>
        </div>
        <div class="loan-card">
            <div class="loan-front"><b>🍽 Daily Expenses Loan</b></div>
            <div class="loan-back">Manage food, transport & daily needs.</div>
        </div>
        <div class="loan-card">
            <div class="loan-front"><b>🏠 Housing Loan</b></div>
            <div class="loan-back">Pay your rent or hostel fees.</div>
        </div>
    </div>
</section>

<br><br>


<!-- Success Stories & Testimonials Container -->
<div class="stories-testimonials-container">
    <!-- Success Stories -->
    <section class="success-stories">
    <h2 style="color:#15421a;"> Some Happy moments 😊😊😊</h2>

        <div class="slider">
            <img src="images/h1.jpg" alt="Story 1">
            <img src="images/h3.jpg" alt="Story 2">
            <img src="images/h2.jpg" alt="Story 3">
        </div>
    </section>

    <!-- Testimonials -->
    <section class="testimonials">
        <h2 style="color:#15421a;">What Our Users Say...</h2>
        <div class="slider">
            <div class="testimonial">
                <div class="profile-pic">
                    <img src="images/saiful.jpeg" alt="Emily">
                </div>
                <p><b>"EduLift saved my semester!"</b></p>
                <span>- Saiful</span>
            </div>
            <div class="testimonial">
                <div class="profile-pic">
                    <img src="images/sristy.jpeg" alt="Ali">
                </div>
                <p><b>"Quick and secure loans!"</b></p>
                <span>- Sristy</span>
            </div>
            <div class="testimonial">
                <div class="profile-pic">
                    <img src="images/siam.jpeg" alt="Maria">
                </div>
                <p><b>"The best student loan platform!"</b></p>
                <span>- Siam</span>
            </div>
        </div>
    </section>
</div>


<script>
    $(document).ready(function(){
        $('.slider').slick({
            autoplay: true,
            autoplaySpeed: 3000,
            dots: false,
            arrows: false,
            infinite: true
        });

        $('.fade-in').each(function(i){
            setTimeout(() => { $(this).addClass('visible'); }, i * 500);
        });

        // Category filtering functionality
        $('.category-btn').click(function() {
            // Remove active class from all buttons
            $('.category-btn').removeClass('active');
            // Add active class to clicked button
            $(this).addClass('active');
            
            const category = $(this).data('category');
            
            if (category === 'all') {
                $('.ln-card').show();
            } else {
                $('.ln-card').each(function() {
                    if ($(this).data('category') !== category) {
                        $(this).hide();
                    } else {
                        $(this).show();
                    }
                });
            }
        });
    });
</script>

<style>
    .category-btn {
        margin: 0 10px;
        transition: all 0.3s ease;
    }
    
    .category-btn.active {
        background-color: #15421a;
        color: white;
        transform: scale(1.05);
    }
    
    .ln-card {
        transition: all 0.3s ease;
    }

    /* CSS for the fancy font heading */
    .fancy-font-heading {
        font-family: 'Playfair Display', serif; /* Use the fancy font */
        font-weight: 700; /* Make it bold */
        color: #15421a; /* Match the theme color */
        text-align: center; /* Center the heading */
        margin-bottom: 20px; /* Add some space below */
    }

    .stories-testimonials-container {
        display: flex;
        justify-content: center;
        gap: 64px;
        margin: 0 auto 64px auto;
        max-width: 1600px;
    }

    .success-stories, .testimonials {
        width: 650px;
        min-height: 750px;
        background: #e3f2fd;
        border-radius: 18px;
        box-shadow: 0 4px 18px rgba(21,66,26,0.07);
        border: 2px solid #90caf9;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 56px 40px;
        box-sizing: border-box;
    }

    .success-stories .slider,
    .testimonials .slider {
        width: 100%;
        height: 540px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .success-stories .slider img {
        width: 100%;
        height: 540px;
        object-fit: cover;
        border-radius: 12px;
    }

    .testimonials .testimonial {
        width: 100%;
        height: 540px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .testimonials .profile-pic img {
        width: 280px;
        height: 280px;
        object-fit: cover;
        border-radius: 50%;
        margin-bottom: 52px;
    }
</style>

    <?php include('templates/footer.php'); ?>
</body>
</html>
