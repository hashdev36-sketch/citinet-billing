<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"></head>
<body style="margin:0;padding:16px;background:linear-gradient(150deg,#0B2A3B 0%,#1C4E6F 100%);font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Arial,sans-serif;">
<div style="max-width:480px;margin:0 auto;background:#fff;border-radius:20px;overflow:hidden;box-shadow:0 8px 32px rgba(0,0,0,.25);">
    <div style="padding:24px 20px;text-align:center;">
        <div style="font-size:32px;">✅</div>
        <h1 style="font-size:22px;font-weight:800;color:#0B2A3B;margin:6px 0 2px;">Your voucher is ready</h1>
        <p style="font-size:13px;color:#6b7280;margin:0;">{{ $voucher->package->name }}</p>
    </div>
    <div style="padding:0 20px 24px;">
        <div style="background:linear-gradient(135deg,#ecfdf5 0%,#d1fae5 100%);border:2px solid #6ee7b7;border-radius:16px;padding:16px;">
            <div style="display:flex;justify-content:space-between;padding:6px 0;font-size:13px;">
                <span style="color:#6b7280;font-weight:700;">Username</span>
                <span style="background:#f3f4f6;padding:4px 12px;border-radius:20px;">{{ $voucher->username }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;padding:6px 0;font-size:13px;">
                <span style="color:#6b7280;font-weight:700;">Password</span>
                <span style="background:#f3f4f6;padding:4px 12px;border-radius:20px;">{{ $voucher->revealPassword() }}</span>
            </div>
            <p style="font-size:12px;color:#047857;margin:10px 0 0;">Expires {{ $voucher->expires_at->format('d M Y, h:i A') }}</p>
        </div>
        <p style="font-size:12px;color:#9ca3af;margin:16px 0 0;">Order {{ $order->order_number }} &middot; &#8358;{{ number_format($order->amount) }}</p>
        <p style="font-size:12px;color:#9ca3af;margin:4px 0 0;">Lost this email? Retrieve it anytime from your dashboard.</p>
    </div>
</div>
</body>
</html>
