<?php
include("db.php");
$message = "";

if(isset($_POST['reset_password'])){
    // trim() removes hidden spaces that cause "Username not found" errors
    $username = trim(mysqli_real_escape_string($conn, $_POST['username']));
    $new_pass = $_POST['new_password'];
    $conf_pass = $_POST['confirm_password'];

    // 1. Validation Checks
    if(empty($username) || empty($new_pass)){
        $message = "<div style='color:#ffaa00; padding:10px; border:1px solid #ffaa00;'>Error: All fields are required.</div>";
    } elseif($new_pass !== $conf_pass){
        $message = "<div style='color:#ff3333; padding:10px; border:1px solid #ff3333;'>Error: Passwords do not match!</div>";
    } else {
        // 2. Check if username exists (Case-insensitive check using LOWER)
        $user_check = mysqli_query($conn, "SELECT * FROM users WHERE LOWER(username) = LOWER('$username')");
        
        if(mysqli_num_rows($user_check) > 0){
            // 3. Hash the new password and update
            // This is now perfectly compatible with your updated login.php
            $hashed_password = password_hash($new_pass, PASSWORD_DEFAULT);
            $update = "UPDATE users SET password='$hashed_password' WHERE LOWER(username) = LOWER('$username')";
            
            if(mysqli_query($conn, $update)){
                $message = "
                <div style='color:#00ff66; padding:10px; border:1px solid #00ff66; margin-bottom:10px;'>
                    ✅ SUCCESS: Password updated for $username!<br>
                    Redirecting to index in 3 seconds...
                </div>
                <a href='index.php' style='display:block; background:#00ff66; color:#000; text-align:center; padding:10px; text-decoration:none; font-weight:bold;'>GO TO LOGIN NOW</a>
                <script>
                    setTimeout(function(){
                        window.location.href = 'index.php';
                    }, 3000);
                </script>";
            } else {
                $message = "<div style='color:red;'>SQL Error: " . mysqli_error($conn) . "</div>";
            }
        } else {
            $message = "<div style='color:#ffaa00; padding:10px; border:1px solid #ffaa00;'>Error: Username '$username' not found. Check your spelling.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | Stock System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background:#000; color:#00ff66; font-family:monospace; display:flex; justify-content:center; align-items:center; height:100vh; margin:0; }
        .reset-card { width:360px; border:2px solid #00ff66; padding:25px; box-shadow:0 0 20px #00ff66; background:#000; border-radius:8px; }
        h2 { text-align:center; border-bottom:1px dashed #00ff66; padding-bottom:15px; margin-top:0; }
        label { display:block; margin-top:15px; font-weight:bold; font-size: 14px; }
        input { width:100%; background:#111; border:1px solid #00ff66; color:#00ff66; padding:12px; margin-top:5px; font-family:monospace; box-sizing: border-box; }
        input:focus { outline: none; background: #222; box-shadow: 0 0 5px #00ff66; }
        button { width:100%; background:#00ff66; color:#000; border:none; padding:14px; margin-top:25px; cursor:pointer; font-weight:bold; font-family:monospace; font-size:16px; transition: 0.3s; }
        button:hover { background:#00cc55; transform: scale(1.02); }
        .footer-link { color:#00ff66; text-decoration:none; display:block; text-align:center; margin-top:20px; border-top:1px dashed #333; padding-top:15px; font-size: 13px; }
        .footer-link:hover { text-decoration: underline; }
        #status-msg { margin-bottom: 20px; }
    </style>
</head>
<body>

<div class="reset-card">
    <h2><i class="fas fa-user-shield"></i> ACCOUNT RECOVERY</h2>
    
    <div id="status-msg"><?php echo $message; ?></div>
    
    <form method="POST" action="forgot_password.php">
        <label><i class="fas fa-user"></i> Confirm Username:</label>
        <input type="text" name="username" placeholder="Enter username" required autocomplete="off">

        <label><i class="fas fa-key"></i> New Password:</label>
        <input type="password" name="new_password" placeholder="••••••••" required>

        <label><i class="fas fa-check-double"></i> Confirm New Password:</label>
        <input type="password" name="confirm_password" placeholder="••••••••" required>

        <button type="submit" name="reset_password">
            <i class="fas fa-sync-alt"></i> UPDATE PASSWORD
        </button>
    </form>
    
    <a href="index.php" class="footer-link">← Return to Secure Login</a>
</div>

</body>
</html>