<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fleet Command: Mission Rewards Available</title>
    <style>
        body {
            background-color: #0a1416;
            color: #e2e8f0;
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #102023;
            border: 1px solid #224249;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5);
        }
        .header {
            background-color: #182f34;
            padding: 24px 32px;
            border-bottom: 1px solid #224249;
            text-align: center;
        }
        .header h1 {
            color: #d4af37; /* Pirate-gold */
            margin: 0;
            font-size: 24px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .content {
            padding: 32px;
            line-height: 1.6;
        }
        .greeting {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 24px;
            color: #14b8a6; /* Primary */
        }
        .text {
            margin-bottom: 24px;
            color: #cbd5e1;
        }
        .box {
            background-color: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 32px;
        }
        .box-title {
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #94a3b8;
            margin-bottom: 8px;
        }
        .mission-name {
            font-size: 18px;
            font-weight: 700;
            color: #10b981; /* Emerald */
            margin: 0;
        }
        .status-badge {
            display: inline-block;
            margin-top: 12px;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .status-valid {
            background-color: rgba(16, 185, 129, 0.2);
            color: #10b981;
            border-color: rgba(16, 185, 129, 0.3);
        }
        .status-invalid {
            background-color: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            border-color: rgba(239, 68, 68, 0.3);
        }
        .action {
            text-align: center;
            margin-top: 32px;
            margin-bottom: 16px;
        }
        .btn {
            display: inline-block;
            background-color: #d4af37;
            color: #0a1416;
            padding: 16px 32px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            box-shadow: 0 8px 20px rgba(212, 175, 55, 0.3);
        }
        .footer {
            background-color: #0f1c1f;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #224249;
            font-size: 12px;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Mission Rewards Available</h1>
        </div>
        <div class="content">
            <div class="greeting">Ahoy, {{ $studentName }}!</div>
            
            <div class="text">
                The Fleet Captain has reviewed your Logic Challenges. 
                Your mission correction is now complete and your bounty status has been updated.
            </div>

            <div class="box">
                <div class="box-title">Mission</div>
                <div class="mission-name">{{ $briefTitle }}</div>
                @if($resultStatus === 'Valid')
                    <div class="status-badge status-valid">Valid</div>
                @else
                    <div class="status-badge status-invalid">Invalid</div>
                @endif
            </div>

            <div class="text" style="margin-bottom: 0;">
                You can now view your detailed performance report, feedback from Fleet Command, and see the rewards you have earned.
            </div>

            <div class="action">
                <a href="{{ $actionUrl }}" class="btn">View Correction</a>
            </div>
        </div>
        <div class="footer">
            Marine HQ Server • Fleet Command<br>
            "Wealth, fame, power... the world is waiting."
        </div>
    </div>
</body>
</html>
