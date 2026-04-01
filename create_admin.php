<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // CHECK IF USER EXISTS
    $check = $pdo->prepare("SELECT id FROM admins WHERE username = ?");
    $check->execute([$username]);

    if ($check->rowCount() > 0) {
        echo "Username already exists!";
    } else {
        $stmt = $pdo->prepare("INSERT INTO admins (username, password_hash) VALUES (?, ?)");
        $stmt->execute([$username, $password_hash]);

        echo "Admin created successfully!";
    }
}
?>