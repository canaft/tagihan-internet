<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <script>
        // Redirect otomatis setelah 3 detik
        setTimeout(function() {
            window.location.href = "{{ url('/login') }}";
        }, 1000);
    </script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #ffffff;
            font-family: Arial, sans-serif;
            position: relative;
        }

        .logo-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 220px;
            height: 220px;
            border-radius: 15px; /* Lebih melengkung tapi tidak bulat sempurna */
            background-color: #f5f5f5;
            box-shadow: 0 15px 30px rgba(0,0,0,0.2); /* Shadow di antara background dan box */
            position: relative;
        }

        .logo-container img {
            max-width: 60%;
            max-height: 60%;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.15); /* Sedikit shadow di logo */
            border-radius: 10px;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #e0e0e0;
            border-top: 4px solid #FF2D20;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="logo-container">
        <img src="{{ asset('storage/images/logo-dhs.png') }}" alt="Logo DHS">
        <div class="spinner"></div>
    </div>
</body>
</html>
