<?php
require '../config.php';

// SET JSON HEADER
header("Content-Type: application/json");

// ONLY POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Method Not Allowed"]);
    exit();
}

// GET JSON INPUT
$data = json_decode(file_get_contents("php://input"), true);

// VALIDATE INPUT
if (!$data || !isset($data['api_key'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing api_key or invalid JSON"]);
    exit();
}

$api_key = $data['api_key'];
$temperature = $data['temperature'] ?? null;
$humidity = $data['humidity'] ?? null;
$soil_moisture = $data['soil_moisture'] ?? null;

// CHECK DEVICE (ONLY ACTIVE)
$stmt = $pdo->prepare("SELECT id FROM devices WHERE api_key = ? AND status = 'active'");
$stmt->execute([$api_key]);
$device = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$device) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized: Invalid API Key"]);
    exit();
}

// INSERT DATA
$insert = $pdo->prepare("
    INSERT INTO sensor_logs (device_id, temperature, humidity, soil_moisture)
    VALUES (?, ?, ?, ?)
");

$insert->execute([
    $device['id'],
    $temperature,
    $humidity,
    $soil_moisture
]);

// SUCCESS RESPONSE
http_response_code(200);
echo json_encode([
    "message" => "Data saved successfully",
    "device_id" => $device['id'],
    "timestamp" => date("Y-m-d H:i:s")
]);