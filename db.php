<?php

// ======================================
// DATABASE CONFIGURATION
// FILE: db.php
// ======================================

$host = "localhost";
$user = "root";
$password = "";
$database = "club_management_system";

// ======================================
// DATABASE CONNECTION
// ======================================

$conn = new mysqli($host, $user, $password, $database);

// ======================================
// CONNECTION CHECK
// ======================================

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// ======================================
// GENERIC DATABASE QUERY FUNCTION
// ======================================

function db_query($query, $types = "", $params = []){

    global $conn;

    $stmt = $conn->prepare($query);

    // CHECK PREPARE SUCCESS

    if(!$stmt){
        die("Prepare Failed: " . $conn->error);
    }

    // BIND PARAMETERS

    if(!empty($types) && !empty($params)){

        // IMPORTANT:
        // Number of type characters
        // must match number of params

        $stmt->bind_param($types, ...$params);
    }

    // EXECUTE QUERY

    if(!$stmt->execute()){
        die("Execute Failed: " . $stmt->error);
    }

    return $stmt;
}

// ======================================
// FETCH SINGLE ROW
// ======================================

function db_fetch_one($query, $types = "", $params = []){

    $stmt = db_query($query, $types, $params);

    $result = $stmt->get_result();

    return $result->fetch_assoc();
}

// ======================================
// FETCH MULTIPLE ROWS
// ======================================

function db_fetch_all($query, $types = "", $params = []){

    $stmt = db_query($query, $types, $params);

    $result = $stmt->get_result();

    $rows = [];

    while($row = $result->fetch_assoc()){
        $rows[] = $row;
    }

    return $rows;
}

// ======================================
// CLOSE DATABASE CONNECTION
// ======================================

function db_close(){

    global $conn;

    $conn->close();
}

?>