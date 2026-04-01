<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
?>

<h2>Welcome <?php echo $_SESSION['admin_username']; ?></h2>
<a href="logout.php">Logout</a>