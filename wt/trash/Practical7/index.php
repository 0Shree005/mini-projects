<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AISE360 | Student Portal PR7</title>
    <style>
        :root {
            --bg-color: #0b1120;
            --primary: #00f2fe;
            --secondary: #4facfe;
            --aurora-1: #00fff2;
            --aurora-2: #7f00ff;
            --glass-bg: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Roboto, sans-serif; }

        body {
            background-color: var(--bg-color);
            overflow: hidden; 
            height: 100vh;
            width: 100vw;
            display: flex;
            flex-direction: column;
            color: white;
        }

        /* The Aurora Layers */
        body::before, body::after {
            content: "";
            position: absolute;
            opacity: 0.6;
            filter: blur(100px);
            z-index: -1;
            animation: aurora-move 15s infinite alternate ease-in-out;
        }

        body::before {
            width: 800px; height: 800px;
            background: radial-gradient(circle, var(--aurora-1) 0%, rgba(0,255,242,0) 70%);
            top: -200px; left: -200px;
        }

        body::after {
            width: 600px; height: 600px;
            background: radial-gradient(circle, var(--aurora-2) 0%, rgba(127,0,255,0) 70%);
            bottom: -100px; right: -100px;
            animation-delay: -5s;
        }

        @keyframes aurora-move {
            0% { transform: translate(0, 0) rotate(0deg); }
            100% { transform: translate(100px, 50px) rotate(10deg); }
        }

        .navbar {
            width: 100%;
            height: 70px;
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--glass-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 50px;
            position: fixed;
            top: 0;
            z-index: 100;
        }

        .nav-logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .nav-links {
            display: flex;
            gap: 30px;
            list-style: none;
        }

        .nav-links li a {
            text-decoration: none;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
            transition: color 0.3s;
        }

        .nav-links li a:hover { color: var(--primary); }

        .main-container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 70px;
        }

        .glass-box {
            background: var(--glass-bg);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 40px;
            width: 400px;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
            text-align: center;
            transform: translateY(0px);
            transition: transform 0.3s ease;
        }

        .glass-box:hover { transform: translateY(-5px); }

        .glass-box h2 {
            margin-bottom: 30px;
            font-weight: 600;
            background: linear-gradient(to right, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .input-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .input-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.7);
        }

        .input-control {
            width: 100%;
            padding: 12px 15px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--glass-border);
            border-radius: 8px;
            color: white;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .input-control:focus {
            outline: none;
            border-color: var(--primary);
            background: rgba(255, 255, 255, 0.07);
            box-shadow: 0 0 10px rgba(0, 242, 254, 0.3);
        }

        .btn-submit {
            width: 100%;
            padding: 12px;
            background: linear-gradient(45deg, var(--secondary), var(--primary));
            border: none;
            border-radius: 8px;
            color: #0b1120;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.1s, box-shadow 0.3s;
        }

        .btn-submit:hover {
            box-shadow: 0 0 20px rgba(0, 242, 254, 0.5);
        }

        .btn-submit:active { transform: scale(0.98); }

        .status-msg {
            margin-top: 20px;
            font-size: 0.9rem;
        }

        .status-msg a { color: var(--primary); text-decoration: none; font-weight: bold;}
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="nav-logo">AISE360</div>
        <ul class="nav-links">
            <li><a href="#">Dashboard</a></li>
            <li><a href="display.php">Student List</a></li>
            <li><a href="#">Settings</a></li>
        </ul>
    </nav>

    <div class="main-container">
        <div class="glass-box">
            <h2>Student Registration</h2>
            
            <form method="POST" action="">
                <div class="input-group">
                    <label>Full Name</label>
                    <input type="text" name="stud_name" class="input-control" placeholder="Your Name" required>
                </div>
                
                <div class="input-group">
                    <label>Email Address</label>
                    <input type="email" name="stud_email" class="input-control" placeholder="example@example.com" required>
                </div>
                
                <button type="submit" name="register" class="btn-submit">Register Student</button>
            </form>

            <?php
            if (isset($_POST['register'])) {
                // Secure Code: Sanitized input
                // $name = mysqli_real_escape_string($conn, $_POST['stud_name']);
                // $email = mysqli_real_escape_string($conn, $_POST['stud_email']);
                // $sql = "INSERT INTO students (name, email) VALUES ('$name', '$email')";

                // INSECURE CODE - DO NOT USE IN PRODUCTION
                // The query is built using direct string concatenation
                $name = $_POST['stud_name']; 
                $email = $_POST['stud_email'];
                $sql = "INSERT INTO students (name, email) VALUES ('$name', '$email')";

                if (mysqli_multi_query($conn, $sql)) {
                    echo "<p class='status-msg' style='color:#00fff2;'>Registration successful! <a href='display.php'>View List</a></p>";
                } else {
                    echo "<p class='status-msg' style='color:#ff4f4f;'>Error: DB Connectivity Issue.</p>";
                }
            }
            ?>
        </div>
    </div>

</body>
</html>