<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f7f6; padding: 20px;">
    <div style="max-width: 600px; margin: auto; background-color: #e6f7e6; border-radius: 10px; padding: 30px; text-align: center;">
        <h2 style="color: #2e7d32;">Reset Your Password</h2>
        <p>Hello {{ $user->name }},</p>
        <p>You requested a password reset. Click the button below to reset your password:</p>
        <a href="{{ url('/reset-password/'.$token) }}" 
           style="display: inline-block; padding: 12px 25px; margin: 20px 0; background-color: #4caf50; color: #fff; text-decoration: none; border-radius: 5px;">
           Reset Password
        </a>
        <p>If you did not request this, you can safely ignore this email.</p>
        <p style="font-size: 12px; color: #555;">Waste2Product &copy; {{ date('Y') }}</p>
    </div>
</body>
</html>
