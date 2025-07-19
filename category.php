<?php
// Include the database connection file
include 'db_connect.php';

// --- Handle Campaign Creation Form Submission (same as index1.php) ---
$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['campaign-title'])) {
    $title = htmlspecialchars($_POST['campaign-title']);
    $goal = filter_var($_POST['goal-amount'], FILTER_VALIDATE_FLOAT);
    $description = htmlspecialchars($_POST['description']);
    $category = htmlspecialchars($_POST['category']);
    $organizer_name = htmlspecialchars($_POST['your-name']);
    $organizer_email = filter_var($_POST['your-email'], FILTER_VALIDATE_EMAIL);
    if (empty($title) || $goal === false || empty($description) || empty($category) || empty($organizer_name) || $organizer_email === false) {
        $message = "<span class='error'>Error: Please fill in all required fields with valid data.</span>";
    } else {
        $image_path = NULL;
        if (isset($_FILES['upload-image']) && $_FILES['upload-image']['error'] == UPLOAD_ERR_OK) {
            $target_dir = "uploads/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $image_file_type = strtolower(pathinfo($_FILES['upload-image']['name'], PATHINFO_EXTENSION));
            $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
            if (in_array($image_file_type, $allowed_types)) {
                $unique_file_name = uniqid() . '.' . $image_file_type;
                $target_file = $target_dir . $unique_file_name;
                if (move_uploaded_file($_FILES['upload-image']['tmp_name'], $target_file)) {
                    $image_path = $target_file;
                } else {
                    $message = "<span class='error'>Error uploading image.</span>";
                }
            } else {
                $message = "<span class='error'>Error: Only JPG, JPEG, PNG, and GIF files are allowed for image uploads.</span>";
            }
        }
        if (empty($message)) {
            $sql = "INSERT INTO fundraisers (title, goal, description, category, image_path, organizer_name, organizer_email) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sdsssss", $title, $goal, $description, $category, $image_path, $organizer_name, $organizer_email);
            if ($stmt->execute()) {
                // Success: reload the page to show the new fundraiser and clear POST data
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit();
            } else {
                $message = "<span class='error'>Error creating campaign: " . $stmt->error . "</span>";
            }
            $stmt->close();
        }
    }
}

// Get the category from the URL query parameter (e.g., ?cat=medical)
// Use htmlspecialchars to prevent XSS if the category name is displayed
$selected_category = isset($_GET['cat']) ? htmlspecialchars($_GET['cat']) : '';

$fundraisers = []; // Array to store fundraisers for the selected category
$page_title = "All Categories"; // Default page title

// Fetch fundraisers from the database filtered by category AND not yet completed
if (!empty($selected_category)) {
    // Prepare SQL statement to fetch fundraisers of a specific category that have NOT reached their goal
    $sql = "SELECT id, title, goal, amount_raised, image_path, category FROM fundraisers WHERE category = ? AND amount_raised < goal ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $selected_category);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $fundraisers[] = $row;
        }
    }
    $stmt->close();
    // Set title based on category, capitalizing the first letter
    $page_title = ucfirst($selected_category) . " Fundraisers";
} else {
    // If no category is specified, show a message
    $page_title = "Select a Category";
}

// Close the database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="style.css">
    <style>
    .start-fund-button {
        display: inline-block;
        padding: 12px 24px;
        background-color: #15421a;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        font-weight: bold;
        transition: background-color 0.3s ease;
        margin-left: 10px;
    }
    .start-fund-button:hover {
        background-color: #1a521f;
    }
    /* Modal styles (copied from index1.php) */
    .modal-bg {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0; top: 0; width: 100vw; height: 100vh;
        background: rgba(0,0,0,0.5);
        justify-content: center; align-items: flex-start;
        overflow-y: auto;
        padding-top: 40px;
    }
    .modal-bg.active { display: flex; }
    .modal-content {
        background: linear-gradient(135deg, #f8fafc 0%, #e6f4ea 100%);
        padding: 0;
        border-radius: 16px;
        max-width: 500px;
        width: 100%;
        position: relative;
        box-shadow: 0 8px 32px rgba(21,66,26,0.18), 0 1.5px 8px rgba(0,0,0,0.08);
        max-height: 90vh;
        overflow-y: auto;
        animation: modalPopIn 0.25s cubic-bezier(.4,2,.6,1) 1;
    }
    @keyframes modalPopIn {
      0% { transform: scale(0.95) translateY(30px); opacity: 0; }
      100% { transform: scale(1) translateY(0); opacity: 1; }
    }
    .modal-header {
        background: linear-gradient(90deg, #15421a 60%, #1a521f 100%);
        color: #fff;
        border-radius: 16px 16px 0 0;
        padding: 1.2rem 1.5rem 1rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.7rem;
    }
    .modal-header .modal-icon {
        font-size: 2rem;
        background: #fff2;
        border-radius: 50%;
        padding: 0.3em 0.5em;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .modal-header h2 {
        margin: 0;
        font-size: 1.4rem;
        font-weight: 700;
        letter-spacing: 0.5px;
    }
    .modal-close {
        position: absolute;
        top: 14px; right: 18px;
        font-size: 2rem;
        color: #fff;
        background: none;
        border: none;
        cursor: pointer;
        z-index: 10;
        transition: color 0.2s;
    }
    .modal-close:hover {
        color: #ff4d4d;
    }
    .modal-content form {
        padding: 1.5rem 1.5rem 1.2rem 1.5rem;
    }
    .form-group { margin-bottom: 1.2rem; }
    .form-group label {
        display: block;
        margin-bottom: 0.4rem;
        font-weight: 500;
        color: #15421a;
        letter-spacing: 0.2px;
    }
    .form-group input, .form-group textarea, .form-group select {
        width: 100%;
        padding: 0.65rem 0.9rem;
        border: 1.5px solid #cbe3d3;
        border-radius: 6px;
        background: #f6fef9;
        font-size: 1rem;
        transition: border-color 0.2s, box-shadow 0.2s;
        outline: none;
        box-sizing: border-box;
    }
    .form-group input:focus, .form-group textarea:focus, .form-group select:focus {
        border-color: #1a521f;
        box-shadow: 0 0 0 2px #cbe3d3;
        background: #fff;
    }
    .form-group textarea {
        min-height: 90px;
        resize: vertical;
    }
    .submit-button {
        background: linear-gradient(90deg, #15421a 60%, #1a521f 100%);
        color: #fff;
        border: none;
        padding: 12px 32px;
        border-radius: 6px;
        font-weight: bold;
        font-size: 1.1rem;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(21,66,26,0.10);
        transition: background 0.2s, box-shadow 0.2s, transform 0.1s;
        margin-top: 0.5rem;
    }
    .submit-button:hover {
        background: linear-gradient(90deg, #1a521f 60%, #15421a 100%);
        box-shadow: 0 4px 16px rgba(21,66,26,0.13);
        transform: translateY(-2px) scale(1.03);
    }
    .message {
        margin-bottom: 1rem;
        display: block;
        font-weight: 500;
        color: #d32f2f;
        text-align: center;
    }
    </style>
</head>
<body>
    <?php include('templates/header1.php'); ?>
    <?php include('templates/header2.php'); ?>

    <section class="icon-filter-bar">
        <div class="container icon-container">
            <a href="category.php?cat=medical" class="icon-item-link">
                <div class="icon-item">
                    <img src="medical-logo.jpg" alt="Medical Category">
                </div>
            </a>
             <a href="category.php?cat=memorial" class="icon-item-link">
                <div class="icon-item">
                     <img src="memorial.jpg" alt="Memorial Category">
                </div>
            </a>
             <a href="category.php?cat=emergency" class="icon-item-link">
                <div class="icon-item">
                     <img src="emergency.jpg" alt="Emergency Category">
                </div>
            </a>
             <a href="category.php?cat=education" class="icon-item-link">
                <div class="icon-item">
                     <img src="education.jpg" alt="Education Category">
                </div>
            </a>
             <a href="category.php?cat=other" class="icon-item-link text-icon">
                Other
            </a>
             <a href="#" class="start-fund-button" id="openStartFundModal">Start a RiseFund</a>
        </div>
    </section>
    <!-- Modal Popup for Start Campaign -->
    <div class="modal-bg" id="startFundModal">
      <div class="modal-content">
        <button class="modal-close" id="closeStartFundModal" title="Close">&times;</button>
        <div class="modal-header">
          <span class="modal-icon">&#128640;</span>
          <h2>Start Your Campaign</h2>
        </div>
        <form action="" method="post" enctype="multipart/form-data">
            <p style="margin:0 0 1.2rem 0;color:#1a521f;font-weight:500;">Create a donation campaign and inspire others to help.</p>
            <?php if (!empty($message)): ?>
                <span class="message"><?php echo $message; ?></span>
            <?php endif; ?>
            <div class="form-group">
                <label for="campaign-title">Campaign Title</label>
                <input type="text" id="campaign-title" name="campaign-title" required>
            </div>
            <div class="form-group">
                <label for="goal-amount">Goal Amount ($)</label>
                <input type="number" id="goal-amount" name="goal-amount" required min="0" step="0.01">
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="6" required></textarea>
            </div>
            <div class="form-group">
                <label for="category">Category</label>
                <select id="category" name="category" required>
                    <option value="">Select Category</option>
                    <option value="medical">Medical</option>
                    <option value="memorial">Memorial</option>
                    <option value="emergency">Emergency</option>
                    <option value="education">Education</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="form-group">
                <label for="upload-image">Upload Image</label>
                <input type="file" id="upload-image" name="upload-image" accept="image/*">
            </div>
            <div class="form-group">
                <label for="your-name">Your Name</label>
                <input type="text" id="your-name" name="your-name" required>
            </div>
            <div class="form-group">
                <label for="your-email">Your Email</label>
                <input type="email" id="your-email" name="your-email" required>
            </div>
            <button type="submit" class="submit-button">Start Campaign</button>
        </form>
      </div>
    </div>
    <main class="container content-area">
        <section class="fundraiser-category">
            <h2><?php echo $page_title; ?></h2>
            <div class="fundraiser-grid">
                <?php if (!empty($selected_category) && count($fundraisers) > 0): ?>
                    <?php foreach ($fundraisers as $fundraiser): ?>
                        <div class="fundraiser-item">
                            <a href="fundraiser-details.php?id=<?php echo $fundraiser['id']; ?>" class="fundraiser-item-link">
                                <?php if (!empty($fundraiser['image_path']) && file_exists($fundraiser['image_path'])): ?>
                                    <img src="<?php echo htmlspecialchars($fundraiser['image_path']); ?>" alt="<?php echo htmlspecialchars($fundraiser['title']); ?>">
                                <?php else: ?>
                                    <img src="https://via.placeholder.com/300x200?text=No+Image" alt="No image available">
                                <?php endif; ?>
                                <div class="item-details">
                                    <h3><?php echo htmlspecialchars($fundraiser['title']); ?></h3>
                                    <p>Raised: $<?php echo number_format($fundraiser['amount_raised'], 2); ?></p>
                                    <p>Goal: $<?php echo number_format($fundraiser['goal'], 2); ?></p>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php elseif (!empty($selected_category)): ?>
                     <p>No active fundraisers found in the "<?php echo htmlspecialchars($selected_category); ?>" category yet.</p>
                <?php else: ?>
                    <p>Please select a category from the icons above.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <footer>
        </footer>

    <script>
    // Modal open/close logic
    const openBtn = document.getElementById('openStartFundModal');
    const modalBg = document.getElementById('startFundModal');
    const closeBtn = document.getElementById('closeStartFundModal');
    openBtn.addEventListener('click', function(e) {
        e.preventDefault();
        modalBg.classList.add('active');
    });
    closeBtn.addEventListener('click', function() {
        modalBg.classList.remove('active');
    });
    window.addEventListener('click', function(e) {
        if (e.target === modalBg) {
            modalBg.classList.remove('active');
        }
    });
    </script>
</body>
</html>
