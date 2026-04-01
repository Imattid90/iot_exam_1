========================================
IoT Exam System - Setup Instructions
========================================

PREREQUISITES:
- XAMPP or WAMP installed
- PHP 7.4 or higher
- MySQL/MariaDB

SETUP STEPS:

1. Copy all PHP files to your web server directory:
   - XAMPP: C:\xampp\htdocs\iot_exam\
   - WAMP: C:\wamp\www\iot_exam\

2. Start Apache and MySQL services

3. Import the database:
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Create database: iot_exam_db
   - Import the following SQL files in order:
     a) admins.sql
     b) devices.sql  
     c) sensor_logs.sql

4. Create an admin account:
   - Open browser and go to: http://localhost/iot_exam/create_admin.php
   - Create your admin username and password
   - Note: Password is automatically hashed for security

5. Login to the system:
   - Go to: http://localhost/iot_exam/login.php
   - Use the admin credentials you created

6. Add a device:
   - After login, go to Add Device page
   - Fill in device name and location
   - Save to generate UUID and API key

7. Test with Postman:
   - Import the Postman collection: IoT_Exam_System.postman_collection.json
   - Replace YOUR_API_KEY_HERE with the API key from your device
   - Send POST requests to: http://localhost/iot_exam/ingest_data.php

API ENDPOINT DETAILS:
- URL: http://localhost/iot_exam/ingest_data.php
- Method: POST
- Headers: Content-Type: application/json
- Body Format:
{
    "api_key": "your_device_api_key_here",
    "temperature": 23.5,
    "humidity": 65.0,
    "soil_moisture": 45
}

FILES STRUCTURE:
- config.php - Database configuration
- functions.php - Helper functions
- login.php - Admin login
- dashboard.php - Admin dashboard
- add_device.php - Add new devices
- view_logs.php - View sensor logs (with red highlights for >35°C)
- ingest_data.php - API endpoint for data ingestion
- style.css - Basic styling
- create_admin.php - Create admin account
- logout.php - Logout functionality

TROUBLESHOOTING:

1. Database connection error:
   - Check config.php credentials
   - Ensure MySQL is running

2. 404 Not Found:
   - Verify files are in correct directory
   - Check URL path

3. API returns 401 Unauthorized:
   - Verify API key exists in devices table
   - Check device status is 'active'

4. No data showing in view_logs.php:
   - First add a device via add_device.php
   - Send test data using Postman
   - Check sensor_logs table in phpMyAdmin

TESTING WITH POSTMAN:

1. Import the provided JSON collection
2. Get API key from devices table or after adding device
3. Update the "YOUR_API_KEY_HERE" in Postman requests
4. Send requests and check responses
5. Verify data appears in view_logs.php

CRITICAL ALERT TESTING:
- Send temperature > 35.0 to see red highlighting in logs
- Example: "temperature": 38.5

========================================