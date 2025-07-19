<?php
// Include the database connection file
// This file contains the code to connect to your MySQL database ($conn)
include 'db_connect.php';

// Get the fundraiser ID from the URL query parameter (e.g., ?id=1)
// intval() is used to ensure the ID is treated as an integer
$fundraiser_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$fundraiser = null; // Variable to store fundraiser details
$donations = []; // Array to store donations for this fundraiser
$donation_message = ''; // Message to display to the user after a donation attempt

// --- Handle Donation Form Submission ---
// Check if the request method is POST and if the 'donate' hidden input is set
// Also ensure a valid fundraiser_id is present
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['donate']) && $fundraiser_id > 0) {
    // Get donation form data and sanitize input
    $donor_name = htmlspecialchars($_POST['donor_name']);
    // Filter donation amount to ensure it's a valid floating-point number
    $donation_amount = filter_var($_POST['donation_amount'], FILTER_VALIDATE_FLOAT);
    $comment = htmlspecialchars($_POST['comment']);

    // Basic validation for the donation amount
    if ($donation_amount === false || $donation_amount <= 0) {
        // Set an error message if the amount is invalid
        $donation_message = "<span class='error'>Please enter a valid donation amount.</span>";
    } else {
        // Start a database transaction
        // This ensures that both the donation insertion and fundraiser update succeed or fail together
        $conn->begin_transaction();

        try {
            // Prepare an SQL statement to insert the new donation
            $sql_insert_donation = "INSERT INTO donations (fundraiser_id, donor_name, amount, comment) VALUES (?, ?, ?, ?)";
            // Prepare the statement
            $stmt_insert_donation = $conn->prepare($sql_insert_donation);
            // Bind parameters (i=integer, s=string, d=double/decimal)
            $stmt_insert_donation->bind_param("isds", $fundraiser_id, $donor_name, $donation_amount, $comment);

            // Execute the insert statement
            if (!$stmt_insert_donation->execute()) {
                // If insertion fails, throw an exception to trigger rollback
                throw new Exception("Error inserting donation: " . $stmt_insert_donation->error);
            }
            // Close the statement
            $stmt_insert_donation->close();

            // Prepare an SQL statement to update the amount_raised for the fundraiser
            $sql_update_fundraiser = "UPDATE fundraisers SET amount_raised = amount_raised + ? WHERE id = ?";
            // Prepare the statement
            $stmt_update_fundraiser = $conn->prepare($sql_update_fundraiser);
            // Bind parameters (d=double/decimal, i=integer)
            $stmt_update_fundraiser->bind_param("di", $donation_amount, $fundraiser_id);

            // Execute the update statement
            if (!$stmt_update_fundraiser->execute()) {
                 // If update fails, throw an exception to trigger rollback
                 throw new Exception("Error updating fundraiser amount: " . $stmt_update_fundraiser->error);
            }
            // Close the statement
            $stmt_update_fundraiser->close();

            // If both operations were successful, commit the transaction
            $conn->commit();

            // Set a success message
            $donation_message = "<span class='success'>Thank you for your donation!</span>";

            // Redirect to the same page after successful donation
            // This prevents form resubmission if the user refreshes the page
            header("Location: fundraiser-details.php?id=" . $fundraiser_id);
            exit(); // Stop script execution after redirect

        } catch (Exception $e) {
            // If any error occurred during the transaction, rollback the changes
            $conn->rollback();
            // Set an error message including the exception message
            $donation_message = "<span class='error'>Donation failed: " . $e->getMessage() . "</span>";
             // In a real application, you would also log this error for debugging
             // error_log($e->getMessage());
        }
    }
}
// --- End Handle Donation Form Submission ---


// --- Fetch Fundraiser Details and Donations (Existing code, modified to use prepared statements) ---
// Check if the fundraiser ID is valid before attempting to fetch data
if ($fundraiser_id > 0) {
    // Prepare SQL statement to get fundraiser details using the ID
    $sql_fundraiser = "SELECT * FROM fundraisers WHERE id = ?";
    // Prepare the statement
    $stmt_fundraiser = $conn->prepare($sql_fundraiser);
    // Bind the fundraiser ID parameter
    $stmt_fundraiser->bind_param("i", $fundraiser_id);
    // Execute the statement
    $stmt_fundraiser->execute();
    // Get the result set
    $result_fundraiser = $stmt_fundraiser->get_result();

    // Check if a fundraiser was found with the given ID
    if ($result_fundraiser->num_rows > 0) {
        // Fetch the fundraiser details as an associative array
        $fundraiser = $result_fundraiser->fetch_assoc();

        // Calculate the percentage of the goal that has been raised
        // Avoid division by zero if the goal is 0
        $percentage_raised = ($fundraiser['goal'] > 0) ? round(($fundraiser['amount_raised'] / $fundraiser['goal']) * 100) : 0;

        // Prepare SQL statement to fetch donations for this specific fundraiser, ordered by date
        $sql_donations = "SELECT donor_name, amount, comment, donated_at FROM donations WHERE fundraiser_id = ? ORDER BY donated_at DESC";
        // Prepare the statement
        $stmt_donations = $conn->prepare($sql_donations);
        // Bind the fundraiser ID parameter
        $stmt_donations->bind_param("i", $fundraiser_id);
        // Execute the statement
        $stmt_donations->execute();
        // Get the result set
        $result_donations = $stmt_donations->get_result();

        // Check if there are any donations and fetch them into an array
        if ($result_donations->num_rows > 0) {
            while($row = $result_donations->fetch_assoc()) {
                $donations[] = $row;
            }
        }
        // Close the donations statement
        $stmt_donations->close();

    } else {
        // If no fundraiser was found with the ID, set fundraiser to false
        $fundraiser = false;
    }

    // Close the fundraiser statement
    $stmt_fundraiser->close();

} else {
    // If the fundraiser ID is invalid or missing, set fundraiser to false
    $fundraiser = false;
}

// Close the database connection after all database operations are complete
$conn->close();

// Helper function to format time elapsed (basic example)
// This function calculates and returns a human-readable string for how long ago a datetime occurred
function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime; // Current time
    $ago = new DateTime($datetime); // The past datetime
    $diff = $now->diff($ago); // Calculate the difference

    // Adjust for weeks
    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    // Define the time units and their singular/plural forms
    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    // Build the string from the difference components
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]); // Remove units that are zero
        }
    }

    // If not requesting full detail, only show the most significant unit
    if (!$full) $string = array_slice($string, 0, 1);
    // Join the parts of the string, or return 'just now' if no difference
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $fundraiser ? htmlspecialchars($fundraiser['title']) : 'Fundraiser Not Found'; ?></title>
    <link rel="stylesheet" href="fundraiser-details.css">
    </head>
<body>
<?php include('templates/header2.php'); ?>
    <div class="container fundraiser-page-container">
        <?php if ($fundraiser): ?>
            <div class="fundraiser-content">
                <div class="fundraiser-image">
                    <?php if (!empty($fundraiser['image_path']) && file_exists($fundraiser['image_path'])): ?>
                        <img src="<?php echo htmlspecialchars($fundraiser['image_path']); ?>" alt="<?php echo htmlspecialchars($fundraiser['title']); ?>">
                    <?php else: ?>
                        <img src="https://via.placeholder.com/700x400?text=No+Image" alt="No image available">
                    <?php endif; ?>
                </div>

                <div class="fundraiser-summary-mobile">
                     <h1><?php echo number_format($fundraiser['amount_raised'], 2); ?> BDT raised</h1>
                     <p class="goal-donations"><?php echo number_format($fundraiser['goal'], 0); ?>K goal • <?php echo count($donations); ?> donations</p>
                     <div class="progress-circle-mobile">
                         <div class="progress-text"><?php echo $percentage_raised; ?>%</div>
                         </div>
                     <button class="share-button" id="share-button-mobile">Share</button>
                     <button class="donate-button" id="donate-now-mobile">Donate now</button>
                </div>

                <div class="fundraiser-details">
                    <h1 class="fundraiser-title-desktop"><?php echo number_format($fundraiser['amount_raised'], 2); ?> BDT raised</h1>

                    <div class="team-fundraiser">
                        <div class="icon-placeholder"></div> <span>Team fundraiser</span>
                         <p><span><?php echo htmlspecialchars($fundraiser['organizer_name']); ?></span> is organizing this fundraiser.</p>
                    </div>

                    <div class="donation-protected">
                        <div class="icon-placeholder"></div> <span>Donation protected</span>
                    </div>

                    <div class="fundraiser-description">
                        <p>
                           <?php echo nl2br(htmlspecialchars($fundraiser['description'])); ?>
                        </p>
                        </div>

                    <div class="fundraiser-updates">
                        <h2>Update (0)</h2>
                        <p>No updates available yet.</p>
                    </div>

                    <div class="fundraiser-support">
                        <h2>Words of support (0)</h2>
                        <p>No words of support yet.</p>
                    </div>

                    <div class="button-group-bottom">
                         <button class="share-button" id="share-button-bottom">Share</button>
                         <button class="donate-button" id="donate-now-bottom">Donate now</button>
                     </div>
                </div>
            </div>

            <aside class="fundraiser-sidebar">
                <div class="sidebar-summary">
                    <h2><?php echo number_format($fundraiser['amount_raised'], 2); ?> BDT raised</h2>
                    <p class="goal-donations"><?php echo number_format($fundraiser['goal'], 0); ?>K goal • <?php echo count($donations); ?> donations</p>
                    <div class="progress-circle">
                        <div class="progress-inner">
                            <div class="progress-text"><?php echo $percentage_raised; ?>%</div>
                            </div>
                         </div>
                </div>

                <div class="sidebar-actions">
                    <button class="share-button" id="share-button-sidebar">Share</button>
                    <button class="donate-button" id="donate-now-sidebar">Donate now</button>
                </div>

                <div class="donation-form-section">
                    <h3>Make a Donation</h3>
                    <?php
                    // Display success or error messages for donation attempts
                    if (!empty($donation_message)): ?>
                        <p class="donation-message"><?php echo $donation_message; ?></p>
                    <?php endif; ?>
                    <form action="fundraiser-details.php?id=<?php echo $fundraiser['id']; ?>" method="post">
                        <div class="form-group">
                            <label for="donation-amount">Amount ($)</label>
                            <input type="number" id="donation-amount" name="donation_amount" step="0.01" min="1" required>
                        </div>
                         <div class="form-group">
                            <label for="donor-name">Your Name (Optional)</label>
                            <input type="text" id="donor-name" name="donor_name">
                        </div>
                         <div class="form-group">
                            <label for="comment">Comment (Optional)</label>
                            <textarea id="comment" name="comment" rows="3"></textarea>
                        </div>
                        <input type="hidden" name="donate" value="1">
                        <button type="submit" class="submit-donation-button">Donate</button>
                    </form>
                </div>
                 <div class="sidebar-donors">
                    <h3><?php echo count($donations); ?> people just donated</h3>
                    <div class="donor-list">
                        <?php if (count($donations) > 0): ?>
                            <?php foreach ($donations as $donation): ?>
                                <div class="donor-item">
                                    <div class="donor-avatar"></div> <div class="donor-info">
                                        <span class="donor-name"><?php echo htmlspecialchars($donation['donor_name'] ?: 'Anonymous'); ?></span>
                                        <span class="donation-amount"><?php echo number_format($donation['amount'], 0); ?> • <?php echo time_elapsed_string($donation['donated_at']); ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No donations yet.</p>
                        <?php endif; ?>
                    </div>
                    <a href="#" class="see-all-donors">See all</a>
                </div>
            </aside>

        <?php else: ?>
            <div class="fundraiser-not-found">
                <h1>Fundraiser Not Found</h1>
                <p>The requested fundraiser could not be found.</p>
                <p><a href="index.php">Go back to the home page</a></p>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // --- Share Button Functionality (Copy Link to Clipboard) ---
        // This script adds click event listeners to all elements with the class 'share-button'
        document.addEventListener('DOMContentLoaded', function() {
            const shareButtons = document.querySelectorAll('.share-button');

            shareButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Get the current page URL from the browser's address bar
                    const pageUrl = window.location.href;

                    // Use the modern Clipboard API to copy the URL to the user's clipboard
                    navigator.clipboard.writeText(pageUrl).then(function() {
                        // Provide feedback to the user upon successful copy
                        alert('Link copied to clipboard!');
                    }).catch(function(err) {
                        // Fallback method for older browsers or if Clipboard API is not available
                        console.error('Could not copy text using Clipboard API: ', err);
                        try {
                            // Create a temporary textarea element
                            const tempInput = document.createElement('textarea');
                            // Set its value to the URL
                            tempInput.value = pageUrl;
                            // Append it to the document body
                            document.body.appendChild(tempInput);
                            // Select the text in the textarea
                            tempInput.select();
                            // Execute the copy command
                            document.execCommand('copy');
                            // Remove the temporary textarea
                            document.body.removeChild(tempInput);
                            // Provide feedback
                            alert('Link copied to clipboard!');
                        } catch (fallbackErr) {
                            console.error('Fallback copy method failed: ', fallbackErr);
                            alert('Could not copy link. Please copy it manually from the address bar.');
                        }
                    });
                });
            });
        });
        // --- End Share Button Functionality ---

        // --- Donate Now Button Functionality (Scroll to Donation Form) ---
        // This script makes the "Donate now" buttons scroll the user to the donation form section
         document.addEventListener('DOMContentLoaded', function() {
             // Select all elements with the class 'donate-button'
             const donateButtons = document.querySelectorAll('.donate-button');
             // Select the donation form section
             const donationFormSection = document.querySelector('.donation-form-section');

             // Check if the donation form section exists on the page
             if (donationFormSection) {
                 // Add a click event listener to each donate button
                 donateButtons.forEach(button => {
                     button.addEventListener('click', function(event) {
                         event.preventDefault(); // Prevent the default button action (e.g., form submission if it were a submit button)

                         // Use scrollIntoView to smoothly scroll the page to the donation form section
                         donationFormSection.scrollIntoView({
                             behavior: 'smooth' // Use smooth scrolling
                         });

                         // Optional: Focus on the donation amount input field after scrolling
                         const amountInput = document.getElementById('donation-amount');
                         if (amountInput) {
                             amountInput.focus(); // Place the cursor in the amount input
                         }
                     });
                 });
             }
         });
        // --- End Donate Now Button Functionality ---

    </script>
    <?php include('templates/footer.php'); ?>
</body>
</html>
