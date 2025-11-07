<?php
// Simpan register menggunakan PDO
require_once __DIR__ . '/db.php';
session_start();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role = trim($_POST['role'] ?? 'studentservice');

    if ($fullName && $email && $password) {
        // Pengecekan email
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $message = "❌ Email sudah terdaftar.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $ins = $pdo->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)");
            if ($ins->execute([$fullName, $email, $hash, $role])) {
                $message = "✅ Registrasi berhasil. Silakan login.";
            } else {
                $message = "❌ Registrasi gagal.";
            }
        }
    } else {
        $message = "Isi semua kolom.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registrasi Akun</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f4f6f9;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }
    .register-container {
      background: #fff;
      padding: 30px 40px;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      width: 360px;
      text-align: center;
    }
    .register-container h2 {
      margin-bottom: 20px;
      color: #333;
    }
    .form-group {
      margin-bottom: 15px;
      text-align: left;
    }
    .form-group label {
      display: block;
      font-size: 14px;
      margin-bottom: 5px;
      color: #444;
    }
    .form-group input, .form-group select {
      width: 100%;
      padding: 10px;
      border-radius: 8px;
      border: 1px solid #ccc;
      outline: none;
      font-size: 14px;
    }
    .form-group input:focus, .form-group select:focus {
      border-color: #007bff;
    }
    .btn {
      background: #007bff;
      color: #fff;
      border: none;
      padding: 12px;
      width: 100%;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
      transition: 0.3s;
    }
    .btn:hover {
      background: #0056b3;
    }
    .message {
      margin-top: 15px;
      font-size: 14px;
    }
  </style>
</head>
<body>
  <div class="register-container">
    <h2>Registrasi Akun</h2>
    <form method="POST">
      <div class="form-group">
        <label for="full_name">Nama Lengkap</label>
        <input type="text" id="full_name" name="full_name" placeholder="Masukkan nama lengkap" required>
      </div>
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="Masukkan email" required>
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Masukkan password" required>
      </div>
      <div class="form-group">
        <label for="role">Role</label>
        <select id="role" name="role" required>
          <option value="superadmin">Superadmin</option>
          <option value="admin">Admin</option>
          <option value="studentservice">Student Service</option>
          <option value="studentsupport">Student Support</option>
          <option value="studentdevelopment">Student Development</option>
        </select>
      </div>
      <button type="submit" class="btn">Daftar</button>
    </form>
    <?php if ($message): ?>
      <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
  </div>
</body>
</html>
