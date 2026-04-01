<?php
// Quick test script - run this to insert sample data
require 'config.php';

// Get first active device
$stmt = $pdo->prepare("SELECT id, api_key FROM devices WHERE status = 'active' LIMIT 1");
$stmt->execute();
$device = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$device) {
    die("No active device found. Please add a device first.");
}

// Insert sample data
$samples = [
    ['temperature' => 22.5, 'humidity' => 65.0, 'soil_moisture' => 45],
    ['temperature' => 24.8, 'humidity' => 58.3, 'soil_moisture' => 38],
    ['temperature' => 36.2, 'humidity' => 62.0, 'soil_moisture' => 52], // Critical alert!
    ['temperature' => 21.0, 'humidity' => 71.5, 'soil_moisture' => 65],
    ['temperature' => 37.5, 'humidity' => 55.0, 'soil_moisture' => 28], // Critical alert!
    ['temperature' => 26.3, 'humidity' => 48.0, 'soil_moisture' => 22],
];

foreach ($samples as $sample) {
    $insert = $pdo->prepare("
        INSERT INTO sensor_logs (device_id, temperature, humidity, soil_moisture)
        VALUES (?, ?, ?, ?)
    ");
    $insert->execute([
        $device['id'],
        $sample['temperature'],
        $sample['humidity'],
        $sample['soil_moisture']
    ]);
}

echo "Sample data inserted successfully!\n";
echo "Check view_logs.php to see the critical alerts highlighted in red.\n";
?>