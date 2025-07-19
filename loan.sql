-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 01, 2025 at 08:42 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `loan`
--

-- --------------------------------------------------------

--
-- Table structure for table `accepted_loans`
--

CREATE TABLE `accepted_loans` (
  `id` int(11) NOT NULL,
  `lender_offer_id` int(11) DEFAULT NULL,
  `borrower_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accepted_loans`
--

INSERT INTO `accepted_loans` (`id`, `lender_offer_id`, `borrower_id`, `created_at`) VALUES
(1, 2, 1, '2025-06-20 17:27:56'),
(2, 2, 1, '2025-06-20 17:35:40'),
(3, 2, 1, '2025-06-20 17:38:26'),
(4, 2, 12, '2025-06-20 17:51:06'),
(5, 3, 11, '2025-06-20 18:01:36'),
(6, 5, 13, '2025-06-24 17:59:59'),
(7, 6, 13, '2025-06-24 18:01:44'),
(8, 6, 13, '2025-06-24 18:02:02'),
(9, 7, 14, '2025-06-30 13:51:38'),
(10, 8, 18, '2025-06-30 19:34:19'),
(11, 10, 16, '2025-07-01 06:17:49'),
(12, 11, 21, '2025-07-01 06:22:55');

-- --------------------------------------------------------

--
-- Table structure for table `borrower_posts`
--

CREATE TABLE `borrower_posts` (
  `id` int(11) NOT NULL,
  `student_name` varchar(255) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `loan_amount` decimal(10,2) NOT NULL,
  `loan_purpose` varchar(255) NOT NULL,
  `loan_description` text NOT NULL,
  `financial_wellbeing` enum('Excellent','Very Good','Okay','Not Good') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `loan_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrower_posts`
--

INSERT INTO `borrower_posts` (`id`, `student_name`, `student_id`, `loan_amount`, `loan_purpose`, `loan_description`, `financial_wellbeing`, `created_at`, `loan_image`) VALUES
(15, 'Md Al mahfuz  Chowdhury', '011221482', 10000.00, 'Living Expenses', 'for food cart', 'Not Good', '2025-06-30 14:33:37', '6862a04152f06_1641896788_212877.png'),
(16, 'Md Al mahfuz  Chowdhury', '011221482', 1000.00, 'Tuition Fees', 'no money', 'Not Good', '2025-06-30 15:39:27', '6862afafcde40_campus.jpg'),
(18, 'Mahmuda Akter Sristy', '011221447', 5000.00, 'Others', 'I need a loan to help launch my  food cart setup.', 'Not Good', '2025-06-30 17:14:47', '6862c607d29ff_food3.png'),
(19, 'Saiful Islam', '011213076', 9000.00, 'Living Expenses', 'I need a loan to cover my living expenses and manage daily necessities', 'Not Good', '2025-06-30 17:20:17', '6862c751e94a1_room1.jpeg'),
(20, 'Md Zawadul Aman Hredoy', '011221103', 15000.00, 'Books and Supplies', 'i need some money for managing my bookshop setup', 'Not Good', '2025-07-01 04:11:35', '68635ff79bc88_book1.jpg'),
(21, 'Saiful Islam', '011213076', 8000.00, 'Others', 'For my mothers medical bill I need that much money', 'Okay', '2025-07-01 04:22:07', '6863626f411b0_medical2.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `borrower_signup`
--

CREATE TABLE `borrower_signup` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `university_email` varchar(150) NOT NULL,
  `dob` date NOT NULL,
  `parent_number` varchar(20) NOT NULL,
  `city` varchar(50) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `nid` varchar(30) NOT NULL,
  `personal_email` varchar(150) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `profile_img` varchar(255) NOT NULL,
  `student_id_card_img` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrower_signup`
--

INSERT INTO `borrower_signup` (`id`, `first_name`, `last_name`, `student_id`, `university_email`, `dob`, `parent_number`, `city`, `gender`, `nid`, `personal_email`, `mobile`, `profile_img`, `student_id_card_img`, `password`, `created_at`) VALUES
(7, 'Md Al mahfuz', ' Chowdhury', '011221482', 'm221482@uiu.ac.bd', '2001-01-11', '01720997741', 'Dhaka', 'Male', '9112121211', 'siam@gmail.com', '01745531722', 'WhatsApp Image 2025-06-30 at 8.18.31 PM.jpeg', 'WhatsApp Image 2025-06-30 at 8.09.36 PM (1).jpeg', '$2y$10$VbmFdHZx2SxI8zYCme0DWOAc6WbKxNhTwshJM01ObEK7LdtqDfsSC', '2025-06-30 14:27:16'),
(8, 'Mahmuda Akter', 'Sristy', '011221447', 'sristy447@uiu.ac.bd', '2001-02-21', '01720997740', 'Dhaka', 'Female', '9112121210', '447@gmail.com', '01569108045', 'sristy.jpeg', 'sristy id card.jpeg', '$2y$10$wjYiUPNd0PmJuLBivQ087.tM/6.7OZ6Qa5NaCVslxt5M0TAzsUTjW', '2025-06-30 16:24:05'),
(9, 'Saiful', 'Islam', '011213076', 'saiful076@uiu.ac.bd', '2002-07-30', '01720997746', 'Dhaka', 'Male', '9143434334', '076@gmail.com', '01720997721', 'saiful.jpeg', 'WhatsApp Image 2025-06-30 at 10.58.06 PM.jpeg', '$2y$10$tOA0SCsHmKJlG8olJkZqBuVArBLZUy9McQJqSozUQW8ekmuYyqp6u', '2025-06-30 17:02:16'),
(10, 'Md Zawadul Aman', 'Hredoy', '011221103', 'saiful@uiu.ac.bd', '2001-05-01', '01720997733', 'Dhaka', 'Male', '9112121266', '103@gmail.com', '01720997766', 'hridoy.jpeg', 'hridoy id.jpeg', '$2y$10$YTc/bkTFwtVrfHo3VhJoUuf35khxVL0oHjWF1TFEF/1GPEoBG7Ayq', '2025-06-30 17:07:22');

-- --------------------------------------------------------

--
-- Table structure for table `contact_info`
--

CREATE TABLE `contact_info` (
  `id` int(11) NOT NULL,
  `person_id` int(11) NOT NULL,
  `contact_type` enum('phone','email','facebook') NOT NULL,
  `contact_value` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_info`
--

INSERT INTO `contact_info` (`id`, `person_id`, `contact_type`, `contact_value`, `created_at`) VALUES
(1, 1, 'phone', '01569108045', '2025-04-27 17:36:22'),
(2, 1, 'email', 'msristy221447@bscse.uiu.ac.bd', '2025-04-27 17:36:22'),
(3, 1, 'facebook', 'https://www.facebook.com/mahmudaakter.sristy', '2025-04-27 17:36:22'),
(4, 2, 'phone', '01745531727', '2025-04-27 17:36:22'),
(5, 2, 'email', 'mdalmahfuzchowdhury@gmail.com', '2025-04-27 17:36:22'),
(6, 2, 'facebook', 'https://www.facebook.com/siam.mahfuz.7', '2025-04-27 17:36:22');

-- --------------------------------------------------------

--
-- Table structure for table `donations`
--

CREATE TABLE `donations` (
  `id` int(11) NOT NULL,
  `fundraiser_id` int(11) DEFAULT NULL,
  `donor_name` varchar(255) DEFAULT NULL,
  `donor_email` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `comment` text DEFAULT NULL,
  `donated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `donations`
--

INSERT INTO `donations` (`id`, `fundraiser_id`, `donor_name`, `donor_email`, `amount`, `comment`, `donated_at`) VALUES
(8, 14, 'adi', NULL, 2000.00, 'nothing', '2025-07-01 05:43:59');

-- --------------------------------------------------------

--
-- Table structure for table `faq`
--

CREATE TABLE `faq` (
  `id` int(11) NOT NULL,
  `question` varchar(255) NOT NULL,
  `answer` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faq`
--

INSERT INTO `faq` (`id`, `question`, `answer`) VALUES
(1, 'What is EdLift?', 'EdLift is a platform that helps UIU students get educational funding through loans and crowdfunding.'),
(2, 'How can I apply for a loan?', 'Simply register on our platform, fill out your loan application, and submit the required documents.'),
(3, 'Who can contribute?', 'Anyone — alumni, individuals, organizations — can donate or lend to students.'),
(4, 'Is there any interest on loans?', 'We offer flexible loan terms with minimal or zero interest depending on the program.'),
(5, 'How can I post a loan request on EdLift?', 'First, create an account on EdLift. After logging in, go to the \"Post a Loan Request\" section, fill out your loan details like amount, reason, and deadline, and submit. Our team will verify and publish it.'),
(6, 'How do I pay back the loan I received?', 'Once your campaign is funded, you will get a repayment schedule based on your agreed terms. You need to follow the installment dates and pay through our secure payment system on your dashboard.'),
(7, 'How does crowdfunding work at EdLift?', 'Crowdfunding at EdLift allows multiple lenders to contribute smaller amounts to fulfill a borrower\'s full loan request. This makes it easier and faster to get funded!'),
(8, 'Is my data and transaction secure on EdLift?', 'Absolutely! EdLift uses end-to-end encryption and secure payment gateways to ensure your personal and financial information stays protected.'),
(9, 'What is the interest rate for loans?', 'Interest rates depend on your loan profile, the amount you request, and your repayment period. All terms will be clearly shown before you accept any funding.'),
(10, 'Can I cancel my loan request?', 'Yes, you can cancel a loan request before it gets fully funded. After cancellation, no money will be deducted or processed.'),
(11, 'Who can lend money on EdLift?', 'Anyone with a valid bank account and identity verification can become a lender on EdLift. Lenders help students achieve their dreams while earning a return on their contribution.'),
(12, 'What happens if I fail to repay on time?', 'Late payments may incur penalties. We strongly encourage borrowers to communicate with us early if they anticipate any difficulties, so that we can assist you.'),
(13, 'Is there a minimum or maximum amount I can borrow?', 'Yes, typically the minimum borrowing amount is ৳5,000 and the maximum is ৳2,00,000. Exact limits depend on your profile and history with EdLift.'),
(14, 'How long does it take to get funded?', 'It varies. Some students get funded within days, while others may take a few weeks. Making a compelling and clear loan request helps you get funded faster!');

-- --------------------------------------------------------

--
-- Table structure for table `fundraisers`
--

CREATE TABLE `fundraisers` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `goal` decimal(10,2) NOT NULL,
  `description` text NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `organizer_name` varchar(255) DEFAULT NULL,
  `organizer_email` varchar(255) DEFAULT NULL,
  `amount_raised` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `fundraisers`
--

INSERT INTO `fundraisers` (`id`, `title`, `goal`, `description`, `category`, `image_path`, `organizer_name`, `organizer_email`, `amount_raised`, `created_at`) VALUES
(7, 'Heal With Hope: A Medical Fundraising Campaign', 10000.00, 'Join us in the &quot;Heal With Hope&quot; campaign to raise essential funds for urgent medical treatment and care. Your support can bring relief, restore health, and offer a second chance to those facing critical health challenges. Every contribution counts—let’s make healing possible, together', 'medical', 'images/medical1.jpg', 'Siam mahfuz', 'mahfuz@gmail.com', 0.00, '2025-06-30 18:49:18'),
(10, 'Bright Futures: Education Fundraiser Campaign&quot;', 11000.00, 'The &quot;Bright Futures&quot; campaign aims to raise funds to support education for underprivileged students, providing scholarships, learning materials, and opportunities for a better tomorrow. Your generous contribution can help unlock potential and empower dreams through education.', 'education', 'images/education2.jpg', 'Saiful', 'saiful@gmail.com', 0.00, '2025-06-30 18:59:19'),
(11, 'A Memorial Fundraising Campaign', 25000.00, 'The &quot;In Loving Memory&quot; campaign is dedicated to honoring the life of a cherished soul by supporting funeral expenses and creating a lasting tribute. Your contribution helps ease the financial burden on the family during this difficult time and ensures their loved one is remembered with dignity and grace.', 'memorial', 'images/memory1.jpg', 'Hridoy', 'hridoy@gmail.com', 0.00, '2025-06-30 19:03:54'),
(14, 'Urgent Aid: Emergency Relief Fundraiser&quot;', 20000.00, 'The &quot;Urgent Aid&quot; campaign is launched to raise funds for immediate blood transfusions needed in a critical medical emergency. Your quick support can help secure lifesaving treatment, cover hospital costs, and bring hope to a patient in urgent need. Every second counts—stand with us to save a life.', 'emergency', 'uploads/6862e343616d1.jpg', 'Zerin', 'zeri@gmail.com', 2000.00, '2025-06-30 19:19:31'),
(15, 'Medical Campaign: &quot;Healing Hands, Hopeful Hearts&quot;', 20000.00, 'Support patients facing critical health challenges. Your donation can help cover the cost of surgeries, treatments, and life-saving medications for those who can\'t afford them.', 'medical', 'uploads/68636b168669d.jpg', 'Shahanur', 'sh@gmail.com', 0.00, '2025-07-01 04:59:02'),
(16, 'Memorial Campaign: &quot;In Loving Memory&quot;', 13000.00, 'Honor the life of a loved one by helping others. This campaign collects donations to support a cause they cared about, creating a lasting legacy of kindness and impact.', 'memorial', 'uploads/68636bcb37094.jpeg', 'Bappy', 'bappy@gmail.com', 0.00, '2025-07-01 05:02:03'),
(17, 'Emergency Campaign: &quot;Act Now, Save Lives&quot;', 30000.00, 'In moments of crisis, every second counts. This emergency relief fund provides immediate support for urgent needs—whether it\'s a natural disaster, accident, or medical emergency.', 'emergency', 'images/er3.jpg', 'Adnan', 'ad@gmail.com', 0.00, '2025-07-01 05:03:11'),
(18, 'Education Campaign: &quot;Fuel Their Future&quot;', 25000.00, 'Help a student overcome financial barriers to education. Your support covers tuition, books, and basic needs, empowering learners to chase their dreams.', 'education', 'uploads/68636d38055a4.jpg', 'Khushbu', 'kh@gmail.com', 0.00, '2025-07-01 05:08:08'),
(20, 'others issue', 2000.00, 'jhakanaka', 'other', 'uploads/6863725753a0d.png', 'Ami', 'ami@gmail.com', 0.00, '2025-07-01 05:29:59'),
(21, 'jani na', 3500.00, 'aaaaaaaaaaa', 'other', 'uploads/6863730317332.jpeg', 'Ami', 'ami@gmail.com', 0.00, '2025-07-01 05:32:51');

-- --------------------------------------------------------

--
-- Table structure for table `lenders_signup`
--

CREATE TABLE `lenders_signup` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `student_id` varchar(50) DEFAULT NULL,
  `versity_email` varchar(100) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `profile_img` varchar(255) DEFAULT NULL,
  `about` text DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `registered_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lenders_signup`
--

INSERT INTO `lenders_signup` (`id`, `first_name`, `student_id`, `versity_email`, `mobile`, `profile_img`, `about`, `password`, `registered_at`) VALUES
(1, 'aaaa', '447', '447@gmail.com', '01926355452', 'dowd.jpeg', 'fdffffffffff', '$2y$10$Y.fkPzIb/CzSZElC9zbiWuR52nHdi.sjeVdpYztaFv3MrnPMhc8Ea', '2025-04-27 15:57:02'),
(2, 'Mahmuda Sristy', '011221480', '221447@gmail.com', '01569108045', 'md2.png', 'I have nothing to say', '$2y$10$qHKU/DPcBIDSZSslQPMN5esMwQAi5ZpJkvI9J.4ki6AepsyxsGmv6', '2025-05-23 18:04:07'),
(3, 'Akter', '011221447', '47@gmail.com', '01926355452', 'md2.png', 'hgfjhg', '$2y$10$z/6YZnkVVjd9JTtcGdcGj.saV68C4rJs5SPme2zGLvulTE1AUoMUW', '2025-05-23 18:21:17'),
(4, 'Tuniiii', '011221482', '221448@gmail.com', '01569108045', 'md2.png', 'frdgfdyhfj', '$2y$10$c3Zo1WUzhCn8hsc0SJ5NsODKqxhlvoMGE7P4Ernb6Ekz5N2I74WT2', '2025-05-23 18:38:01'),
(5, 'bbbbbcccc', '10', '440@gmail.com', '01926355450', 'maria.jpg', 'baaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', '$2y$10$tjlKFLpOEeEbUJ5vgexlQeRK4TClVVs7eg0l4nlpaJu/KPbBjz1ru', '2025-06-19 16:11:21'),
(6, 'zawad Saiful', '011221444', 'msristy444@uiu.ac.bd', '01926355452', 'dowd.jpeg', 'vvvvvvvvv', '$2y$10$9l31VoUJH69wA6At.r3wn.i33T08lglpbPC/Ox/5WaTZVW7MvnfUG', '2025-06-24 17:48:07'),
(7, 'Shahanaz parvin', '011221666', '447@gmail.com', '01720997741', '686236ee41f3a_campus.jpg', 'yghgfhj', '$2y$10$/1ZjKgrczZSgR/qwi6jy2.tADJFRo9kz9EjLztIaADfR.Sa3Eqc6S', '2025-06-30 13:49:06'),
(8, 'Raju Ahmed', '011221212', '212@gmail.com', '01720996690', 'lender (2).jpeg', 'I am in my final semester, and I am offering loans only for valid and clearly stated purposes.', '$2y$10$a6bXsc8RRuGN4YPL8tX9.eS8NPvMOJri7dIUG60hur/XSrtl8jnIi', '2025-06-30 18:39:18'),
(9, 'Ahnaf', '011221321', '321@uiu.ac.bd', '01720997733', 'lender3.jpeg', 'I lend money to support students with genuine needs and responsible, goal-driven purposes', '$2y$10$NvEbS5s0xQrejiMX7KZ7aef8rnNQRj6RsfjXCCeDJWeaON/1IPqMO', '2025-06-30 18:43:08');

-- --------------------------------------------------------

--
-- Table structure for table `lender_lend`
--

CREATE TABLE `lender_lend` (
  `id` int(11) NOT NULL,
  `lender_id` int(11) NOT NULL,
  `borrower_id` int(11) DEFAULT NULL,
  `full_name` varchar(100) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `versity_email` varchar(100) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `interest_rate` decimal(5,2) NOT NULL,
  `duration` enum('monthly','yearly','end') NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lender_lend`
--

INSERT INTO `lender_lend` (`id`, `lender_id`, `borrower_id`, `full_name`, `student_id`, `versity_email`, `mobile`, `interest_rate`, `duration`, `description`, `created_at`) VALUES
(4, 5, 11, 'bbbbbcccc', '10', '440@gmail.com', '01926355450', 12.00, 'end', 'fffffffffffffrrrrrrrrrrrrrrrrrrrrrr', '2025-06-20 18:02:47'),
(6, 6, 13, 'zawad Saiful', '011221444', 'msristy444@uiu.ac.bd', '01926355452', 12.00, 'monthly', 'ggggg', '2025-06-24 18:01:37'),
(7, 7, 14, 'Shahanaz parvin', '011221666', '447@gmail.com', '01720997741', 12.00, 'monthly', 'dfdfd', '2025-06-30 13:51:24'),
(8, 8, 18, 'Raju Ahmed', '011221212', '212@gmail.com', '01720996690', 6.00, 'end', 'I want the money at the end of the duration', '2025-06-30 19:32:52'),
(9, 9, 21, 'Ahnaf', '011221321', '321@uiu.ac.bd', '01720997733', 3.00, 'monthly', 'Back the money on time', '2025-07-01 06:10:59'),
(10, 9, 16, 'Ahnaf', '011221321', '321@uiu.ac.bd', '01720997733', 4.00, 'monthly', 'i want the money monthly with interest rate 4%', '2025-07-01 06:17:21'),
(11, 8, 21, 'Raju Ahmed', '011221212', '212@gmail.com', '01720996690', 5.00, 'end', 'back money on the time', '2025-07-01 06:22:01');

-- --------------------------------------------------------

--
-- Table structure for table `loan_offers`
--

CREATE TABLE `loan_offers` (
  `id` int(11) NOT NULL,
  `lender_name` varchar(100) NOT NULL,
  `interest_rate` decimal(5,2) NOT NULL,
  `payable_system` varchar(50) NOT NULL,
  `loan_duration` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `persons`
--

CREATE TABLE `persons` (
  `person_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `persons`
--

INSERT INTO `persons` (`person_id`, `name`, `image_url`) VALUES
(1, 'Mahmuda Akter Sristy', 'maria.jpg'),
(2, 'Md Al Mahfuz Chowdhury', 'maria.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accepted_loans`
--
ALTER TABLE `accepted_loans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `borrower_posts`
--
ALTER TABLE `borrower_posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `borrower_signup`
--
ALTER TABLE `borrower_signup`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`),
  ADD UNIQUE KEY `university_email` (`university_email`);

--
-- Indexes for table `contact_info`
--
ALTER TABLE `contact_info`
  ADD PRIMARY KEY (`id`),
  ADD KEY `person_id` (`person_id`);

--
-- Indexes for table `donations`
--
ALTER TABLE `donations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fundraiser_id` (`fundraiser_id`);

--
-- Indexes for table `faq`
--
ALTER TABLE `faq`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fundraisers`
--
ALTER TABLE `fundraisers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lenders_signup`
--
ALTER TABLE `lenders_signup`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lender_lend`
--
ALTER TABLE `lender_lend`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lender_id` (`lender_id`);

--
-- Indexes for table `loan_offers`
--
ALTER TABLE `loan_offers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `persons`
--
ALTER TABLE `persons`
  ADD PRIMARY KEY (`person_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accepted_loans`
--
ALTER TABLE `accepted_loans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `borrower_posts`
--
ALTER TABLE `borrower_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `borrower_signup`
--
ALTER TABLE `borrower_signup`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `contact_info`
--
ALTER TABLE `contact_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `donations`
--
ALTER TABLE `donations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `faq`
--
ALTER TABLE `faq`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `fundraisers`
--
ALTER TABLE `fundraisers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `lenders_signup`
--
ALTER TABLE `lenders_signup`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `lender_lend`
--
ALTER TABLE `lender_lend`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `loan_offers`
--
ALTER TABLE `loan_offers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `persons`
--
ALTER TABLE `persons`
  MODIFY `person_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `contact_info`
--
ALTER TABLE `contact_info`
  ADD CONSTRAINT `contact_info_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `persons` (`person_id`) ON DELETE CASCADE;

--
-- Constraints for table `donations`
--
ALTER TABLE `donations`
  ADD CONSTRAINT `donations_ibfk_1` FOREIGN KEY (`fundraiser_id`) REFERENCES `fundraisers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lender_lend`
--
ALTER TABLE `lender_lend`
  ADD CONSTRAINT `lender_lend_ibfk_1` FOREIGN KEY (`lender_id`) REFERENCES `lenders_signup` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
