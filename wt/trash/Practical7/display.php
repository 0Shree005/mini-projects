<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AISE360 | Student Database</title>
    <style>
        /* --- 1. Consistent Aurora Background --- */
        :root {
            --bg-color: #0b1120;
            --primary: #00f2fe;
            --secondary: #4facfe;
            --aurora-1: #00fff2;
            --aurora-2: #7f00ff;
            --glass-bg: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }

        body {
            background-color: var(--bg-color);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: white;
            overflow-x: hidden;
        }

        /* Aurora Layers */
        body::before, body::after {
            content: "";
            position: fixed;
            opacity: 0.4;
            filter: blur(100px);
            z-index: -1;
        }
        body::before { width: 600px; height: 600px; background: var(--aurora-1); top: -100px; left: -100px; border-radius: 50%; }
        body::after { width: 500px; height: 500px; background: var(--aurora-2); bottom: -100px; right: -100px; border-radius: 50%; }

        /* --- 2. Navbar --- */
        .navbar {
            width: 100%;
            height: 70px;
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--glass-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 50px;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .nav-logo { font-size: 1.5rem; font-weight: bold; color: var(--primary); letter-spacing: 1px; }

        /* --- 3. Dashboard Container --- */
        .container {
            flex: 1;
            padding: 50px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .header-section {
            width: 100%;
            max-width: 900px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .header-section h2 { font-weight: 600; font-size: 1.8rem; }

        .btn-add {
            padding: 10px 20px;
            background: var(--glass-bg);
            border: 1px solid var(--primary);
            border-radius: 8px;
            color: var(--primary);
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s;
        }

        .btn-add:hover { background: var(--primary); color: #0b1120; box-shadow: 0 0 15px var(--primary); }

        /* --- 4. Glass Table Styling --- */
        .table-container {
            width: 100%;
            max-width: 900px;
            background: var(--glass-bg);
            backdrop-filter: blur(15px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        thead {
            background: rgba(255, 255, 255, 0.07);
            color: var(--primary);
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 1px;
        }

        th, td { padding: 20px; border-bottom: 1px solid var(--glass-border); }

        tbody tr:hover { background: rgba(255, 255, 255, 0.03); }

        .id-badge {
            background: rgba(0, 242, 254, 0.1);
            color: var(--primary);
            padding: 4px 10px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 0.85rem;
        }

        .empty-state { text-align: center; padding: 40px; color: rgba(255, 255, 255, 0.5); }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="nav-logo">AISE360</div>
        <!-- <div style="color: rgba(255,255,255,0.6); font-size: 0.9rem;">Lead Management System v1.0</div> -->
    </nav>

    <div class="container">
        <div class="header-section">
            <h2>Registered Students</h2>
            <a href="index.php" class="btn-add">+ Register New</a>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Ref ID</th>
                        <th>Student Name</th>
                        <th>Email Address</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT * FROM students ORDER BY id DESC";
                    $result = mysqli_query($conn, $query);

                    if (mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td><span class='id-badge'># " . $row['id'] . "</span></td>";
                            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3' class='empty-state'>No records found in the database.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>