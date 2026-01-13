<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inquiry Status Update</title>
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
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .content {
            background-color: #ffffff;
            padding: 20px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-pending { background-color: #ffc107; color: #212529; }
        .status-contacted { background-color: #17a2b8; color: #fff; }
        .status-completed { background-color: #28a745; color: #fff; }
        .status-cancelled { background-color: #dc3545; color: #fff; }
        .footer {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            font-size: 12px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ config('app.name') }}</h2>
        <p>Inquiry Status Update</p>
    </div>

    <div class="content">
        <p>Dear {{ $inquiry->name }},</p>

        <p>We wanted to update you on the status of your inquiry submitted for <strong>{{ $inquiry->company }}</strong>.</p>

        <p><strong>Current Status:</strong>
            <span class="status-badge status-{{ $inquiry->status }}">{{ ucfirst($inquiry->status) }}</span>
        </p>

        @if($inquiry->status == 'contacted')
            <p>We have reviewed your inquiry and will be in touch with you shortly to discuss your requirements in detail.</p>
        @elseif($inquiry->status == 'completed')
            <p>Your inquiry has been completed. Thank you for choosing us for your business needs.</p>
        @elseif($inquiry->status == 'cancelled')
            <p>Unfortunately, we had to cancel your inquiry. If you have any questions, please don't hesitate to contact us.</p>
        @else
            <p>Your inquiry is currently being reviewed by our team. We will update you as soon as there are any developments.</p>
        @endif

        <div style="margin: 20px 0; padding: 15px; background-color: #f8f9fa; border-radius: 5px;">
            <h4>Inquiry Details:</h4>
            <p><strong>Company:</strong> {{ $inquiry->company }}</p>
            <p><strong>Contact Person:</strong> {{ $inquiry->name }}</p>
            <p><strong>Email:</strong> {{ $inquiry->email }}</p>
            <p><strong>Phone:</strong> {{ $inquiry->phone }}</p>
            @if($inquiry->message)
                <p><strong>Message:</strong> {{ $inquiry->message }}</p>
            @endif
        </div>

        <p>If you have any questions or need further assistance, please feel free to contact us.</p>

        <p>Best regards,<br>
        {{ config('app.name') }} Team</p>
    </div>

    <div class="footer">
        <p>This is an automated message. Please do not reply to this email.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
</body>
</html>
