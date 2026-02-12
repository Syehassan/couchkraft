<?php

namespace App\Support;

use Spatie\MediaLibrary\Support\UrlGenerator\DefaultUrlGenerator;

class CustomUrlGenerator extends DefaultUrlGenerator
{
    public function getUrl(): string
    {
        $url = parent::getUrl();
        
        // Force storage URL from config
        $storageUrl = config('app.storage_url') ?: config('app.url');
        $appUrl = config('app.url');
        
        // Replace APP_URL with STORAGE_URL in the generated URL
        if ($storageUrl !== $appUrl) {
            $url = str_replace($appUrl, $storageUrl, $url);
        }
        
        return $url;
    }
}
