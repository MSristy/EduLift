<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>FAQ - EdLift</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>

  </style>
    
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>About Us - EdLift</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet"> <!-- AOS Animation -->
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      margin: 0;
      padding: 0;
      background: #f0f2f5;
      color: #333;
    }

    header {
      
      color: green;
      
      text-align: center;
      
    }

    header h1 {
      font-size: 2.5rem;
      margin-bottom: 10px;
      font-family:cursive;
    }

    .search-box {
      margin: 20px auto;
      max-width: 500px;
      display: flex;
      justify-content: center;
    }

    .search-box input {
      width: 100%;
      padding: 12px 15px;
      font-size: 1rem;
      border: 2px solidrgb(19, 78, 17);
      border-radius: 50px;
      outline: none;
      transition: 0.3s;
    }

    .search-box input:focus {
      border-color:rgb(29, 165, 36);
    }

    main {
      max-width: 1000px;
      margin: 20px auto;
      padding: 0 20px;
    }

    .faq-section {
      background: white;
      border-radius: 15px;
      margin-bottom: 20px;
      box-shadow: 0 4px 10px #71be71;
      overflow: hidden;
      transition: 0.4s;
    }

    .faq-question {
      background:rgb(222, 235, 209);
      color: black;
      padding: 18px 20px;
      font-size: 1.2rem;
      cursor: pointer;
      position: relative;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .faq-question:hover {
      background:rgb(213, 253, 217);
    }

    .faq-answer {
      display: none;
      padding: 20px;
      background: #f9f9f9;
      font-size: 1rem;
      color: #555;
    }

    .faq-question i.fa-chevron-down {
      position: absolute;
      right: 20px;
      transition: transform 0.3s;
    }

    .faq-section.active .faq-answer {
      display: block;
    }

    .faq-section.active .faq-question i.fa-chevron-down {
      transform: rotate(180deg);
    }

    footer {
      text-align: center;
      padding: 20px;
      background: #0056b3;
      color: #fff;
      font-size: 0.9rem;
      margin-top: 40px;
    }

    @media (max-width: 600px) {
      header h1 {
        font-size: 2rem;
      }

      .faq-question {
        font-size: 1rem;
      }
    }
  </style>
</head>
<body>

<?php include('templates/header1.php'); ?>
<?php include('templates/header2.php'); ?>


<header data-aos="fade-down">
  <h1>Frequently Asked Questions</h1>
  <p>Your doubts answered — fast and easy!</p>
</header>

<div class="search-box" data-aos="fade-up">
  <input type="text" id="searchInput" placeholder="Search your question...">
</div>

<main id="faqContainer">
  <?php
    $conn = new mysqli('localhost', 'root', '', 'loan');
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT question, answer FROM faq";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        echo '<div class="faq-section" data-aos="fade-up">';
        echo '<div class="faq-question">';
        echo '<i class="fas fa-question-circle"></i> ' . htmlspecialchars($row['question']);
        echo '<i class="fas fa-chevron-down"></i>';
        echo '</div>';
        echo '<div class="faq-answer">' . nl2br(htmlspecialchars($row['answer'])) . '</div>';
        echo '</div>';
      }
    } else {
      echo '<p>No FAQs available right now.</p>';
    }

    $conn->close();
  ?>
</main>


<!-- External JS Libraries -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script> <!-- AOS Animation -->
    <script>
    AOS.init({
        duration: 800,
        easing: 'ease-in-out',
    });

    // Toggle FAQ Answer
    const faqs = document.querySelectorAll('.faq-section');
    faqs.forEach(faq => {
        faq.querySelector('.faq-question').addEventListener('click', () => {
        faq.classList.toggle('active');
        });
    });

    // Updated Live Search with fallback
    const searchInput = document.getElementById('searchInput');
    const faqContainer = document.getElementById('faqContainer');

    searchInput.addEventListener('input', function() {
        const filter = searchInput.value.toLowerCase();
        let found = false;

        faqs.forEach(faq => {
        const questionText = faq.querySelector('.faq-question').innerText.toLowerCase();
        const answerText = faq.querySelector('.faq-answer').innerText.toLowerCase();
        
        if (questionText.includes(filter) || answerText.includes(filter)) {
            faq.style.display = "";
            found = true;
        } else {
            faq.style.display = "none";
        }
        });

        // If not found any matching FAQ
        let autoReply = document.getElementById('autoReply');
        if (!found && filter.length > 0) {
        if (!autoReply) {
            autoReply = document.createElement('div');
            autoReply.id = 'autoReply';
            autoReply.className = 'faq-section active'; // style same as others
            autoReply.innerHTML = `
            <div class="faq-question">
                <i class="fas fa-info-circle"></i> Oops! No exact match found.
            </div>
            <div class="faq-answer">
                Sorry, we couldn’t find an exact answer for "<strong>${searchInput.value}</strong>". <br><br>
                Please contact our support team for more help!
            </div>
            `;
            faqContainer.appendChild(autoReply);
        } else {
            autoReply.querySelector('.faq-answer').innerHTML = `
            Sorry, we couldn’t find an exact answer for "<strong>${searchInput.value}</strong>". <br><br>
            Please contact our support team for more help!
            `;
            autoReply.style.display = "";
        }
        } else {
        if (autoReply) {
            autoReply.style.display = "none";
        }
        }
    });
    </script>

    


<?php include('templates/footer.php'); ?>

</body>
</html>
