<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue sur Waste2Product</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }
        .logo {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .welcome-title {
            font-size: 28px;
            margin: 20px 0;
            font-weight: 300;
        }
        .content {
            padding: 40px 30px;
            color: #333;
        }
        .greeting {
            font-size: 24px;
            color: #28a745;
            margin-bottom: 20px;
        }
        .message {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .features {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 15px;
            margin: 30px 0;
        }
        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            font-size: 16px;
        }
        .feature-icon {
            color: #28a745;
            margin-right: 15px;
            font-size: 20px;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 50px;
            font-weight: bold;
            font-size: 18px;
            margin: 20px 0;
            transition: transform 0.3s ease;
        }
        .cta-button:hover {
            transform: translateY(-2px);
        }
        .footer {
            background: #2c3e50;
            color: white;
            padding: 30px;
            text-align: center;
        }
        .social-links {
            margin: 20px 0;
        }
        .social-links a {
            color: white;
            text-decoration: none;
            margin: 0 10px;
            font-size: 20px;
        }
        .footer-text {
            font-size: 14px;
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <div class="logo">🌱 Waste2Product</div>
            <div class="welcome-title">Welcome to our community!</div>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">Hello {{ $user->first_name }}! 👋</div>
            
            <div class="message">
                <p>Congratulations! Your account has been successfully created on <strong>Waste2Product</strong>.</p>
                <p>You are now part of a community committed to transforming waste into useful products and contributing to a more sustainable future.</p>
            </div>

            <div class="features">
                <h3 style="color: #28a745; margin-top: 0;">🎯 What you can do now:</h3>
                <div class="feature-item">
                    <span class="feature-icon">♻️</span>
                    <span>Discover our recycled products</span>
                </div>
                <div class="feature-item">
                    <span class="feature-icon">🛒</span>
                    <span>Make responsible purchases</span>
                </div>
                <div class="feature-item">
                    <span class="feature-icon">💡</span>
                    <span>Learn eco-friendly tips</span>
                </div>
                <div class="feature-item">
                    <span class="feature-icon">🤝</span>
                    <span>Join our community</span>
                </div>
            </div>

            <div style="text-align: center;">
                <a href="{{ url('/') }}" class="cta-button">Start the adventure 🌟</a>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="social-links">
                <a href="#">📘 Facebook</a>
                <a href="#">📷 Instagram</a>
                <a href="#">🐦 Twitter</a>
                <a href="#">💼 LinkedIn</a>
            </div>
            <div class="footer-text">
                <p>Thank you for trusting us for your transition to a more sustainable lifestyle!</p>
                <p>The Waste2Product Team 🌱</p>
                <p style="margin-top: 20px; font-size: 12px;">
                    If you did not create this account, you can ignore this email.
                </p>
            </div>
        </div>
    </div>
</body>
</html>