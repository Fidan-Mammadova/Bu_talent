<!DOCTYPE html>
<html>
<head>
    <title>Your OTP Code</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .otp-code {
            font-size: 32px;
            font-weight: bold;
            color: #4a90e2;
            text-align: center;
            padding: 20px;
            background: #f5f5f5;
            border-radius: 5px;
            margin: 20px 0;
        }
        .expiry {
            color: #666;
            font-size: 14px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Your OTP Code</h2>
        <p>Please use the following OTP code to verify your account:</p>
        
        <div class="otp-code">
            {{ $otp }}
        </div>
        
        <p class="expiry">This OTP will expire in {{ $expiresIn }}.</p>
        
        <p>If you didn't request this OTP, please ignore this email.</p>
        
        <p>Best regards,<br>Your Application Team</p>
    </div>
</body>
</html> 