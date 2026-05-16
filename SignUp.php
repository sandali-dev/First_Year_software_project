<?php
session_start();
include 'database.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: register.php");
    exit();
}

// ── Input validation 
$name     = trim($_POST['name']     ?? '');
$email    = trim($_POST['email']    ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($name) || empty($email) || empty($password)) {
    echo "<script>alert('Please fill in all fields.'); window.location.href='register.php';</script>";
    exit();
}

if (strlen($name) < 2 || strlen($name) > 100) {
    echo "<script>alert('Name must be between 2 and 100 characters.'); window.location.href='register.php';</script>";
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('Please enter a valid email address.'); window.location.href='register.php';</script>";
    exit();
}

if (strlen($email) > 120) {
    echo "<script>alert('Email address is too long.'); window.location.href='register.php';</script>";
    exit();
}

if (strlen($password) < 6) {
    echo "<script>alert('Password must be at least 6 characters long.'); window.location.href='register.php';</script>";
    exit();
}

// ── Hash password and insert ──────────────────────────────────
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$sql  = "INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $name, $email, $hashedPassword);

if ($stmt->execute()) {
    // Regenerate session ID on registration to prevent session fixation
    session_regenerate_id(true);

    $_SESSION['user_id']         = $conn->insert_id;
    $_SESSION['user_name']       = $name;
    $_SESSION['user_email']      = $email;
    $_SESSION['profile_created'] = date('Y-m-d');

    header("Location: Front_End.php");
    exit();
} else {
    if ($stmt->errno === 1062) {
        echo "<script>alert('This email is already registered. Please sign in.'); window.location.href='login.php';</script>";
    } else {
        // FIXED: log the real error server-side, show a generic message to the user
        error_log("Registration DB error: " . $stmt->error);
        echo "<script>alert('Registration failed. Please try again.'); window.location.href='register.php';</script>";
    }
}
?>