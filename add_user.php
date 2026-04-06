<?php
// 1. ERROR REPORTING
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// 2. DATABASE CREDENTIALS (Corrected based on your screenshots)
$host = 'sql101.infinityfree.com'; // Rectified to sql101
$user = 'if0_41353191'; 
$pass = '8900veshka1'; 
$dbname = 'if0_41353191_lance';   // Rectified to _lance

try {
    $conn = new mysqli($host, $user, $pass, $dbname);
} catch (mysqli_sql_exception $e) {
    die("<div style='color:red; padding:20px; border:1px solid red; background:white; font-family:sans-serif;'>
            <strong>Connection Failed:</strong> " . $e->getMessage() . " <br><br>
            <em>Check if the database 'if0_41353191_lance' is still active in your Control Panel.</em>
         </div>");
}

$msg = "";
$msg_class = "";

// 3. REGISTRATION LOGIC
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register_user'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $raw_password = $_POST['password'];
    $role = $_POST['role'];

    // Check if user exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $msg = "Error: Username '$username' is already taken.";
        $msg_class = "error";
    } else {
        // Hash password to match your table's encryption format
        $hashed_password = password_hash($raw_password, PASSWORD_DEFAULT);

        // Insert including NULL for security columns to match your 'jk.JPG' structure
        $insert = $conn->prepare("INSERT INTO users (username, password, security_question, security_answer, role) VALUES (?, ?, NULL, NULL, ?)");
        $insert->bind_param("sss", $username, $hashed_password, $role);

        if ($insert->execute()) {
            $msg = "Success! User '$username' has been registered.";
            $msg_class = "success";
        } else {
            $msg = "Error saving to database.";
            $msg_class = "error";
        }
        $insert->close();
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add System User</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: #0f172a; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; color: #fff; }
        .card { background: #fff; color: #333; padding: 40px; border-radius: 12px; width: 350px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5); border-top: 5px solid #10b981; }
        h2 { margin-top: 0; text-align: center; color: #1e293b; }
        label { display: block; margin: 15px 0 5px; font-weight: 600; font-size: 13px; color: #4b5563; }
        input, select { width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #10b981; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; margin-top: 25px; transition: 0.2s; }
        button:hover { background: #059669; transform: translateY(-1px); }
        .msg { padding: 10px; border-radius: 6px; margin-bottom: 20px; text-align: center; font-size: 14px; }
        .error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .back { display: block; text-align: center; margin-top: 20px; text-decoration: none; color: #64748b; font-size: 13px; }
        .back:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="card">
    <h2><i class="fas fa-user-plus"></i> Register User</h2>

    <?php if($msg): ?>
        <div class="msg <?php echo $msg_class; ?>">
            <?php echo $msg; ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <label>Username</label>
        <input type="text" name="username" required placeholder="e.g. admin_jane">

        <label>Password</label>
        <input type="password" name="password" required placeholder="••••••••">

        <label>System Role</label>
        <select name="role">
            <option value="staff">staff</option>
            <option value="admin">admin</option>
        </select>

        <button type="submit" name="register_user">Create Account</button>
        <a href="index.php" class="back">← Back to Login page</a>
    </form>
</div>

</body>
</html>