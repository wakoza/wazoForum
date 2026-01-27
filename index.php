<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Forum | Community Platform</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
        }

       
        /* ===== HERO SECTION ===== */
        .hero {
            background: linear-gradient(135deg, rgba(0, 102, 204, 0.7), rgba(0, 51, 153, 0.7)), 
                        url('pictures/background.jpeg') center/cover;
            color: white;
            padding: 120px 20px;
            text-align: center;
            position: relative;
            min-height: 600px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .hero h1 {
            font-size: 56px;
            font-weight: 700;
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .hero p {
            font-size: 20px;
            margin-bottom: 40px;
            opacity: 0.95;
        }

        .search-box {
            display: flex;
            gap: 10px;
            max-width: 500px;
            margin: 0 auto;
            margin-bottom: 30px;
        }

        .search-box button {
            padding: 14px 30px;
            background: #0066cc;
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }

        .search-box button:hover {
            background: #0052a3;
        }

        /* ===== FEATURES SECTION ===== */
        .features {
            max-width: 1200px;
            margin: 80px auto;
            padding: 0 20px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-bottom: 80px;
        }

        .feature-card {
            text-align: center;
            padding: 30px;
        }

        .feature-icon {
            font-size: 48px;
            color: #0066cc;
            margin-bottom: 20px;
        }

        .feature-card h3 {
            font-size: 20px;
            margin-bottom: 12px;
            font-weight: 600;
        }

        .feature-card p {
            color: #666;
            line-height: 1.6;
        }

        /* ===== COMPANY SECTION ===== */
        .company-section {
            background: white;
            padding: 80px 20px;
            margin-top: 60px;
        }

        .company-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
        }

        .company-text h2 {
            font-size: 42px;
            margin-bottom: 20px;
            line-height: 1.3;
        }

        .company-text h2 span {
            color: #0066cc;
        }

        .company-text p {
            color: #666;
            line-height: 1.8;
            margin-bottom: 20px;
            font-size: 16px;
        }

        .company-btn {
            display: inline-block;
            background: #0066cc;
            color: white;
            padding: 12px 30px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.3s;
            margin-top: 20px;
        }

        .company-btn:hover {
            background: #0052a3;
        }

        .company-images {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .company-images img {
            width: 100%;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .company-images .large-img {
            grid-column: 1 / -1;
        }

    

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 36px;
            }

            .search-box {
                flex-direction: column;
            }

            .company-content {
                grid-template-columns: 1fr;
            }

            .nav-links {
                gap: 15px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
<?php include("header.php"); ?>
    <!-- HERO SECTION -->
    <section class="hero">
        <h1>Responsive Design<br>for Community Forums</h1>
        <p>Connect, discuss, and share knowledge with our community</p>
        <div class="search-box">
            <button>Join Discussion</button>
        </div>
    </section>

    <!-- FEATURES SECTION -->
    <section class="features">
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-comments"></i></div>
                <h3>Discussion Boards</h3>
                <p>Create and participate in topic-based discussions with thousands of community members.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-lightbulb"></i></div>
                <h3>Ask & Answer</h3>
                <p>Get expert help by asking questions and sharing valuable solutions with the community.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-globe"></i></div>
                <h3>Global Network</h3>
                <p>Connect with users from around the world and expand your professional network.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-user-circle"></i></div>
                <h3>User Profiles</h3>
                <p>Build your reputation and track your contributions across the platform.</p>
            </div>
        </div>
    </section>

    <!-- COMPANY SECTION -->
    <section class="company-section">
        <div class="company-content">
            <div class="company-text">
                <h2>We've been building <span>community</span><br>for <span>many years.</span></h2>
                <p>Our platform has been trusted by thousands of users to find answers, share expertise, and build meaningful connections. We're committed to providing a safe, welcoming space for everyone.</p>
                <p>Join our growing community and discover why millions of people choose to engage with us every day. Your voice matters and your contributions help others.</p>
                <a href="register.php" class="company-btn">Join our community</a>
            </div>
            <div class="company-images">
                <img src="pictures/background.jpeg" alt="Community Discussion">
                <img src="pictures/background2.jpeg" alt="Team Collaboration">
                <img src="pictures/background.jpeg" alt="Professional Network" class="large-img">
            </div>
        </div>
    </section>
    <?php 
    include("footer.php");
    ?>
</body>
</html>
