<?php
session_start();
include 'database.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit();
}

$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $storedPassword = $row['password'];

    if (!password_verify($password, $storedPassword) && !hash_equals($storedPassword, $password)) {
        echo "<script>alert('Invalid email or password'); window.location.href='login.php';</script>";
        exit();
    }

    $_SESSION['user_name'] = $row['full_name'];
    $_SESSION['user_email'] = $row['email'];
    $_SESSION['profile_created'] = $row['created_at'] ?? date('Y-m-d');

    header("Location: Front_End.php");
    exit();
} else {
    echo "<script>alert('Invalid email or password'); window.location.href='login.php';</script>";
}
?>
