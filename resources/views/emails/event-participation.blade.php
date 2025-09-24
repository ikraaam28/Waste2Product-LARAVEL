<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Participation Confirmation</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        .header p {
            margin: 10px 0 0 0;
            font-size: 16px;
            opacity: 0.9;
        }
        .content {
            padding: 40px 30px;
        }
        .event-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 25px;
            margin: 25px 0;
            border-left: 4px solid #28a745;
        }
        .event-title {
            color: #28a745;
            font-size: 22px;
            font-weight: 600;
            margin: 0 0 15px 0;
        }
        .event-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 20px 0;
        }
        .detail-item {
            display: flex;
            align-items: center;
        }
        .detail-item i {
            color: #28a745;
            margin-right: 10px;
            width: 20px;
        }
        .qr-section {
            text-align: center;
            margin: 30px 0;
            padding: 25px;
            background: #f8f9fa;
            border-radius: 10px;
        }
        .qr-code {
            margin: 20px 0;
        }
        .qr-code img {
            max-width: 200px;
            height: auto;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .instructions {
            background: #e3f2fd;
            border-radius: 10px;
            padding: 20px;
            margin: 25px 0;
        }
        .instructions h3 {
            color: #1976d2;
            margin: 0 0 15px 0;
            font-size: 18px;
        }
        .instructions ul {
            margin: 0;
            padding-left: 20px;
        }
        .instructions li {
            margin: 8px 0;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            text-decoration: none;
            padding: 15px 30px;
            border-radius: 25px;
            font-weight: 600;
            margin: 10px 5px;
            transition: all 0.3s ease;
        }
        .button:hover {
            background: linear-gradient(135deg, #218838, #1ea085);
            transform: translateY(-2px);
        }
        .footer {
            background: #f8f9fa;
            padding: 30px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }
        .footer a {
            color: #28a745;
            text-decoration: none;
        }
        .badge {
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin: 5px;
        }
        @media (max-width: 600px) {
            .event-details {
                grid-template-columns: 1fr;
            }
            .container {
                margin: 0;
                border-radius: 0;
            }
            .header, .content, .footer {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🎉 You're Registered!</h1>
            <p>Thank you for joining our event. Here are your event details and QR code.</p>
        </div>

        <!-- Content -->
        <div class="content">
            <h2>Hello {{ $user->first_name }}!</h2>
            <p>Great news! You've successfully registered for the event. We're excited to have you join us.</p>

            <!-- Event Card -->
            <div class="event-card">
                <h3 class="event-title">{{ $event->title }}</h3>
                <p>{{ $event->description }}</p>
                
                <div class="event-details">
                    <div class="detail-item">
                        <i class="fas fa-calendar-alt"></i>
                        <span><strong>Date:</strong> {{ $event->date->format('l, F j, Y') }}</span>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-clock"></i>
                        <span><strong>Time:</strong> {{ $event->time->format('g:i A') }}</span>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span><strong>Location:</strong> {{ $event->location }}</span>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-tag"></i>
                        <span><strong>Category:</strong> {{ $event->category }}</span>
                    </div>
                </div>

                <div style="text-align: center; margin-top: 20px;">
                    <span class="badge">Event Confirmed</span>
                    <span class="badge">QR Code Ready</span>
                </div>
            </div>

            <!-- QR Code Section -->
            <div class="qr-section">
                <h3 style="color: #28a745; margin-bottom: 20px;">Your Event QR Code</h3>
                <p>Present this QR code at the event for check-in:</p>
                
                <div class="qr-code">
                    <div id="qrcode" style="display: flex; justify-content: center;"></div>
                </div>
                
                <p style="font-size: 14px; color: #6c757d; margin-top: 15px;">
                    <strong>Participant ID:</strong> {{ $participantId }}
                </p>
            </div>

            <!-- Instructions -->
            <div class="instructions">
                <h3>📱 How to Use Your QR Code</h3>
                <ul>
                    <li><strong>Save this email</strong> or take a screenshot of the QR code</li>
                    <li><strong>Arrive on time</strong> and present your QR code at check-in</li>
                    <li><strong>Keep your phone charged</strong> for easy access to the QR code</li>
                    <li><strong>Contact us</strong> if you have any questions before the event</li>
                </ul>
            </div>

            <!-- Action Buttons -->
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ route('events.show', $event) }}" class="button">View Event Details</a>
                <a href="{{ route('events.qr', [$event, $participantId]) }}" class="button">View QR Code Online</a>
            </div>

            <!-- Additional Info -->
            <div style="background: #fff3cd; border-radius: 10px; padding: 20px; margin: 25px 0;">
                <h4 style="color: #856404; margin: 0 0 10px 0;">⚠️ Important Reminders</h4>
                <ul style="margin: 0; color: #856404;">
                    <li>Please arrive 15 minutes before the event starts</li>
                    <li>Bring a valid ID for verification</li>
                    <li>Check your email for any updates about the event</li>
                    <li>If you can't attend, please let us know as soon as possible</li>
                </ul>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>TeaHouse Events</strong></p>
            <p>Thank you for being part of our community!</p>
            <p>
                <a href="{{ route('events') }}">Browse More Events</a> | 
                <a href="{{ route('my-events') }}">My Events</a> | 
                <a href="{{ route('contact') }}">Contact Us</a>
            </p>
            <p style="font-size: 12px; margin-top: 20px;">
                This email was sent to {{ $user->email }}. If you have any questions, please contact us.
            </p>
        </div>
    </div>

    <!-- QR Code Library -->
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
    <script>
        // Generate QR Code
        const qrData = {!! $qrData !!};
        
        QRCode.toCanvas(document.getElementById('qrcode'), JSON.stringify(qrData), {
            width: 200,
            height: 200,
            color: {
                dark: '#28a745',
                light: '#ffffff'
            },
            margin: 2,
            errorCorrectionLevel: 'M'
        }, function (error) {
            if (error) console.error(error);
        });
    </script>
</body>
</html>
