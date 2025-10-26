<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f2f5;
            margin: 0;
        }

        .dashboard-card {
            background: #fff;
            padding: 40px 30px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            width: 360px;
            text-align: center;
        }

        .dashboard-card h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            margin: 20px 0;
            object-fit: cover;
            border: 3px solid #4CAF50;
        }

        .user-info {
            font-size: 16px;
            color: #555;
            margin-bottom: 25px;
        }

        .logout-btn {
            padding: 12px 20px;
            background: #e53935;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 15px;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .logout-btn:hover {
            background: #d32f2f;
        }
    </style>
</head>
<body>
    <div class="dashboard-card">
        <h2>Welcome, {{ auth()->user()->name }}</h2>

        @if(auth()->user()->avatar)
            <img src="{{ auth()->user()->avatar }}" alt="Avatar" class="avatar">
        @endif

        <div class="user-info">
            <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
        </div>

        <a href="{{ route('logout') }}" class="logout-btn">Logout</a>
    </div>
</body>
</html>
