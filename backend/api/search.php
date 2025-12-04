<?php
/**
 * Search API Endpoint
 * Search movies by keyword
 */

require_once '../config/config.php';
require_once '../config/cors.php';
require_once '../helpers/ApiHelper.php';

handleCORS();

try {
    // Get parameters
    $keyword = $_GET['keyword'] ?? '';
    $page = $_GET['page'] ?? 1;
    $limit = $_GET['limit'] ?? 20;
    
    if (empty($keyword)) {
        echo json_encode(ApiHelper::errorResponse('Keyword is required', 400));
        exit;
    }
    
    // Build endpoint
    $endpoint = '/v1/api/tim-kiem';
    
    // Build params
    $params = [
        'keyword' => $keyword,
        'page' => $page,
        'limit' => $limit
    ];
    
    // Call API
    $response = ApiHelper::get($endpoint, $params);
    
    // Return response
    echo json_encode($response);
    
} catch (Exception $e) {
    echo json_encode(ApiHelper::errorResponse($e->getMessage()));
}
