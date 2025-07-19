<?php
// templates/loanCard.php
// Usage: include this file and provide $row, $category, $badgeColor, $badgeIcon, $buttonClass, $buttonText as needed
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
    <a href="lenderLend.php?borrower_id=<?php echo $row['id']; ?>">
        <button class="btn lend <?php echo $buttonClass; ?>" style="margin-top:18px;width:90%;font-size:1.1em;box-shadow:0 2px 8px rgba(76,175,80,0.12);" <?php if($buttonClass=="completed") echo "disabled style='pointer-events:none;opacity:0.8;'"; ?>>
            <?php if($buttonClass=="completed") echo '<i class="fa fa-check-circle"></i> '; ?><?php echo $buttonText; ?>
        </button>
    </a>
</div>
