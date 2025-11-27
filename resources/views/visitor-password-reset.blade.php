<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password - Happy Hostel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 10px;
        }
        .title {
            font-size: 24px;
            color: #1f2937;
            margin-bottom: 20px;
        }
        .content {
            margin-bottom: 30px;
        }
        .button {
            display: inline-block;
            background-color: #2563eb;
            color: #ffffff;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
        }
        .button:hover {
            background-color: #1d4ed8;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 14px;
            color: #6b7280;
        }
        .warning {
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .contact-info {
            background-color: #f9fafb;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Happy Hostel</div>
            <h1 class="title">Reset Your Password</h1>
        </div>

        <div class="content">
            <p>Hello {{ $visitor->name }},</p>
            
            <p>We received a request to reset your password for your Happy Hostel account. If you made this request, click the button below to reset your password:</p>
            
            <div style="text-align: center;">
                <a href="{{ $resetUrl }}" class="button">Reset My Password</a>
            </div>
            
            <p>If the button doesn't work, you can copy and paste this link into your browser:</p>
            <p style="word-break: break-all; color: #2563eb;">{{ $resetUrl }}</p>
            
            <div class="warning">
                <strong>Important:</strong> This password reset link will expire in 1 hour for security reasons. If you don't reset your password within this time, you'll need to request a new reset link.
            </div>
            
            <p>If you didn't request a password reset, please ignore this email. Your password will remain unchanged.</p>
        </div>

        <div class="contact-info">
            <h3 style="margin-top: 0; color: #1f2937;">Need Help?</h3>
            <p>If you're having trouble with the reset link or have any questions, please contact our support team:</p>
            <ul style="margin: 10px 0;">
                <li><strong>Email:</strong> support@happyhostel.com</li>
                <li><strong>Phone:</strong> +1 (555) 123-4567</li>
            </ul>
        </div>

        <div class="footer">
            <p>This email was sent to {{ $visitor->email }} because a password reset was requested for your Happy Hostel account.</p>
            <p>© {{ date('Y') }} Happy Hostel. All rights reserved.</p>
        </div>
    </div>
</body>
</html>