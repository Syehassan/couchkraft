<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Helper to find file iteratively
function findFile($filename, $depth = 3) {
    $dir = __DIR__;
    for ($i = 0; $i < $depth; $i++) {
        $path = $dir . '/' . $filename;
        if (file_exists($path)) {
            return $path;
        }
        $dir = dirname($dir);
    }
    return false;
}

echo "<h1>Storage Debugger</h1>";

// Locate autoload
$autoload = findFile('vendor/autoload.php');
if (!$autoload) {
    die("Error: Could not find vendor/autoload.php. Checked up to 3 levels up from " . __DIR__);
}
require $autoload;

// Locate bootstrap/app.php
$bootstrap = findFile('bootstrap/app.php');
if (!$bootstrap) {
    die("Error: Could not find bootstrap/app.php. Checked up to 3 levels up from " . __DIR__);
}
$app = require_once $bootstrap;

// Prepare the kernel to handle requests (boots service providers)
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Test file path (adjust based on the URL you provided)
$testPath = 'media/8/Screenshot-2026-02-11-184320.png';

echo "<h3>Looking for: $testPath</h3>";
echo "<b>Generated URL:</b> " . Storage::disk('public')->url($testPath) . "<br>";
echo "(It should contain 'imagestorage', not 'storage')<br><hr>";

try {
    // Check various disks
    $disks = ['public', 'local'];
    foreach ($disks as $disk) {
        $exists = Storage::disk($disk)->exists($testPath);
        $path = Storage::disk($disk)->path($testPath);
        echo "<b>Disk '$disk':</b> " . ($exists ? 'FOUND' : 'Missing') . " ($path)<br>";
    }
    
    // Recursive search for ANY file in media
    echo "<hr><h3>Listing All Media Files</h3>";
    $mediaPath = storage_path('app/public/media');
    if (is_dir($mediaPath)) {
        $files = scandir($mediaPath);
        echo "Contents of $mediaPath:<br>";
        echo "<pre>" . print_r($files, true) . "</pre>";
        
        // Deep scan to find the latest file
        echo "<b>Deep Scan for Images:</b><br>";
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($mediaPath));
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                echo $file->getPathname() . "<br>";
            }
        }
    } else {
        echo "Media folder not found at $mediaPath<br>";
    }
    
    echo "<hr><h3>Route Cache Status</h3>";
    try {
        echo "Running 'route:clear'...<br>";
        \Illuminate\Support\Facades\Artisan::call('route:clear');
        echo "Output: " . \Illuminate\Support\Facades\Artisan::output() . "<br>";
        
        echo "Running 'config:clear'...<br>";
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        echo "Output: " . \Illuminate\Support\Facades\Artisan::output() . "<br>";
    } catch (Exception $e) {
        echo "Artisan Error: " . $e->getMessage();
    }
    
    echo "<hr><h3>Environment Check</h3>";
    echo "Filesystem Root: " . config('filesystems.disks.public.root') . "<br>";
    echo "Public URL: " . config('filesystems.disks.public.url') . "<br>";
    
} catch (Exception $e) {
    echo "<b>Error:</b> " . $e->getMessage();
}
