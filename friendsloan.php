<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Friends Loan</title>
    <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f9f9f9;
      color: #333;
    }
    .section {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 60px 10%;
      flex-wrap: wrap;
      animation: fadeIn 1s ease-in;
    }
    .section.reverse {
      flex-direction: row-reverse;
    }
    .image-box, .text-box {
      flex: 1 1 45%;
      min-width: 300px;
      margin: 20px;
    }
    .image-box img {
      width: 100%;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      animation: slideIn 1.2s ease-out;
    }
    .text-box h2 {
      font-size: 28px;
      margin-bottom: 20px;
      color: #c8642a;
    }
    .text-box p {
      font-size: 18px;
      line-height: 1.6;
    }
    .cta {
      text-align: center;
      padding: 40px 0;
    }
    .cta a {
      background-color: #ff9800;
      color: #fff;
      padding: 12px 30px;
      text-decoration: none;
      border-radius: 8px;
      font-size: 18px;
      transition: background 0.2s;
    }
    .cta a:hover {
      background-color: #c8642a;
    }

    @keyframes fadeIn {
      from {opacity: 0;}
      to {opacity: 1;}
    }
    @keyframes slideIn {
      from {transform: translateY(30px); opacity: 0;}
      to {transform: translateY(0); opacity: 1;}
    }
  </style>
</head>
<body>

    <?php include('templates/header1.php'); ?>
    <?php include('templates/header2.php'); ?>


    <!-- Main Heading Section -->
    <div class="section">
    <div class="image-box">
      <img src="images/friend.jpg" alt="Skill-based Loan Illustration">
    </div>
    <div class="text-box">
      <h2 style="font-size:2rem;color:#c8642a;margin-bottom:18px;">Borrow or Repay with Skills—Your Talents Have Value!</h2>
      <div style="background:#eaf6fb;padding:18px 22px 18px 22px;border-radius:10px;box-shadow:0 2px 8px rgba(44,62,80,0.07);margin-bottom:10px;">
        <p style="font-size:1.15rem;margin:0 0 10px 0;color:#222;font-weight:500;">No cash? No problem! With our <a href='signup.php' style="color:#c8642a;font-weight:600;text-decoration:underline;cursor:pointer;">Skill-for-Loan</a> system, you can:</p>
        <ul style="font-size:1.08rem;line-height:1.7;margin:0 0 0 20px;color:#333;">
          <li>Teach a friend a subject or skill you know well</li>
          <li>Help with a project, assignment, or creative task</li>
          <li>Share your notes, resources, or expertise</li>
        </ul>
        <p style="margin-top:12px;font-size:1.08rem;color:#444;">Every contribution counts—repay your loan with what you do best and build trust in your circle!</p>
      </div>
      <div style="margin-top:10px;font-size:1.05rem;color:#555;">This modern approach makes borrowing easy, fair, and rewarding for everyone. Your skills are as valuable as money!</div>
    </div>
  </div>

  <div class="section reverse">
    <div class="image-box">
      <img src="images/friend2.jpeg" alt="Loan Approval Flow">
    </div>
    <div class="text-box">
      <h2 style="font-size:2rem;color:#c8642a;margin-bottom:18px;">How Does Friends Loan Work?</h2>
      <div style="background:#eaf6fb;padding:18px 22px 18px 22px;border-radius:10px;box-shadow:0 2px 8px rgba(44,62,80,0.07);margin-bottom:10px;">
        <ol style="font-size:1.08rem;line-height:1.7;margin:0 0 0 20px;color:#333;">
          <li><b>Post a loan request</b>—share your reason and amount needed</li>
          <li><b>Friends get notified</b> and can offer help or request a skill in return</li>
          <li><b>Choose your lender</b>—pick who you trust or who needs your skills</li>
          <li><b>Repay with money or skills</b>—your choice, your value</li>
          <li><b>Trust Score updates</b>—every successful repayment boosts your reputation</li>
          <li><b>Friend testimonials</b>—get support and credibility from your circle</li>
        </ol>
        <p style="margin-top:12px;font-size:1.08rem;color:#444;">It’s simple, transparent, and designed for real friendships. Support each other, grow together!</p>
      </div>
      <div style="margin-top:10px;font-size:1.05rem;color:#555;">Experience a smarter, more human way to borrow and lend—where trust and talent matter most.</div>
    </div>
  </div>

  <div class="cta">
    <a href="signup.php">Join Now to Start Borrowing with Friends</a>
  </div>



    <?php include('templates/footer.php'); ?>

</body>
</html> 