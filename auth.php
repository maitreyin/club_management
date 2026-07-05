<?php

require_once 'db.php';

session_start();

// ======================================
// SAFE OUTPUT
// ======================================

function safe($value){
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

// ======================================
// REDIRECT
// ======================================

function redirect($url){
    header("Location: $url");
    exit();
}

// ======================================
// CHECK POST METHOD
// ======================================

function is_post(){
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

// ======================================
// CHECK LOGIN
// ======================================

function is_logged_in(){
    return isset($_SESSION['student_id']);
}

// ======================================
// LOGIN USER
// ======================================

function login_user($identity, $password){

    $query = "
    SELECT *
    FROM student
    WHERE email = ?
    OR roll_no = ?
    ";

    $stmt = db_query(
        $query,
        "ss",
        [$identity, $identity]
    );

    $result = $stmt->get_result();

    if($result->num_rows === 1){

        $user = $result->fetch_assoc();

        // VERIFY PASSWORD

        if(password_verify($password, $user['password'])){

            $_SESSION['student_id'] = $user['student_id'];
            $_SESSION['student_name'] = $user['name'];

            return true;
        }
    }

    return false;
}

// ======================================
// LOGOUT
// ======================================

function logout_user(){

    session_unset();

    session_destroy();
}

// ======================================
// REGISTER USER
// ======================================

function register_user(
    $roll,
    $name,
    $email,
    $phone,
    $department,
    $year,
    $password,
    &$error
){

    // CHECK EXISTING EMAIL OR ROLL

    $check = db_query(
        "SELECT * FROM student
        WHERE email = ?
        OR roll_no = ?",
        "ss",
        [$email, $roll]
    );

    $result = $check->get_result();

    if($result->num_rows > 0){

        $error = "Email or Roll Number already exists.";

        return false;
    }

    // HASH PASSWORD

    $hashed = password_hash($password, PASSWORD_DEFAULT);

    // INSERT USER

    db_query(
        "INSERT INTO student
        (roll_no,name,email,phone,department,year,password)
        VALUES (?,?,?,?,?,?,?)",
        "sssssis",
        [
            $roll,
            $name,
            $email,
            $phone,
            $department,
            $year,
            $hashed
        ]
    );

    return true;
}

?>