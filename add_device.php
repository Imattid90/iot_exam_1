<?php
require 'config.php';
require 'functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $device_name = trim($_POST['device_name']);
    $location = trim($_POST['location']);

    // VALIDATION
    if (empty($device_name)) {
        echo "Device name is required!";
        exit();
    }

    if (strlen($location) > 100) {
        echo "Location must be under 100 characters!";
        exit();
    }

    $uuid = generateUUID();
    $api_key = generateApiKey();

    $stmt = $pdo->prepare("INSERT INTO devices (device_uuid, device_name, location, api_key) VALUES (?, ?, ?, ?)");
    $stmt->execute([$uuid, $device_name, $location, $api_key]);

    echo "Device created successfully!";
}
?>

<form method="POST">
    <input type="text" name="device_name" placeholder="Device Name" required>
    <input type="text" name="location" placeholder="Location (max 100 chars)">
    <button type="submit">Add Device</button>
</form>