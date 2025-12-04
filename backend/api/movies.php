<?php
/**
 * Movies API Endpoint
 * Get list of movies by type
 */

require_once '../config/config.php';
require_once '../config/cors.php';
require_once '../helpers/ApiHelper.php';

handleCORS();

try {
    // Get parameters
    $type = $_GET['type'] ?? 'phim-bo';
    $page = $_GET['page'] ?? 1;
    $limit = $_GET['limit'] ?? 20;
    $sortField = $_GET['sort_field'] ?? 'modified.time';
    $sortType = $_GET['sort_type'] ?? 'desc';
    $category = $_GET['category'] ?? '';
    $country = $_GET['country'] ?? '';
    $year = $_GET['year'] ?? '';
    
    // Build endpoint
    $endpoint = '/v1/api/danh-sach/' . $type;
    
    // Build params
    $params = [
        'page' => $page,
        'limit' => $limit,
        'sort_field' => $sortField,
        'sort_type' => $sortType
    ];
    
    if ($category) $params['category'] = $category;
    if ($country) $params['country'] = $country;
    if ($year) $params['year'] = $year;
    
    // Call API
    $response = ApiHelper::get($endpoint, $params);
    
    // Return response
    echo json_encode($response);
    
} catch (Exception $e) {
    echo json_encode(ApiHelper::errorResponse($e->getMessage()));
}
