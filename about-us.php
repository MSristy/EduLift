<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>About Us - EdLift</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="about-us.css">

</head>
<body>

<?php include('templates/header1.php'); ?>
<?php include('templates/header2.php'); ?>


<!-- Hero Section -->
<section class="hero">
  <div class="hero-overlay"></div>
  <div class="hero-content">
    <h1>Welcome to EdLift</h1>
    <p>Empowering UIU students through innovative crowdfunding and student loans to fuel their academic dreams.</p>
    <a href="#about" class="hero-btn">Learn More</a>
  </div>
</section>

<!-- About Summary -->
<section class="about-summary">
  <div class="about-card">
    <div class="about-accent"></div>
    <h2>Who We Are</h2>
    <p>EdLift is a platform dedicated to breaking financial barriers for UIU students. We bring together students, alumni, donors, and financial partners to create a thriving ecosystem of educational support.</p>
  </div>
</section>

<!-- Features Section -->
<section class="features">
  <div class="feature-card">
    <i class="fas fa-hand-holding-usd"></i>
    <h3>Student Loans</h3>
    <p>Affordable and flexible loans to help students stay focused on their education, not their financial stress.</p>
  </div>
  <div class="feature-card">
    <i class="fas fa-users"></i>
    <h3>Crowdfunding Support</h3>
    <p>Students can share their stories and gather support from the community to meet tuition and living expenses.</p>
  </div>
  <div class="feature-card">
    <i class="fas fa-globe"></i>
    <h3>Global Network</h3>
    <p>Connecting students with a global community of donors, mentors, and career opportunities.</p>
  </div>
</section>

<!-- Our Story Section -->
<section class="our-story">
  <h2>Our Journey</h2>
  <p>Since our founding, EdLift has been committed to unlocking academic potential by making education funding more accessible and transparent.</p>

  <div class="timeline">
    <div class="timeline-item">
      <h4>2024 - Idea Born</h4>
      <p>The concept of EdLift started with a vision to support UIU students facing financial hardships.</p>
    </div>
    <div class="timeline-item">
      <h4>2025 - Platform Launch</h4>
      <p>EdLift officially launched, connecting hundreds of students with funding sources within months.</p>
    </div>
    <div class="timeline-item">
      <h4>Future Vision</h4>
      <p>Expand EdLift across multiple universities and reach thousands of students across Bangladesh and beyond.</p>
    </div>
  </div>
</section>



<!-- Team Section -->
<section class="team-section">
    <h2>Our Team</h2>
    <div class="team-container">
        <?php
        // Team members data
        $team_members = [
            [
                'name' => 'Mahmuda Akter Sristy',
                'position' => 'Founder & CEO',
                'image' => 'images/sristy.jpeg',
                'phone' => '+880 1711-234567',
                'email' => 'mahmuda.sristy@edulift.com',
                'facebook' => 'https://facebook.com/mahmuda.sristy'
            ],
            [
                'name' => 'Md Al-Mahfuz Chowdhury',
                'position' => 'Head of Operations',
                'image' => 'images/siam.jpeg',
                'phone' => '+880 1712-345678',
                'email' => 'md.al-mahfuz@edulift.com',
                'facebook' => 'https://facebook.com/md.al-mahfuz'
            ],
            [
                'name' => 'Zawad Ridoy',
                'position' => 'Technical Lead',
                'image' => 'images/hridoy.jpeg',
                'phone' => '+880 1713-456789',
                'email' => 'zawad.ridoy@edulift.com',
                'facebook' => 'https://facebook.com/zawad.ridoy'
            ],
            [
                'name' => 'Saiful Islam',
                'position' => 'Technical Lead',
                'image' => 'images/saiful.jpeg',
                'phone' => '+880 1713-456789',
                'email' => 'saiful.islam@edulift.com',
                'facebook' => 'https://facebook.com/saiful.islam'
            ]
        ];

        // Display each team member
        foreach ($team_members as $member) {
            echo '<div class="team-member">
                <div class="profile-image">
                    <img src="' . $member['image'] . '" alt="' . $member['name'] . '">
                </div>
                <div class="member-info">
                    <h3>' . $member['name'] . '</h3>
                    <p class="position">' . $member['position'] . '</p>
                    <div class="contact-info contact-icons">
                        <a href="tel:' . $member['phone'] . '" class="icon-btn" title="Call"><i class="fas fa-phone"></i></a>
                        <a href="mailto:' . $member['email'] . '" class="icon-btn" title="Email"><i class="fas fa-envelope"></i></a>
                        <a href="' . $member['facebook'] . '" class="icon-btn" target="_blank" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                    </div>
                </div>
            </div>';
        }
        ?>
    </div>
</section>

<style>
.hero {
    position: relative;
    min-height: 350px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: url('images/education1.jpg') center/cover no-repeat;
    border-radius: 0 0 30px 30px;
    overflow: hidden;
    margin-bottom: 40px;
}
.hero-overlay {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(21, 66, 26, 0.7);
    z-index: 1;
}
.hero-content {
    position: relative;
    z-index: 2;
    color: #fff;
    text-align: center;
    padding: 40px 20px;
}
.hero-content h1 {
    font-size: 3em;
    font-weight: bold;
    margin-bottom: 20px;
    letter-spacing: 2px;
    text-shadow: 0 4px 24px rgba(0,0,0,0.3);
}
.hero-content p {
    font-size: 1.3em;
    margin-bottom: 30px;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
    text-shadow: 0 2px 8px rgba(0,0,0,0.2);
}
.hero-btn {
    display: inline-block;
    background: #fff;
    color: #15421a;
    font-weight: 600;
    padding: 14px 36px;
    border-radius: 30px;
    font-size: 1.1em;
    text-decoration: none;
    box-shadow: 0 2px 12px rgba(21,66,26,0.15);
    transition: background 0.3s, color 0.3s, transform 0.2s;
}
.hero-btn:hover {
    background: #15421a;
    color: #fff;
    transform: translateY(-3px) scale(1.04);
}

.about-summary {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 40px 0 30px 0;
    background: linear-gradient(90deg, #e8f5e9 0%, #f9fbe7 100%);
}
.about-card {
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 4px 24px rgba(21,66,26,0.08);
    padding: 40px 32px 32px 32px;
    max-width: 650px;
    width: 100%;
    text-align: center;
    position: relative;
}
.about-accent {
    width: 60px;
    height: 6px;
    background: #15421a;
    border-radius: 3px;
    margin: 0 auto 18px auto;
}
.about-card h2 {
    color: #15421a;
    font-size: 2.2em;
    margin-bottom: 18px;
    font-weight: 700;
    letter-spacing: 1px;
}
.about-card p {
    color: #444;
    font-size: 1.18em;
    line-height: 1.7;
    margin-bottom: 0;
}

.features {
    display: flex;
    justify-content: space-around;
    align-items: flex-start;
    flex-wrap: wrap;
    padding: 40px 20px;
    background-color: #fff;
    border-radius: 15px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.1);
    margin: 0 auto;
    max-width: 1200px;
}

.feature-card {
    background: #f9f9f9;
    border-radius: 12px;
    padding: 30px;
    text-align: center;
    margin: 15px;
    flex: 1 1 300px;
    transition: transform 0.3s, box-shadow 0.3s;
}

.feature-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.2);
}

.feature-card i {
    font-size: 2.5em;
    color: #15421a;
    margin-bottom: 15px;
}

.feature-card h3 {
    font-size: 1.8em;
    color: #15421a;
    margin-bottom: 10px;
}

.feature-card p {
    color: #666;
    font-size: 1.1em;
    line-height: 1.6;
}

.our-story {
    padding: 50px 20px;
    text-align: center;
    background-color: #f4f9f4;
}

.our-story h2 {
    color: #15421a;
    margin-bottom: 30px;
    font-size: 2.5em;
}

.timeline {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 20px;
    margin-top: 20px;
}

.timeline-item {
    background: #fff;
    border-radius: 12px;
    padding: 20px 30px;
    width: 100%;
    max-width: 600px;
    position: relative;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.timeline-item h4 {
    color: #15421a;
    margin-bottom: 10px;
    font-size: 1.4em;
}

.timeline-item p {
    color: #444;
    font-size: 1.1em;
    line-height: 1.6;
    margin-bottom: 0;
}

.team-section {
    padding: 50px 20px;
    background-color: #f9f9f9;
    text-align: center;
}

.team-section h2 {
    color: #15421a;
    margin-bottom: 40px;
    font-size: 2.5em;
}

.team-container {
    display: flex;
    justify-content: center;
    flex-wrap: nowrap;
    gap: 40px;
    max-width: 1300px;
    margin: 0 auto;
    overflow-x: auto;
}

.team-member {
    background: linear-gradient(135deg, #f9fbe7 0%, #e8f5e9 100%);
    border-radius: 18px;
    padding: 28px 20px 24px 20px;
    box-shadow: 0 8px 32px rgba(21,66,26,0.10);
    width: 300px;
    transition: transform 0.3s, box-shadow 0.3s, border 0.3s;
    border: 2px solid transparent;
    position: relative;
}
.team-member:hover {
    transform: translateY(-12px) scale(1.03);
    box-shadow: 0 16px 32px rgba(21,66,26,0.18);
    border: 2px solid #15421a;
}
.profile-image {
    width: 120px;
    height: 120px;
    margin: 0 auto 18px auto;
    border-radius: 50%;
    overflow: hidden;
    border: 4px solid #fff;
    box-shadow: 0 2px 12px rgba(21,66,26,0.10);
    background: #e8f5e9;
}
.profile-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.member-info h3 {
    color: #15421a;
    margin: 10px 0 4px 0;
    font-size: 1.35em;
    font-weight: 700;
}
.position {
    color: #388e3c;
    font-style: italic;
    margin-bottom: 18px;
    font-size: 1.05em;
}
.contact-info.contact-icons {
    display: flex;
    flex-direction: row;
    justify-content: center;
    gap: 16px;
    margin-top: 10px;
}
.icon-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background: #fff;
    color: #15421a;
    font-size: 1.25em;
    box-shadow: 0 2px 8px rgba(21,66,26,0.10);
    transition: background 0.3s, color 0.3s, box-shadow 0.3s, transform 0.2s;
    border: 1.5px solid #e0e0e0;
    text-decoration: none;
}
.icon-btn:hover {
    background: #15421a;
    color: #fff;
    box-shadow: 0 4px 16px rgba(21,66,26,0.18);
    transform: scale(1.08);
    border-color: #15421a;
}
</style>

<?php include('templates/footer.php'); ?>

</body>
</html>
