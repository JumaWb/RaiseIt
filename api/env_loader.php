<?php
function loadEnv($file = __DIR__ . '/.env') {
    if (!file_exists($file)) {
        die("Error: .env file not found.");
    }

    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            putenv(trim($key) . '=' . trim($value));
        }
    }
}

// Load environment variables
loadEnv();
?>
