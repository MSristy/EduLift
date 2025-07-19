<?php
// Include the database connection file
include 'db_connect.php';

$message = ''; // Message to display to the user (success or error)

// Check if the form was submitted using the POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize input to prevent XSS
    $title = htmlspecialchars($_POST['campaign-title']);
    // Filter goal amount to ensure it's a valid float
    $goal = filter_var($_POST['goal-amount'], FILTER_VALIDATE_FLOAT);
    $description = htmlspecialchars($_POST['description']);
    $category = htmlspecialchars($_POST['category']);
    $organizer_name = htmlspecialchars($_POST['your-name']);
    // Filter email to ensure it's a valid email format
    $organizer_email = filter_var($_POST['your-email'], FILTER_VALIDATE_EMAIL);

    // Validate required fields and data types
    if (empty($title) || $goal === false || empty($description) || empty($category) || empty($organizer_name) || $organizer_email === false) {
        $message = "<span class='error'>Error: Please fill in all required fields with valid data.</span>";
    } else {
        $image_path = NULL; // Initialize image path to NULL

        // --- Handle Image Upload ---
        // Check if a file was uploaded without errors
        if (isset($_FILES['upload-image']) && $_FILES['upload-image']['error'] == UPLOAD_ERR_OK) {
            $target_dir = "uploads/"; // Directory where uploaded images will be saved
            // Create the uploads directory if it doesn't exist
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true); // Create directory with read/write permissions
            }

            // Get the file extension
            $image_file_type = strtolower(pathinfo($_FILES['upload-image']['name'], PATHINFO_EXTENSION));
            // Define allowed file types
            $allowed_types = array('jpg', 'jpeg', 'png', 'gif');

            // Validate file type
            if (in_array($image_file_type, $allowed_types)) {
                // Generate a unique filename to prevent overwriting files with the same name
                $unique_file_name = uniqid() . '.' . $image_file_type;
                $target_file = $target_dir . $unique_file_name;

                // Move the uploaded file from temporary location to the target directory
                if (move_uploaded_file($_FILES['upload-image']['tmp_name'], $target_file)) {
                    $image_path = $target_file; // Store the file path to save in the database
                } else {
                    $message = "<span class='error'>Error uploading image.</span>";
                }
            } else {
                $message = "<span class='error'>Error: Only JPG, JPEG, PNG, and GIF files are allowed for image uploads.</span>";
            }
        }
        // --- End Image Upload Handling ---

        // If there were no validation or upload errors, proceed with database insertion
        if (empty($message)) {
            // Prepare an SQL statement for inserting the new fundraiser data
            // Using prepared statements prevents SQL injection
            $sql = "INSERT INTO fundraisers (title, goal, description, category, image_path, organizer_name, organizer_email)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";

            // Prepare the statement
            $stmt = $conn->prepare($sql);
            // Bind parameters to the statement (s=string, d=double/decimal)
            $stmt->bind_param("sdsssss", $title, $goal, $description, $category, $image_path, $organizer_name, $organizer_email);

            // Execute the prepared statement
            if ($stmt->execute()) {
                $new_fundraiser_id = $conn->insert_id; // Get the ID of the newly inserted fundraiser
                $message = "<span class='success'>New campaign created successfully!</span>";
                // Redirect the user to the new fundraiser's details page upon successful creation
                header("Location: fundraiser-details.php?id=" . $new_fundraiser_id);
                exit(); // Stop script execution after redirect
            } else {
                // Handle database insertion error
                $message = "<span class='error'>Error creating campaign: " . $stmt->error . "</span>";
            }

            // Close the prepared statement
            $stmt->close();
        }
    }

    // Close the database connection after all operations are done
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Start Your Campaign</title>
    <link rel="stylesheet" href="start-campaign.css">
</head>
<body>

    <div class="container form-container">
        <h1>Start Your Campaign</h1>
        <p>Create a donation campaign and inspire others to help.</p>

        <?php
        // Display success or error messages if they exist
        if (!empty($message)): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
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
                <div class="upload-area">
                     <input type="file" id="upload-image" name="upload-image" accept="image/*">
                     <p>+ Upload Image</p>
                </div>
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

    <style>
    .start-fund-container {
        text-align: center;
        padding: 20px;
        background-color: #f9f9f9;
    }

    .start-fund-button {
        display: inline-block;
        padding: 12px 24px;
        background-color: #15421a;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        font-weight: bold;
        transition: background-color 0.3s ease;
    }

    .start-fund-button:hover {
        background-color: #1a521f;
    }
    </style>
</body>
</html>
