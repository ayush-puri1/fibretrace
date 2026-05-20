<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Payment Confirmed — FibreTrace</title>
    <style>
        body { font-family: 'Inter', Arial, sans-serif; background: #f0f4f8; margin: 0; padding: 40px 20px; }
        .container { max-width: 560px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,53,39,0.08); }
        .header { background: linear-gradient(135deg, #006C49 0%, #00A36C 100%); padding: 40px 32px; text-align: center; }
        .header h1 { color: #ffffff; font-size: 24px; margin: 0; font-weight: 700; }
        .body { padding: 32px; }
        .body p { color: #374151; font-size: 15px; line-height: 1.6; }
        .amount { font-size: 28px; font-weight: 800; color: #006C49; }
        .lot-box { background: #F0FDF4; border: 1px solid #BBF7D0; border-radius: 10px; padding: 16px 20px; margin: 20px 0; }
        .lot-box .label { font-size: 11px; text-transform: uppercase; color: #6B7280; font-weight: 700; letter-spacing: 0.05em; }
        .lot-box .value { font-size: 16px; font-weight: 700; color: #065F46; }
        .footer { padding: 24px 32px; border-top: 1px solid #E5E7EB; color: #9CA3AF; font-size: 12px; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $recipientRole === 'buyer' ? '✅ Payment Confirmed' : '💰 Payment Received' }}</h1>
        </div>
        <div class="body">
            <p>Hello,</p>
            @if($recipientRole === 'buyer')
                <p>Your payment of <span class="amount">{{ $amount }}</span> has been successfully received into escrow for lot <strong>{{ $lotNumber }}</strong>. The seller has been notified and will dispatch the goods shortly.</p>
            @else
                <p>Great news! A payment of <span class="amount">{{ $amount }}</span> has been confirmed in escrow for lot <strong>{{ $lotNumber }}</strong>. Please arrange dispatch at the earliest.</p>
            @endif
            <div class="lot-box">
                <div class="label">Transaction Reference</div>
                <div class="value">{{ $transaction->transaction_number }}</div>
            </div>
            <p>Log in to your dashboard to track the latest status.</p>
        </div>
        <div class="footer">
            © {{ date('Y') }} FibreTrace B2B Marketplace · noreply@fibretrace.in
        </div>
    </div>
</body>
</html>
