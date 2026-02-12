<?php

// Enable error reporting to see what's happening
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Storage Recovery Script</h1>";

// 1. Detect Base Path
$base = __DIR__ . '/storage'; 

// Check if we are in a 'public' folder and need to go up
if (basename(__DIR__) === 'public') {
   $base = __DIR__ . '/../storage';
}
// Force it to current directory if index.php exists here (common in public_html setups)
if (file_exists(__DIR__ . '/index.php')) {
    $base = __DIR__ . '/storage';
}

echo "Target Storage Path: <b>" . $base . "</b><br><hr>";

// 2. Define Folders to Create
$folders = [
    '/app',
    '/app/public', 
    '/app/private',
    '/framework',
    '/framework/cache',
    '/framework/cache/data',
    '/framework/sessions',
    '/framework/testing',
    '/framework/views',
    '/logs'
];

// 3. Create Base Folder
if (!is_dir($base)) {
    echo "Creating base 'storage' folder... ";
    if (mkdir($base, 0755, true)) {
        echo "<span style='color:green'>SUCCESS</span><br>";
    } else {
        echo "<span style='color:red'>FAILED</span> (Check permissions of current folder)<br>";
    }
} else {
    echo "Base 'storage' folder already exists.<br>";
}

// 4. Create Subfolders
foreach ($folders as $folder) {
    $path = $base . $folder;
    echo "Checking: $folder ... ";
    
    if (!is_dir($path)) {
        if (mkdir($path, 0755, true)) {
            echo "<span style='color:green'>CREATED</span><br>";
        } else {
            echo "<span style='color:red'>FAILED</span> - Could not create $path<br>";
        }
    } else {
        echo "<span style='color:blue'>EXISTS</span><br>";
    }
    
    // Attempt to set permissions ensuring write access
    @chmod($path, 0755);
}

echo "<hr>";
echo "<h3>Recovery Complete.</h3>";
echo "Please delete this file after use.";
