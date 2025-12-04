<?php
/**
 * API Helper Class
 * Handle API calls to phimapi.com with caching
 */

class ApiHelper {
    
    /**
     * Make GET request to API
     */
    public static function get($endpoint, $params = []) {
        $url = API_BASE_URL . $endpoint;
        
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        
        // Check cache first
        if (CACHE_ENABLED) {
            $cacheKey = md5($url);
            $cachedData = self::getCache($cacheKey);
            
            if ($cachedData !== null) {
                return $cachedData;
            }
        }
        
        // Make API request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            return self::errorResponse("API Error: $error");
        }
        
        if ($httpCode !== 200) {
            return self::errorResponse("API returned status code: $httpCode");
        }
        
        $data = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return self::errorResponse("Invalid JSON response");
        }
        
        // Save to cache
        if (CACHE_ENABLED && $data) {
            self::setCache($cacheKey, $data);
        }
        
        return $data;
    }
    
    /**
     * Get cached data
     */
    private static function getCache($key) {
        $cacheFile = CACHE_DIR . $key . '.json';
        
        if (!file_exists($cacheFile)) {
            return null;
        }
        
        $cacheTime = filemtime($cacheFile);
        
        if (time() - $cacheTime > CACHE_TIME) {
            unlink($cacheFile);
            return null;
        }
        
        $data = file_get_contents($cacheFile);
        return json_decode($data, true);
    }
    
    /**
     * Set cache data
     */
    private static function setCache($key, $data) {
        if (!is_dir(CACHE_DIR)) {
            mkdir(CACHE_DIR, 0755, true);
        }
        
        $cacheFile = CACHE_DIR . $key . '.json';
        file_put_contents($cacheFile, json_encode($data));
    }
    
    /**
     * Return error response
     */
    public static function errorResponse($message, $code = 500) {
        return [
            'status' => false,
            'error' => $message,
            'code' => $code
        ];
    }
    
    /**
     * Return success response
     */
    public static function successResponse($data) {
        return [
            'status' => true,
            'data' => $data
        ];
    }
}
