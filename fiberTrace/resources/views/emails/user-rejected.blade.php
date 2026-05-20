<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Registration Update — FibreTrace</title>
    <style>
        body { font-family: 'Inter', Arial, sans-serif; background: #f0f4f8; margin: 0; padding: 40px 20px; }
        .container { max-width: 560px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #1F2937 0%, #374151 100%); padding: 40px 32px; text-align: center; }
        .header h1 { color: #ffffff; font-size: 22px; margin: 0; font-weight: 700; }
        .body { padding: 32px; }
        .body p { color: #374151; font-size: 15px; line-height: 1.6; }
        .reason-box { background: #FEF2F2; border: 1px solid #FECACA; border-radius: 10px; padding: 16px 20px; margin: 20px 0; color: #991B1B; font-size: 14px; }
        .footer { padding: 24px 32px; border-top: 1px solid #E5E7EB; color: #9CA3AF; font-size: 12px; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Registration Update</h1>
        </div>
        <div class="body">
            <p>Hello <strong>{{ $userName }}</strong>,</p>
            <p>Thank you for registering <strong>{{ $companyName }}</strong> on FibreTrace. After reviewing your submitted GSTIN, we were unable to verify your account at this time.</p>
            <div class="reason-box">
                <strong>Reason:</strong> {{ $reason }}
            </div>
            <p>If you believe this is an error, please contact our support team at <a href="mailto:support@fibretrace.in">support@fibretrace.in</a> with your correct GSTIN documentation.</p>
        </div>
        <div class="footer">
            © {{ date('Y') }} FibreTrace B2B Marketplace · noreply@fibretrace.in
        </div>
    </div>
</body>
</html>
