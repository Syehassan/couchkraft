<?php

// Basic security check - you might want to remove this line or secure it if you leave the file
// But for a one-time use script on a dev site, it's okay-ish. 
// Ideally, delete after use.

echo "<h1>Storage Link Utility</h1>";

$target = $_SERVER['DOCUMENT_ROOT'] . '/../storage/app/public';
$shortcut = $_SERVER['DOCUMENT_ROOT'] . '/storage';

echo "Target: " . $target . "<br>";
echo "Shortcut: " . $shortcut . "<br>";

if (file_exists($shortcut)) {
    echo "Check: Link already exists.<br>";
    if (is_link($shortcut)) {
        echo "Type: Symbolic Link.<br>";
        echo "Points to: " . readlink($shortcut) . "<br>";
    } else {
        echo "Type: Directory/File (Not a link).<br>";
        echo "Action: Attempting to rename existing directory to storage_backup...<br>";
        if (rename($shortcut, $shortcut . '_backup_' . time())) {
            echo "Success: Renamed via PHP.<br>";
        } else {
            echo "Error: Could not rename. Please delete 'public/storage' manually via FTP/File Manager.<br>";
            die();
        }
    }
} else {
    echo "Check: Link does not exist.<br>";
}

echo "Action: Creating symlink...<br>";

try {
    // Try PHP native symlink first
    if (symlink($target, $shortcut)) {
        echo "<strong>Success! Symlink created.</strong><br>";
        echo "You can now delete this file.";
    } else {
        echo "<strong>Failed:</strong> PHP symlink() function refused. Try the Artisan method below.<br>";
    }
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<h3>Artisan Method (Laravel)</h3>";

// Bootstrap Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Create Kernel
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

try {
    echo "Running 'storage:link' via Artisan...<br>";
    \Illuminate\Support\Facades\Artisan::call('storage:link');
    echo "Output: " . \Illuminate\Support\Facades\Artisan::output() . "<br>";
} catch (Exception $e) {
    echo "Artisan Error: " . $e->getMessage();
}
