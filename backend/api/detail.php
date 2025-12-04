<?php
/**
 * Movie Detail API Endpoint
 * Get movie details by slug
 */

require_once '../config/config.php';
require_once '../config/cors.php';
require_once '../helpers/ApiHelper.php';

handleCORS();

try {
    // Get parameters
    $slug = $_GET['slug'] ?? '';
    
    if (empty($slug)) {
        echo json_encode(ApiHelper::errorResponse('Slug is required', 400));
        exit;
    }
    
    // Build endpoint
    $endpoint = '/phim/' . $slug;
    
    // Call API
    $response = ApiHelper::get($endpoint);
    
    // Return response
    echo json_encode($response);
    
} catch (Exception $e) {
    echo json_encode(ApiHelper::errorResponse($e->getMessage()));
}
