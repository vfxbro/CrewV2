<?php
require_once 'includes/config.php';
if (!DEBUG_MODE) {
    http_response_code(404);
    exit('Not Found');
}

$logfile = 'logs/error.log';
$log_content = file_exists($logfile) ? file_get_contents($logfile) : 'No log entries.';
$log_content = htmlspecialchars($log_content, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Information</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container my-5">
        <h1 class="h3 mb-4">Debug Information</h1>
        <pre class="p-3 bg-dark text-light rounded" style="white-space: pre-wrap;">
<?php echo $log_content; ?>
        </pre>
    </div>
</body>
</html>
