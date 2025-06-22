<?php
echo "<h1>PHP Test</h1>";
echo "<p>PHP Version: " . PHP_VERSION . "</p>";
echo "<p>Current Time: " . date('Y-m-d H:i:s') . "</p>";
echo "<p>PDO MySQL Available: " . (extension_loaded('pdo_mysql') ? 'Yes' : 'No') . "</p>";

// Test basic database connection
if (isset($_POST['test_db'])) {
    $host = $_POST['host'];
    $dbname = $_POST['dbname'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        echo "<div style='color: green; border: 1px solid green; padding: 10px; margin: 10px 0;'>";
        echo "<strong>✓ Database connection successful!</strong>";
        echo "</div>";
    } catch (Exception $e) {
        echo "<div style='color: red; border: 1px solid red; padding: 10px; margin: 10px 0;'>";
        echo "<strong>✗ Database connection failed:</strong><br>" . $e->getMessage();
        echo "</div>";
    }
}
?>

<form method="post" style="margin: 20px 0; padding: 20px; border: 1px solid #ccc;">
    <h3>Test Database Connection</h3>
    <p><label>Host: <input type="text" name="host" value="localhost" required></label></p>
    <p><label>Database: <input type="text" name="dbname" required></label></p>
    <p><label>Username: <input type="text" name="username" required></label></p>
    <p><label>Password: <input type="password" name="password"></label></p>
    <p><button type="submit" name="test_db">Test Connection</button></p>
</form>

<p><a href="install_clean.php">Go to Installation</a></p>