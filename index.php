<?php
session_start();

// --- 1. DATABASE CONFIGURATION (Updated from your screenshot) ---
$host = "sql101.infinityfree.com"; 
$user = "if0_41353191";           
$pass = "8900veshka1";           
$dbname = "if0_41353191_lance";   

// --- 2. CONNECT TO DATABASE ---
$conn = new mysqli($host, $user, $pass, $dbname);

// If the connection fails, this will show a message instead of a "500 Error"
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

$error = "";

// --- 3. LOGIN LOGIC ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    if (empty($username) || empty($password) || empty($role)) {
        $error = "All fields are required!";
    } else {
        // Use prepared statements to prevent SQL Injection
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND role = ?");
        $stmt->bind_param("ss", $username, $role);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Verify the hashed password
            if (password_verify($password, $user['password'])) {
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                // Redirection Logic
                if ($user['role'] === 'admin') {
                    header("Location: admin.php");
                } else {
                    header("Location: sales.php");
                }
                exit();
            } else {
                $error = "Invalid password!";
            }
        } else {
            $error = "Invalid username or role!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Login | Stock System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body {
            background: linear-gradient(135deg, #1e272e, #2f3640);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .login-container {
            background: #fff;
            width: 100%;
            max-width: 380px;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
            text-align: center;
        }
        .login-header h2 { color: #2c3e50; margin-bottom: 20px; }
        .input-group { position: relative; margin-bottom: 15px; }
        .input-group input, .input-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            outline: none;
        }
        .error-text {
            background: #fdeaea;
            color: #e74c3c;
            padding: 10px;
            border-radius: 6px;
            font-size: 0.85rem;
            margin-bottom: 15px;
        }
        button {
            width: 100%;
            padding: 12px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }
        button:hover { background: #2980b9; }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-header">
        <i class="fas fa-boxes-stacked fa-3x" style="color:#3498db; margin-bottom:10px;"></i>
        <h2>STOCK MANAGER</h2>
    </div>

    <form method="POST" action="">
        <div class="input-group">
            <input type="text" name="username" placeholder="Username" required>
        </div>
        <div class="input-group">
            <input type="password" name="password" placeholder="Password" required>
        </div>
        <div class="input-group">
            <select name="role" required>
                <option value="">Select Role</option>
                <option value="staff">Staff</option>
                <option value="admin">Admin</option>
            </select>
        </div>

        <?php if (!empty($error)): ?>
            <div class="error-text"><?php echo $error; ?></div>
        <?php endif; ?>

        <button type="submit">Login</button>
    </form>
</div>

</body>
</html>