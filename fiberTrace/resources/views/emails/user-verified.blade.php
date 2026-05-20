<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Account Verified — FibreTrace</title>
    <style>
        body { font-family: 'Inter', Arial, sans-serif; background: #f0f4f8; margin: 0; padding: 40px 20px; }
        .container { max-width: 560px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,53,39,0.08); }
        .header { background: linear-gradient(135deg, #006C49 0%, #00A36C 100%); padding: 40px 32px; text-align: center; }
        .header h1 { color: #ffffff; font-size: 24px; margin: 0; font-weight: 700; }
        .header p { color: rgba(255,255,255,0.85); font-size: 14px; margin: 8px 0 0; }
        .body { padding: 32px; }
        .body p { color: #374151; font-size: 15px; line-height: 1.6; }
        .badge { display: inline-block; background: #D1FAE5; color: #065F46; font-size: 12px; font-weight: 700; padding: 6px 14px; border-radius: 100px; margin-bottom: 20px; }
        .cta { display: block; background: #006C49; color: #ffffff; text-align: center; padding: 14px 32px; border-radius: 10px; text-decoration: none; font-weight: 700; font-size: 15px; margin: 24px 0; }
        .footer { padding: 24px 32px; border-top: 1px solid #E5E7EB; color: #9CA3AF; font-size: 12px; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 You're Verified!</h1>
            <p>Your FibreTrace B2B account is ready</p>
        </div>
        <div class="body">
            <span class="badge">✓ GSTIN Verified</span>
            <p>Hello <strong>{{ $userName }}</strong>,</p>
            <p>Great news! Your account for <strong>{{ $companyName }}</strong> has been verified by our team. You now have full access to the FibreTrace marketplace.</p>
            <p>You can now:</p>
            <ul>
                <li>Browse and bid on active lots</li>
                <li>List your fibre scrap for auction</li>
                <li>Track settlements and dispatch</li>
            </ul>
            <a href="{{ $loginUrl }}" class="cta">Login to FibreTrace →</a>
            <p style="font-size: 13px; color: #6B7280;">If you didn't register for FibreTrace, please ignore this email.</p>
        </div>
        <div class="footer">
            © {{ date('Y') }} FibreTrace B2B Marketplace · noreply@fibretrace.in
        </div>
    </div>
</body>
</html>
