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
<section class="why-edulift">
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


    <section class="ln-section loan-card-grid">
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
            'tuition' => '#ff9800', // orange (matches your main accent)
            'living' => '#14532d', // dark green theme
            'books' => '#c8642a', // brownish-orange (matches your loan purpose text)
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
            // Gather all rows to determine max card height
            $rows = [];
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
            $maxCardHeight = 0;
            $cardHeights = [];
            // Output buffer to measure each card's height
            foreach ($rows as $row) {
                ob_start();
                ?>
                <div class="ln-card" style="visibility:hidden;position:absolute;z-index:-1;top:0;left:0;">
                    <span style="position:absolute;top:12px;left:12px;background:<?php echo $catColors[getCategoryFromPurpose($row['loan_purpose'])]; ?>;color:white;padding:5px 15px;border-radius:20px;font-size:0.95em;font-weight:bold;box-shadow:0 2px 8px rgba(0,0,0,0.08);z-index:2;letter-spacing:1px;"></span>
                    <div class="card-header" style="margin-top:30px;"></div>
                    <p style="margin:8px 0 0 0; color:#555;"></p>
                    <img class="student-img" src="" alt="Student" style="max-height:120px;object-fit:cover;box-shadow:0 2px 8px rgba(0,0,0,0.08);margin-bottom:10px;">
                    <p style="font-size:1.1em;margin:8px 0 0 0;"></p>
                    <div style="background:#fff3e0;border-left:4px solid <?php echo $catColors[getCategoryFromPurpose($row['loan_purpose'])]; ?>;padding:10px 14px;border-radius:7px;margin:12px 0 0 0;min-height:48px;text-align:left;"></div>
                    <div style="margin:12px 0 0 0;"></div>
                    <div style="margin:14px 0 0 0;text-align:right;color:#888;font-size:0.95em;"></div>
                    <div style="display:flex;justify-content:center;align-items:center;height:56px;margin-top:8px;min-height:56px;"></div>
                </div>
                <?php
                $cardHtml = ob_get_clean();
                $cardHeights[] = strlen($cardHtml);
            }
            if (count($cardHeights) > 0) {
                $maxCardHeight = max($cardHeights) * 0.38; // reduce estimated px height for a shorter card
                if ($maxCardHeight < 370) $maxCardHeight = 370; // set a reasonable minimum
            } else {
                $maxCardHeight = 370;
            }
            foreach ($rows as $row) {
                $category = getCategoryFromPurpose($row['loan_purpose']);
                $badgeColor = $catColors[$category];
                $badgeIcon = $catIcons[$category];
                $loanAccepted = false;
                $loan_id = $row['id'];
                $acceptedQuery = "SELECT * FROM accepted_loans WHERE borrower_id = '$loan_id' LIMIT 1";
                $acceptedResult = $conn->query($acceptedQuery);
                if ($acceptedResult && $acceptedResult->num_rows > 0) {
                    $loanAccepted = true;
                }
                ?>
                <div class="ln-card" data-category="<?php echo $category; ?>" style="height:<?php echo (int)$maxCardHeight; ?>px;display:flex;flex-direction:column;justify-content:space-between;">
                    <span style="position:absolute;top:12px;left:12px;background:<?php echo $badgeColor; ?>;color:white;padding:5px 15px;border-radius:20px;font-size:0.95em;font-weight:bold;box-shadow:0 2px 8px rgba(0,0,0,0.08);z-index:2;letter-spacing:1px;">
                        <?php echo $badgeIcon . ' ' . ucfirst($category); ?>
                    </span>
                    <div class="card-header" style="margin-top:30px;">
                        <img src="images/icon.png" alt="Loan Icon">
                        <h3 style="font-size:1.3rem; color:#15421a; letter-spacing:1px; margin-left:8px;"> <?php echo htmlspecialchars($row['student_name']); ?> </h3>
                    </div>
                    <p style="margin:8px 0 0 0; color:#555;"><strong>ID:</strong> <?php echo htmlspecialchars($row['student_id']); ?></p>
                    <?php if (!empty($row['loan_image'])): ?>
                        <img class="student-img" src="images/<?php echo htmlspecialchars($row['loan_image']); ?>" alt="Student" style="max-height:120px;object-fit:cover;box-shadow:0 2px 8px rgba(0,0,0,0.08);margin-bottom:10px;">
                    <?php else: ?>
                        <img class="student-img" src="images/md2.png" alt="Student" style="max-height:120px;object-fit:cover;box-shadow:0 2px 8px rgba(0,0,0,0.08);margin-bottom:10px;">
                    <?php endif; ?>
                    <p style="font-size:1.1em;margin:8px 0 0 0;"><b>৳<?php echo htmlspecialchars($row['loan_amount']); ?></b> <span style="color:#888;font-size:0.95em;">for</span> <span style="color:#c8642a;font-weight:bold;"> <?php echo htmlspecialchars($row['loan_purpose']); ?> </span></p>
                    <div style="background:#fff3e0;border-left:4px solid <?php echo $badgeColor; ?>;padding:10px 14px;border-radius:7px;margin:12px 0 0 0;min-height:48px;text-align:left;display:flex;flex-direction:column;justify-content:flex-start;height:100%;overflow:auto;">
                        <span style="font-weight:600;color:#b85c00;">Description:</span>
                        <span style="color:#333;word-break:break-word;white-space:pre-line;flex:1;"> <?php echo nl2br(htmlspecialchars($row['loan_description'])); ?> </span>
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
                    <div style="display:flex;justify-content:center;align-items:center;height:56px;margin-top:8px;min-height:56px;">
                        <?php if ($loanAccepted): ?>
                            <button class="btn lend lend-completed" disabled>LEND COMPLETED</button>
                        <?php else: ?>
                            <a href="LenderSignup.php" style="width:100%;max-width:90%;display:inline-block;"><button class="btn lend" style="width:100%;font-size:1.1em;background:#023d1b;color:#fff;font-weight:600;letter-spacing:1px;box-shadow:0 4px 16px rgba(2,61,27,0.13);border:2px solid #023d1b;transition:background 0.2s;">LEND NOW</button></a>
                        <?php endif; ?>
                    </div>
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
        background-color: #14532d !important;
        color: #fff !important;
        transform: scale(1.05);
    }
    .category-btn.active[data-category="all"] {
        background-color: #ff9800;
        color: #fff;
        box-shadow: 0 4px 16px rgba(255,152,0,0.18);
    }
    .category-btn:hover {
        background-color: #14532d;
        color: #fff;
        box-shadow: 0 4px 16px rgba(20,83,45,0.18);
        transform: translateY(-2px) scale(1.07);
    }
    .btn.lend {
        background: #023d1b !important;
        color: #fff !important;
        border: none !important;
        border-radius: 30px !important;
        font-size: 1.13em !important;
        font-weight: 700 !important;
        letter-spacing: 1px;
        box-shadow: 0 6px 24px rgba(2,61,27,0.18), 0 2px 8px rgba(2,61,27,0.13);
        padding: 12px 0 !important;
        width: 100%;
        max-width: 90%;
        transition: background 0.2s, box-shadow 0.2s, transform 0.1s;
        outline: none;
        cursor: pointer;
        text-transform: uppercase;
        position: relative;
        overflow: hidden;
    }
    .btn.lend:hover {
        background: #026c31 !important;
        color: #fff !important;
        transform: translateY(-2px) scale(1.03);
        box-shadow: 0 10px 32px rgba(2,61,27,0.22), 0 4px 16px rgba(2,61,27,0.13);
    }
    .btn.lend.lend-completed {
        background: linear-gradient(90deg, #b0852a 0%, #c8642a 100%) !important; /* darker gold/orange gradient */
        color: #fff !important;
        border: none !important;
        border-radius: 30px !important;
        font-size: 1.13em !important;
        font-weight: 700 !important;
        letter-spacing: 1px;
        box-shadow: 0 6px 24px rgba(200,100,42,0.18), 0 2px 8px rgba(33,140,116,0.10);
        padding: 12px 0 !important;
        width: 100%;
        max-width: 90%;
        opacity: 0.97;
        cursor: not-allowed;
        text-transform: uppercase;
        position: relative;
        overflow: hidden;
    }
    /* Removed the right icon from lend-completed button */
    .ln-card {
        position: relative;
        max-width: 320px;
        min-width: 0;
        width: 100%;
        margin: 0;
        box-shadow: 0 8px 32px 0 rgba(10,61,98,0.13), 0 1.5px 8px rgba(33,140,116,0.08);
        border: 2px solid #e0a94a; /* visible, soft orange border */
        background: linear-gradient(135deg, #f4f8fb 0%, #e9f5f2 100%);
        border-radius: 18px;
        padding: 4px 4px 4px 4px;
        margin-bottom: 0;
        transition: box-shadow 0.2s, transform 0.2s, border-color 0.2s;
        display: flex;
        flex-direction: column;
        align-items: stretch;
        min-height: 80px;
        height: 100%;
        transition: all 0.3s ease;
    }
    .ln-card .card-header {
        margin-top: 4px;
        display: flex;
        align-items: center;
    }
    .ln-card img.student-img {
        width: 100% !important;
        height: 180px !important;
        max-width: 100% !important;
        max-height: 180px !important;
        margin-bottom: 2px !important;
        object-fit: cover !important;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        display: block;
    }
    .ln-card .card-header {
        margin-top: 8px;
        display: flex;
        align-items: center;
    }
    .ln-card img.student-img {
        max-height: 50px !important;
        margin-bottom: 4px !important;
    }
    .ln-card .card-header {
        margin-top: 16px;
        display: flex;
        align-items: center;
    }
    .ln-card img.student-img {
        max-height: 70px !important;
        margin-bottom: 6px !important;
    }
    .ln-card p, .ln-card span, .ln-card div {
        font-size: 0.97em;
    }
    /* CSS for the fancy font heading */
    .fancy-font-heading {
        font-family: 'Playfair Display', serif;
        font-weight: 700;
        color: #15421a;
        text-align: center;
        margin-bottom: 20px;
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

    .ln-section.loan-card-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 36px 48px; /* 36px row gap, 48px column gap */
        justify-content: center;
        align-items: stretch;
        margin-bottom: 32px;
    }
</style>

    <?php include('templates/footer.php'); ?>
</body>
</html>
