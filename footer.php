<html>
    <head>
        <style>
        footer {
            background: #222;
            color: #fff;
            padding: 50px 20px 30px;
            margin-top: 80px;
            border-top: 1px solid #444;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-bottom: 30px;
        }

        .footer-section h3 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #fff;
        }

        .footer-section p {
            font-size: 14px;
            line-height: 1.6;
            color: #ccc;
        }

        .footer-links {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-links li {
            margin-bottom: 10px;
        }

        .footer-links a {
            color: #ccc;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s ease;
        }

        .footer-links a:hover {
            color: #667eea;
        }

        .footer-bottom {
            border-top: 1px solid #444;
            padding-top: 20px;
            text-align: center;
            color: #999;
            font-size: 13px;
        }

        .developer-credit {
            color: #ccc;
            margin-bottom: 10px;
        }

        .developer-credit a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }

        .developer-credit a:hover {
            text-decoration: underline;
        }

        .social-links {
            margin-top: 15px;
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .social-links a {
            color: #ccc;
            text-decoration: none;
            font-size: 12px;
            padding: 5px 10px;
            border: 1px solid #444;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .social-links a:hover {
            color: #667eea;
            border-color: #667eea;
        }

        @media (max-width: 768px) {
            .footer-content {
                grid-template-columns: 1fr;
                gap: 30px;
            }

            footer {
                padding: 40px 20px 25px;
            }
        }
        </style>
    </head>
    <body>
        
    <footer>
        <div class="footer-container">
            <div class="footer-content">
                <!-- About Section -->
                <div class="footer-section">
                    <h3>About wazoForum</h3>
                    <p>wazoForum is a vibrant community discussion platform dedicated to fostering meaningful conversations and knowledge sharing among users from diverse backgrounds.</p>
                </div>

                <!-- Quick Links -->
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="categories.php">Categories</a></li>
                        <li><a href="posts.php">All Posts</a></li>
                        <li><a href="contact.php">Contact Us</a></li>
                    </ul>
                </div>

                <!-- Legal & Support -->
                <div class="footer-section">
                    <h3>Legal & Support</h3>
                    <ul class="footer-links">
                        <li><a href="privacy.php">Privacy Policy</a></li>
                        <li><a href="terms.php">Terms of Service</a></li>
                        <li><a href="faq.php">FAQ</a></li>
                        <li><a href="support.php">Support</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div class="footer-section">
                    <h3>Get In Touch</h3>
                    <p>Have questions? We'd love to hear from you.</p>
                    <p style="margin-top: 10px;">Email: <a href="mailto:info@wazoforum.com" style="color: #667eea;">info@wazoforum.com</a></p>
                    <p>Phone: <a href="tel:+255..." style="color: #667eea;">+255 626 727 009</a></p>
                </div>
            </div>

            <div class="footer-bottom">
                <div class="developer-credit">
                    Developed<span style="color: #e74c3c;"></span> by <a href="#" title="Developer">Aniceth Leonce Charles</a>
                </div>
    
                <p style="margin-top: 20px;">&copy; 2026 wazoForum. All Rights Reserved. Building Communities Together.</p>
            </div>
        </div>
    </footer>

    </body>
</html>