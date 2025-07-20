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
    // Check if fundraiser is already completed
    $sql_check = "SELECT amount_raised, goal FROM fundraisers WHERE id = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("i", $fundraiser_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    $row_check = $result_check->fetch_assoc();
    $stmt_check->close();
    $current_raised = $row_check['amount_raised'];
    $goal = $row_check['goal'];
    if ($goal > 0 && $current_raised >= $goal) {
        $donation_message = "<span class='error'>This fundraiser is already completed. No more donations are accepted.</span>";
    } else {
        $donor_name = htmlspecialchars($_POST['donor_name']);
        $donation_amount = filter_var($_POST['donation_amount'], FILTER_VALIDATE_FLOAT);
        $comment = htmlspecialchars($_POST['comment']);
        if ($donation_amount === false || $donation_amount <= 0) {
            $donation_message = "<span class='error'>Please enter a valid donation amount.</span>";
        } else {
            // Cap donation so it doesn't exceed the goal
            $donation_to_add = $donation_amount;
            if ($goal > 0 && ($current_raised + $donation_amount) > $goal) {
                $donation_to_add = $goal - $current_raised;
            }
            if ($donation_to_add <= 0) {
                $donation_message = "<span class='error'>This fundraiser is already completed. No more donations are accepted.</span>";
            } else {
                $conn->begin_transaction();
                try {
                    $sql_insert_donation = "INSERT INTO donations (fundraiser_id, donor_name, amount, comment) VALUES (?, ?, ?, ?)";
                    $stmt_insert_donation = $conn->prepare($sql_insert_donation);
                    $stmt_insert_donation->bind_param("isds", $fundraiser_id, $donor_name, $donation_to_add, $comment);
                    if (!$stmt_insert_donation->execute()) {
                        throw new Exception("Error inserting donation: " . $stmt_insert_donation->error);
                    }
                    $stmt_insert_donation->close();
                    $sql_update_fundraiser = "UPDATE fundraisers SET amount_raised = amount_raised + ? WHERE id = ?";
                    $stmt_update_fundraiser = $conn->prepare($sql_update_fundraiser);
                    $stmt_update_fundraiser->bind_param("di", $donation_to_add, $fundraiser_id);
                    if (!$stmt_update_fundraiser->execute()) {
                        throw new Exception("Error updating fundraiser amount: " . $stmt_update_fundraiser->error);
                    }
                    $stmt_update_fundraiser->close();
                    $conn->commit();
                    $donation_message = "<span class='success'>Thank you for your donation!</span>";
                    header("Location: fundraiser-details.php?id=" . $fundraiser_id);
                    exit();
                } catch (Exception $e) {
                    $conn->rollback();
                    $donation_message = "<span class='error'>Donation failed: " . $e->getMessage() . "</span>";
                }
            }
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
                     <h1>৳<?php echo number_format($fundraiser['amount_raised'], 2); ?> raised</h1>
                     <p class="goal-donations">৳<?php echo number_format($fundraiser['goal'], 0); ?>K goal • <?php echo count($donations); ?> donations</p>
                     <div class="progress-circle-mobile">
                         <div class="progress-text"><?php echo $percentage_raised; ?>%</div>
                     </div>
                     <button class="share-button" id="share-button-mobile">Share</button>
                     <?php if ($percentage_raised >= 100): ?>
                         <button class="donate-button" id="donate-now-mobile" disabled style="background:#aaa;cursor:not-allowed;">Fund Completed</button>
                     <?php else: ?>
                         <button class="donate-button" id="donate-now-mobile">Donate now</button>
                     <?php endif; ?>
                </div>

                <div class="fundraiser-details">
                    <h1 class="fundraiser-title-desktop">৳<?php echo number_format($fundraiser['amount_raised'], 2); ?> raised</h1>
                    <?php if ($percentage_raised >= 100): ?>
                        <div style="margin: 16px 0; padding: 10px 18px; background: #e0e0e0; color: #b71c1c; border-radius: 8px; font-weight: bold; font-size: 1.1em; text-align: center;">Fund Completed</div>
                    <?php endif; ?>

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
                        <h2>Update (<span id="update-count">0</span>)</h2>
                        <div id="updates-list"><p>No updates available yet.</p></div>
                    </div>

                    <div class="fundraiser-support">
                        <h2>Words of support (<span id="support-count">0</span>)</h2>
                        <div id="support-list"><p>No words of support yet.</p></div>
                    </div>

                    <div class="button-group-bottom">
                         <button class="share-button" id="share-button-bottom">Share</button>
                         <?php if ($percentage_raised >= 100): ?>
                             <button class="donate-button" id="donate-now-bottom" disabled style="background:#aaa;cursor:not-allowed;">Fund Completed</button>
                         <?php else: ?>
                             <button class="donate-button" id="donate-now-bottom">Donate now</button>
                         <?php endif; ?>
                     </div>
                </div>
            </div>

            <aside class="fundraiser-sidebar">
                <div class="sidebar-summary">
                    <h2>৳<?php echo number_format($fundraiser['amount_raised'], 2); ?> raised</h2>
                    <p class="goal-donations">৳<?php echo number_format($fundraiser['goal'], 0); ?>K goal • <?php echo count($donations); ?> donations</p>
                    <div class="progress-circle">
                        <div class="progress-inner">
                            <div class="progress-text"><?php echo $percentage_raised; ?>%</div>
                            </div>
                         </div>
                </div>

                <div class="sidebar-actions">
                    <button class="share-button" id="share-button-sidebar">Share</button>
                    <?php if ($percentage_raised >= 100): ?>
                        <button class="donate-button" id="donate-now-sidebar" disabled style="background:#aaa;cursor:not-allowed;">Fund Completed</button>
                    <?php else: ?>
                        <button class="donate-button" id="donate-now-sidebar">Donate now</button>
                    <?php endif; ?>
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
                            <label for="donation-amount">Amount (৳)</label>
                            <input type="number" id="donation-amount" name="donation_amount" step="0.01" min="1" required placeholder="Enter amount in Taka">
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
                    <div class="donor-list" id="donor-list">
                        <?php if (count($donations) > 0): ?>
                            <?php foreach ($donations as $donation): ?>
                                <div class="donor-item donor-clickable" data-donor='<?php echo json_encode([
                                    "name" => $donation['donor_name'] ?: 'Anonymous',
                                    "amount" => number_format($donation['amount'], 0),
                                    "comment" => $donation['comment'],
                                    "donated_at" => $donation['donated_at'],
                                ]); ?>'>
                                    <div class="donor-avatar"></div> <div class="donor-info">
                                        <span class="donor-name"><?php echo htmlspecialchars($donation['donor_name'] ?: 'Anonymous'); ?></span>
                                        <span class="donation-amount">৳<?php echo number_format($donation['amount'], 0); ?> • <span class="donation-time" data-time="<?php echo htmlspecialchars($donation['donated_at']); ?>"><?php echo time_elapsed_string($donation['donated_at']); ?></span></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No donations yet.</p>
                        <?php endif; ?>
                    </div>
                    <a href="#" class="see-all-donors" id="see-all-donors">See all</a>
                </div>

                <!-- Modal for all donors -->
                <div id="all-donors-modal" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.45);z-index:9999;align-items:center;justify-content:center;">
                  <div style="background:linear-gradient(135deg,#fff8f2 60%,#ffe3c2 100%);padding:0;border-radius:32px;max-width:440px;width:94vw;box-shadow:0 16px 64px 0 rgba(255,179,102,0.22),0 2px 12px #ffb36633;position:relative;overflow-y:auto;display:flex;flex-direction:column;align-items:center;">
                    <button id="close-donors-modal" style="position:absolute;top:18px;right:22px;font-size:2em;background:none;border:none;cursor:pointer;color:#e67c1a;transition:color 0.2s;z-index:2;">&times;</button>
                    <h2 style="margin-top:36px;margin-bottom:18px;color:#e67c1a;text-align:center;font-size:1.5em;font-weight:700;letter-spacing:0.01em;text-shadow:0 1px 0 #fff,0 2px 8px #ffb36622;">All Donors</h2>
                    <div id="all-donors-list" style="width:100%;padding:0 24px 24px 24px;display:flex;flex-direction:column;gap:14px;">
                      <?php if (count($donations) > 0): ?>
                        <?php foreach ($donations as $donation): ?>
                          <div style="background:linear-gradient(135deg,#fff 60%,#fff3e0 100%);border-radius:16px;box-shadow:0 2px 8px #ffb36622;padding:16px 18px;display:flex;align-items:center;gap:18px;min-height:90px;color:#111;">
                            <div style="width:56px;height:56px;border-radius:50%;background:linear-gradient(135deg,#fff,#ffe3c2 80%);display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px #ffb36633;border:2.5px solid #fff;flex-shrink:0;color:#e67c1a;">
                              <span style="font-size:1.7em;font-weight:bold;">
                                <?php echo ($donation['donor_name'] && $donation['donor_name'] !== 'Anonymous') ? strtoupper(substr($donation['donor_name'],0,1)) : '👤'; ?>
                              </span>
                            </div>
                            <div style="flex:1;display:flex;flex-direction:column;gap:4px;color:#111;">
                              <span style="font-size:1.13em;font-weight:700;letter-spacing:0.01em;">
                                <?php echo htmlspecialchars($donation['donor_name'] ?: 'Anonymous'); ?>
                              </span>
                              <span style="font-size:1.05em;font-weight:600;color:#ff9800;background:#fff3e0;padding:2px 10px;border-radius:10px;box-shadow:0 1px 4px #ffb36622;display:inline-block;width:max-content;">
                                ৳<?php echo number_format($donation['amount'], 0); ?>
                              </span>
                              <span style="color:#444;font-size:0.97em;">
                                <?php echo time_elapsed_string($donation['donated_at']); ?>
                              </span>
                            </div>
                            <div style="font-size:1.03em;color:#b85c00;text-align:left;background:linear-gradient(90deg,#fff3e0 60%,#ffe3c2 100%);padding:8px 12px;border-radius:8px;min-width:80px;max-width:180px;box-shadow:0 1px 6px #ffb36622;font-style:italic;word-break:break-word;">
                              <?php echo !empty($donation['comment']) ? '"' . htmlspecialchars($donation['comment']) . '"' : 'No comment'; ?>
                            </div>
                          </div>
                        <?php endforeach; ?>
                      <?php else: ?>
                        <p>No donations yet.</p>
                      <?php endif; ?>
                    </div>
                  </div>
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

    <!-- Donor Detail Popup -->

    <div id="donor-detail-popup" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.32);z-index:10000;align-items:center;justify-content:center;backdrop-filter:blur(3px);">
      <div style="background:linear-gradient(135deg,#fff8f2 60%,#ffe3c2 100%);padding:0;border-radius:32px;max-width:440px;width:94vw;box-shadow:0 16px 64px 0 rgba(255,179,102,0.22),0 2px 12px #ffb36633;position:relative;overflow:hidden;display:flex;flex-direction:column;align-items:center;color:#111;">
        <div style="width:100%;height:120px;background:linear-gradient(120deg,#ffb366 60%,#ffe3c2 100%);display:flex;align-items:center;justify-content:center;position:relative;color:#fff;">
          <div style="width:92px;height:92px;border-radius:50%;background:linear-gradient(135deg,#fff,#ffe3c2 80%);display:flex;align-items:center;justify-content:center;box-shadow:0 2px 16px #ffb36655;position:absolute;bottom:-46px;left:50%;transform:translateX(-50%);border:5px solid #fff;color:#e67c1a;">
            <span style="font-size:3em;font-weight:bold;" id="popup-donor-avatar">👤</span>
          </div>
          <button id="close-donor-detail" style="position:absolute;top:18px;right:22px;font-size:2em;background:none;border:none;cursor:pointer;color:#fff;transition:color 0.2s;z-index:2;">&times;</button>
        </div>
        <div style="padding:68px 36px 36px 36px;width:100%;display:flex;flex-direction:column;align-items:center;color:#111;">
          <h3 id="popup-donor-name" style="margin:0 0 10px 0;font-size:1.45em;font-weight:700;text-align:center;letter-spacing:0.01em;text-shadow:0 1px 0 #fff,0 2px 8px #ffb36622;">Donor Name</h3>
          <div id="popup-donor-amount" style="font-weight:700;margin-bottom:10px;font-size:1.18em;color:#ff9800;background:#fff3e0;padding:6px 18px;border-radius:16px;box-shadow:0 1px 6px #ffb36622;">৳0</div>
          <div id="popup-donor-time" style="color:#444;font-size:1em;margin-bottom:16px;">Time ago</div>
          <div id="popup-donor-comment" style="font-size:1.13em;color:#b85c00;margin-bottom:8px;text-align:center;background:linear-gradient(90deg,#fff3e0 60%,#ffe3c2 100%);padding:16px 18px;border-radius:14px;min-width:120px;max-width:100%;box-shadow:0 1px 8px #ffb36633;font-style:italic;">No comment</div>
        </div>
      </div>
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

            // Real-time update for donation time-ago
            function updateTimeAgo() {
                const timeElements = document.querySelectorAll('.donation-time');
                timeElements.forEach(function(el) {
                    const donatedAt = el.getAttribute('data-time');
                    if (donatedAt) {
                        el.textContent = timeAgoString(new Date(donatedAt));
                    }
                });
            }
            function timeAgoString(date) {
                const now = new Date();
                const seconds = Math.floor((now - date) / 1000);
                let interval = Math.floor(seconds / 31536000);
                if (interval >= 1) return interval + ' year' + (interval > 1 ? 's' : '') + ' ago';
                interval = Math.floor(seconds / 2592000);
                if (interval >= 1) return interval + ' month' + (interval > 1 ? 's' : '') + ' ago';
                interval = Math.floor(seconds / 604800);
                if (interval >= 1) return interval + ' week' + (interval > 1 ? 's' : '') + ' ago';
                interval = Math.floor(seconds / 86400);
                if (interval >= 1) return interval + ' day' + (interval > 1 ? 's' : '') + ' ago';
                interval = Math.floor(seconds / 3600);
                if (interval >= 1) return interval + ' hour' + (interval > 1 ? 's' : '') + ' ago';
                interval = Math.floor(seconds / 60);
                if (interval >= 1) return interval + ' minute' + (interval > 1 ? 's' : '') + ' ago';
                return 'just now';
            }
            setInterval(updateTimeAgo, 15000); // update every 15 seconds
            updateTimeAgo();

            // See all donors modal
            const seeAllBtn = document.getElementById('see-all-donors');
            const modal = document.getElementById('all-donors-modal');
            const closeModal = document.getElementById('close-donors-modal');
            if (seeAllBtn && modal && closeModal) {
                seeAllBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    modal.style.display = 'flex';
                });
                closeModal.addEventListener('click', function() {
                    modal.style.display = 'none';
                });
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) modal.style.display = 'none';
                });
            }

            // Donor profile popup
            function showDonorPopup(donor) {
                document.getElementById('popup-donor-name').textContent = donor.name || 'Anonymous';
                document.getElementById('popup-donor-amount').textContent = '৳' + donor.amount;
                document.getElementById('popup-donor-time').textContent = timeAgoString(new Date(donor.donated_at));
                // Avatar: use first letter of name or default icon
                var avatar = document.getElementById('popup-donor-avatar');
                if (donor.name && donor.name.trim() !== '' && donor.name !== 'Anonymous') {
                    avatar.textContent = donor.name.trim().charAt(0).toUpperCase();
                } else {
                    avatar.textContent = '👤';
                }
                var commentDiv = document.getElementById('popup-donor-comment');
                if (donor.comment && donor.comment.trim() !== '') {
                    commentDiv.style.display = 'block';
                    commentDiv.textContent = '"' + donor.comment + '"';
                } else {
                    commentDiv.style.display = 'block';
                    commentDiv.textContent = 'No comment';
                }
                document.getElementById('donor-detail-popup').style.display = 'flex';
            }
            // --- Real-time updates for Updates and Words of Support (Demo/Static) ---
            function fetchUpdatesAndSupport() {
                // Simulate AJAX fetch. Replace with real AJAX in production.
                // Example static data:
                const updates = [
                  { text: "We have reached 50% of our goal! Thank you!", time: "2025-07-19 15:00:00" },
                  { text: "Campaign started!", time: "2025-07-18 10:00:00" }
                ];
                const support = [
                  { name: "Ayesha", message: "Praying for your success!", time: "2025-07-20 10:00:00" },
                  { name: "Anonymous", message: "Best wishes!", time: "2025-07-19 18:00:00" }
                ];
                // Render updates
                const updatesList = document.getElementById('updates-list');
                const updateCount = document.getElementById('update-count');
                if (updates.length > 0) {
                  updatesList.innerHTML = updates.map(u => `<div style="background:#fbeee6;padding:10px 14px;margin-bottom:8px;border-radius:8px;font-size:1em;color:#444;"><span style='font-weight:500;'>${u.text}</span><br><span style='color:#888;font-size:0.93em;'>${timeAgoString(new Date(u.time))}</span></div>`).join('');
                } else {
                  updatesList.innerHTML = '<p>No updates available yet.</p>';
                }
                updateCount.textContent = updates.length;
                // Render support
                const supportList = document.getElementById('support-list');
                const supportCount = document.getElementById('support-count');
                if (support.length > 0) {
                  supportList.innerHTML = support.map(s => `<div style="background:#fff3e0;padding:10px 14px;margin-bottom:8px;border-radius:8px;font-size:1em;color:#444;"><span style='font-weight:500;color:#b71c1c;'>${s.name}</span>: <span>${s.message}</span><br><span style='color:#888;font-size:0.93em;'>${timeAgoString(new Date(s.time))}</span></div>`).join('');
                } else {
                  supportList.innerHTML = '<p>No words of support yet.</p>';
                }
                supportCount.textContent = support.length;
            }
            setInterval(fetchUpdatesAndSupport, 15000); // update every 15 seconds
            fetchUpdatesAndSupport();
            function closeDonorPopup() {
                document.getElementById('donor-detail-popup').style.display = 'none';
            }
            document.getElementById('close-donor-detail').addEventListener('click', closeDonorPopup);
            document.getElementById('donor-detail-popup').addEventListener('click', function(e) {
                if (e.target === this) closeDonorPopup();
            });
            function attachDonorClickHandlers() {
                document.querySelectorAll('.donor-clickable').forEach(function(el) {
                    el.addEventListener('click', function(e) {
                        e.stopPropagation();
                        const donor = JSON.parse(this.getAttribute('data-donor'));
                        showDonorPopup(donor);
                    });
                });
            }
            attachDonorClickHandlers();
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
                 // Add click event listener to each donate button
                 donateButtons.forEach(button => {
                     button.addEventListener('click', function() {
                         // Scroll smoothly to the donation form section
                         donationFormSection.scrollIntoView({ behavior: 'smooth' });
                     });
                 });
             }
         });
        // --- End Donate Now Button Functionality ---
    </script>
</body>
</html>
