<?php
session_start();
include 'database.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit();
}

// ── Input validation ──────────────────────────────────────────
$email    = trim($_POST['email']    ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($email) || empty($password)) {
    echo "<script>alert('Please fill in all fields.'); window.location.href='login.php';</script>";
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('Please enter a valid email address.'); window.location.href='login.php';</script>";
    exit();
}

if (strlen($password) < 6) {
    echo "<script>alert('Invalid email or password.'); window.location.href='login.php';</script>";
    exit();
}

// ── Database lookup ───────────────────────────────────────────
$sql  = "SELECT id, full_name, email, password, created_at FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    // FIXED: only use password_verify — no plaintext fallback
    if (!password_verify($password, $row['password'])) {
        echo "<script>alert('Invalid email or password.'); window.location.href='login.php';</script>";
        exit();
    }

    // Regenerate session ID on login to prevent session fixation
    session_regenerate_id(true);

    $_SESSION['user_id']         = $row['id'];
    $_SESSION['user_name']       = $row['full_name'];
    $_SESSION['user_email']      = $row['email'];
    $_SESSION['profile_created'] = $row['created_at'] ?? date('Y-m-d');

    header("Location: Front_End.php");
    exit();
} else {
    echo "<script>alert('Invalid email or password.'); window.location.href='login.php';</script>";
}
?>