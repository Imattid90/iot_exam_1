<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

require 'config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Sensor Logs</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        h2 {
            color: #333;
            margin-bottom: 20px;
        }
        
        .nav {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        
        .nav a {
            margin-right: 15px;
            text-decoration: none;
            color: #007bff;
        }
        
        .nav a:hover {
            text-decoration: underline;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background-color: #4CAF50;
            color: white;
            font-weight: bold;
        }
        
        tr:hover {
            background-color: #f5f5f5;
        }
        
        .critical {
            background-color: #ffebee !important;
            border-left: 4px solid #f44336;
        }
        
        .critical td {
            color: #c62828;
            font-weight: bold;
        }
        
        .critical:hover {
            background-color: #ffcdd2 !important;
        }
        
        .timestamp {
            font-family: monospace;
            font-size: 0.9em;
        }
        
        .alert-badge {
            display: inline-block;
            background-color: #f44336;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 0.7em;
            margin-left: 5px;
            font-weight: normal;
        }
        
        .stats {
            background-color: #e3f2fd;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="nav">
            <strong>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></strong> |
            <a href="dashboard.php">Dashboard</a> |
            <a href="view_logs.php">View Logs</a> |
            <a href="add_device.php">Add Device</a> |
            <a href="logout.php">Logout</a>
        </div>
        
        <h2>Sensor Logs - Last 50 Readings</h2>
        
        <?php
        try {
            // Query to get last 50 sensor readings with device information
            $stmt = $pdo->prepare("
                SELECT 
                    sl.id,
                    sl.temperature,
                    sl.humidity,
                    sl.soil_moisture,
                    sl.logged_at,
                    d.device_name,
                    d.location,
                    d.status
                FROM sensor_logs sl
                INNER JOIN devices d ON sl.device_id = d.id
                ORDER BY sl.logged_at DESC
                LIMIT 50
            ");
            
            $stmt->execute();
            $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Calculate statistics
            $total_logs = count($logs);
            $critical_alerts = 0;
            $avg_temp = 0;
            $avg_humidity = 0;
            
            if ($total_logs > 0) {
                $temp_sum = 0;
                $humidity_sum = 0;
                
                foreach ($logs as $log) {
                    if ($log['temperature'] > 35.0) {
                        $critical_alerts++;
                    }
                    $temp_sum += floatval($log['temperature']);
                    $humidity_sum += floatval($log['humidity']);
                }
                
                $avg_temp = $temp_sum / $total_logs;
                $avg_humidity = $humidity_sum / $total_logs;
            }
            ?>
            
            <div class="stats">
                <strong>Statistics:</strong><br>
                Total logs: <?php echo $total_logs; ?><br>
                Critical alerts (Temperature > 35.0°C): <?php echo $critical_alerts; ?><br>
                Average Temperature: <?php echo number_format($avg_temp, 2); ?>°C<br>
                Average Humidity: <?php echo number_format($avg_humidity, 2); ?>%
            </div>
            
            <?php if (empty($logs)): ?>
                <div class="no-data">
                    No sensor data available yet. Please add devices and send data through the API endpoint.
                </div>
            <?php else: ?>
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Device Name</th>
                                <th>Location</th>
                                <th>Temperature (°C)</th>
                                <th>Humidity (%)</th>
                                <th>Soil Moisture</th>
                                <th>Logged At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($logs as $log): 
                                $is_critical = ($log['temperature'] > 35.0);
                            ?>
                                <tr class="<?php echo $is_critical ? 'critical' : ''; ?>">
                                    <td><?php echo htmlspecialchars($log['id']); ?></td>
                                    <td>
                                        <?php echo htmlspecialchars($log['device_name']); ?>
                                        <?php if ($log['status'] !== 'active'): ?>
                                            <span style="color: #ff9800; font-size: 0.8em;">(<?php echo $log['status']; ?>)</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($log['location'] ?: 'N/A'); ?></td>
                                    <td>
                                        <?php echo number_format($log['temperature'], 2); ?>°C
                                        <?php if ($is_critical): ?>
                                            <span class="alert-badge">CRITICAL</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo number_format($log['humidity'], 2); ?>%</td>
                                    <td>
                                        <?php 
                                        $moisture = $log['soil_moisture'];
                                        if ($moisture !== null && $moisture !== '') {
                                            echo $moisture;
                                            if ($moisture < 30) {
                                                echo ' <span style="color: #ff9800;">(Dry)</span>';
                                            } elseif ($moisture > 70) {
                                                echo ' <span style="color: #2196f3;">(Wet)</span>';
                                            }
                                        } else {
                                            echo 'N/A';
                                        }
                                        ?>
                                    </td>
                                    <td class="timestamp">
                                        <?php 
                                        $date = new DateTime($log['logged_at']);
                                        echo $date->format('Y-m-d H:i:s');
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div style="margin-top: 20px; font-size: 0.9em; color: #666;">
                    <strong>Note:</strong> Rows with red highlight indicate critical temperature alerts (> 35.0°C).
                </div>
            <?php endif; ?>
            
        <?php
        } catch (PDOException $e) {
            echo '<div style="color: red; padding: 10px; background-color: #ffebee; border-radius: 5px;">';
            echo '<strong>Database Error:</strong> ' . htmlspecialchars($e->getMessage());
            echo '</div>';
        }
        ?>
        
    </div>
</body>
</html>