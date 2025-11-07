<?php
session_start();
require_once __DIR__ . '/db.php';

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($username) || empty($password)) {
    header("Location: ../main/login.php?error=empty_fields");
    exit;
}

$stmt = $pdo->prepare("SELECT id, email, password, role, full_name FROM users WHERE email = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();

if (!$user) {
    header("Location: ../main/login.php?error=user_not_found");
    exit;
}

if (!password_verify($password, $user['password'])) {
    header("Location: ../main/login.php?error=wrong_password");
    exit;
}

// Login sukses
$_SESSION['user_id'] = $user['id'];
$_SESSION['user_email'] = $user['email'];
$_SESSION['user_role'] = $user['role'];
$_SESSION['user_fullname'] = $user['full_name'] ?? '';

header("Location: ../main/index.php");
exit;
