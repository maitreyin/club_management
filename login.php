<?php

session_start();

$conn = new mysqli("localhost","root","","club_management_system");

if($conn->connect_error){
    die("Connection Failed");
}

$error = "";

// ======================================
// LOGIN
// ======================================

if(isset($_POST['login'])){

    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // ==============================
    // ADMIN LOGIN
    // ==============================

    if($role == "admin"){

        if($email == "admin@gmail.com"
        && $password == "admin123"){

            $_SESSION['admin'] = true;
            $_SESSION['admin_name'] = "Administrator";

            header("Location:index.php");
            exit();
        }
        else{
            $error = "Invalid Admin Credentials";
        }
    }

    // ==============================
    // STUDENT LOGIN
    // ==============================

    else{

        $sql = "
        SELECT * FROM student
        WHERE email='$email'
        ";

        $result = $conn->query($sql);

        if($result->num_rows > 0){

            $row = $result->fetch_assoc();

            if($password == $row['password']){

                $_SESSION['student_id']
                = $row['student_id'];

                $_SESSION['student_name']
                = $row['name'];

                header("Location:index.php");
                exit();
            }
            else{
                $error = "Wrong Password";
            }
        }
        else{
            $error = "User Not Found";
        }
    }
}

?>

<!DOCTYPE html>
<html>
<head>

<title>Login</title>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Segoe UI;
}

body{
    background:linear-gradient(to right,#2563eb,#1d4ed8);
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
}

.login-box{
    width:420px;
    background:white;
    padding:40px;
    border-radius:12px;
    box-shadow:0px 0px 15px rgba(0,0,0,0.2);
}

.login-box h1{
    text-align:center;
    margin-bottom:30px;
    color:#0f172a;
}

input,select{
    width:100%;
    padding:12px;
    margin-top:15px;
    border:1px solid #ccc;
    border-radius:5px;
}

button{
    width:100%;
    padding:12px;
    margin-top:20px;
    border:none;
    background:#2563eb;
    color:white;
    border-radius:5px;
    cursor:pointer;
    font-size:16px;
}

button:hover{
    background:#1d4ed8;
}

.error{
    color:red;
    margin-top:15px;
    text-align:center;
}

</style>

</head>

<body>

<div class="login-box">

<h1>Club Management Login</h1>

<form method="POST">

<select name="role" required>
<option value="student">Login as Student</option>
<option value="admin">Login as Admin</option>
</select>

<input type="email"
name="email"
placeholder="Enter Email"
required>

<input type="password"
name="password"
placeholder="Enter Password"
required>

<button name="login">
Login
</button>

</form>

<div class="error">
<?php echo $error; ?>
</div>

</div>

</body>
</html>