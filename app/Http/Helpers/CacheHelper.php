<?php

namespace App\Http\Helpers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CacheHelper
{
    public static function getCachedData($key, $callback, $ttl = 60)
    {
        if (Cache::has($key)) {
            return Cache::get($key);
        }
        try {
            $data = $callback();
            Cache::put($key, $data, $ttl);
            return $data;
        } catch (\Throwable $th) {
            Log::error($th);
            return null;
        }
    }
}
