<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #dc2626;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }
        .button {
            display: inline-block;
            background-color: #dc2626;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: bold;
        }
        .button:hover {
            background-color: #b91c1c;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 14px;
            color: #666;
        }
        .warning {
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('app.name') }}</h1>
        <p>Đặt lại mật khẩu</p>
    </div>
    
    <div class="content">
        <h2>Xin chào {{ $customer->name }}!</h2>
        
        <p>Chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn.</p>
        
        <p><strong>Thông tin tài khoản:</strong></p>
        <ul>
            <li>Số điện thoại: {{ $customer->tel }}</li>
            <li>Email: {{ $customer->email }}</li>
        </ul>
        
        <p>Để đặt lại mật khẩu, vui lòng click vào nút bên dưới:</p>
        
        <div style="text-align: center;">
            <a href="{{ $resetUrl }}" class="button">Đặt lại mật khẩu</a>
        </div>
        
        <p>Hoặc copy và paste link sau vào trình duyệt:</p>
        <p style="word-break: break-all; background-color: #f3f4f6; padding: 10px; border-radius: 4px;">
            {{ $resetUrl }}
        </p>
        
        <div class="warning">
            <strong>⚠️ Lưu ý quan trọng:</strong>
            <ul>
                <li>Link này chỉ có hiệu lực trong <strong>15 phút</strong></li>
                <li>Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này</li>
                <li>Không chia sẻ link này với bất kỳ ai</li>
            </ul>
        </div>
        
        <p>Nếu bạn gặp khó khăn, vui lòng liên hệ với chúng tôi để được hỗ trợ.</p>
        
        <p>Trân trọng,<br>
        Đội ngũ {{ config('app.name') }}</p>
    </div>
    
    <div class="footer">
        <p>Email này được gửi tự động, vui lòng không trả lời.</p>
        <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
</body>
</html>
