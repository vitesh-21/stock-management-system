<?php
session_start();
include("db.php");

// Only Admin can access
if(!isset($_SESSION['role']) || $_SESSION['role'] != "Admin"){
    header("Location: index.html");
    exit();
}

// ADD USER
if(isset($_POST['add_user'])){

    $username = mysqli_real_escape_string($conn,$_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    mysqli_query($conn,"INSERT INTO users (username,password,role) VALUES ('$username','$password','$role')");

    header("Location: manage_users.php?msg=User Added Successfully");
}


// DELETE USER
if(isset($_GET['delete'])){

    $id = intval($_GET['delete']);

    mysqli_query($conn,"DELETE FROM users WHERE id='$id'");

    header("Location: manage_users.php?msg=User Deleted");
}


// FETCH USERS
$users = mysqli_query($conn,"SELECT * FROM users ORDER BY id DESC");

?>

<!DOCTYPE html>
<html>
<head>

<title>Manage Users</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>

body{
font-family: Arial;
background:#f4f6f9;
padding:40px;
}

.container{
max-width:1000px;
margin:auto;
background:white;
padding:30px;
border-radius:10px;
box-shadow:0 5px 20px rgba(0,0,0,0.1);
}

h1{
margin-bottom:20px;
}

table{
width:100%;
border-collapse:collapse;
margin-top:20px;
}

table th{
background:#2c3e50;
color:white;
padding:12px;
text-align:left;
}

table td{
padding:12px;
border-bottom:1px solid #eee;
}

.btn{
padding:10px 15px;
border:none;
border-radius:5px;
cursor:pointer;
}

.btn-add{
background:#27ae60;
color:white;
}

.btn-delete{
background:#e74c3c;
color:white;
}

form input,select{
width:100%;
padding:10px;
margin-bottom:10px;
border:1px solid #ddd;
border-radius:5px;
}

.message{
background:#27ae60;
color:white;
padding:10px;
margin-bottom:15px;
border-radius:5px;
}

</style>

</head>

<body>

<div class="container">

<h1><i class="fas fa-users"></i> Manage Users</h1>

<a href="index.php" style="text-decoration:none;">
<button class="btn">⬅ Back to Dashboard</button>
</a>


<?php if(isset($_GET['msg'])){ ?>

<div class="message">
<?php echo htmlspecialchars($_GET['msg']); ?>
</div>

<?php } ?>


<h2>Add New User</h2>

<form method="POST">

<input type="text" name="username" placeholder="Username" required>

<input type="password" name="password" placeholder="Password" required>

<select name="role">

<option value="Admin">Admin</option>

<option value="Staff">Staff</option>

</select>

<button type="submit" name="add_user" class="btn btn-add">
<i class="fas fa-user-plus"></i> Add User
</button>

</form>


<h2 style="margin-top:40px;">System Users</h2>

<table>

<tr>
<th>ID</th>
<th>Username</th>
<th>Role</th>
<th>Action</th>
</tr>

<?php while($row = mysqli_fetch_assoc($users)){ ?>

<tr>

<td>#<?php echo $row['id']; ?></td>

<td><?php echo htmlspecialchars($row['username']); ?></td>

<td><?php echo $row['role']; ?></td>

<td>

<a href="manage_users.php?delete=<?php echo $row['id']; ?>" 
onclick="return confirm('Delete this user?')">

<button class="btn btn-delete">
<i class="fas fa-trash"></i> Delete
</button>

</a>

</td>

</tr>

<?php } ?>

</table>

</div>

</body>
</html>