<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Storage Routes
|--------------------------------------------------------------------------
|
| This route serves storage files directly via PHP, bypassing the need for
| symbolic links. This is useful for shared hosting environments where
| the storage folder is not accessible via the public web server.
|
*/

Route::get('imagestorage/{path}', function ($path) {
    if (!Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
        return response()->json(['error' => 'File not found'], 404);
    }
    return Illuminate\Support\Facades\Storage::disk('public')->response($path);
})->where('path', '.*'); // Allow slashes in filename
