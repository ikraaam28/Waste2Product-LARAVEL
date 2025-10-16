<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate of Participation</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            text-align: center;
            color: #333;
            background: #fff;
        }
        .certificate {
            width: 80%;
            margin: 50px auto;
            border: 10px solid #004c99;
            padding: 20px;
            position: relative;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 20px;
        }
        h1 {
            font-size: 2.5em;
            color: #004c99;
            margin-bottom: 10px;
        }
        h2 {
            font-size: 1.5em;
            color: #004c99;
            margin-bottom: 20px;
        }
        p {
            font-size: 1.2em;
            margin: 5px 0;
        }
        .app-name {
            font-weight: bold;
            font-size: 1.1em;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <!-- Force image to load by removing lazy loading and ensuring it’s in the HTML -->
<img class="logo" src="{{ public_path('assets/img/recycleverse1.png') }}" alt="RecycleVerse Logo">
        <h1>Certificate of Participation</h1>
        <h2>Congratulations!</h2>
        <p>This certifies that</p>
        <p><strong>{{ htmlspecialchars($userName ?? 'Unknown User') }}</strong></p>
        <p>has successfully participated in the tutorial</p>
        <p><strong>{{ htmlspecialchars($tuto->title ?? 'Unknown Tutorial') }}</strong></p>
        <p>with an average score of <strong>{{ number_format($averagePercentage ?? 0, 2) }}%</strong>.</p>
        <p>Issued on: October 16, 2025</p>
        <p class="app-name">Proudly presented by <strong>RecycleVerse</strong></p>
    </div>
</body>
</html>