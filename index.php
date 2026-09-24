<?php
session_start();

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

echo $requestUri;

switch ($requestUri) {

    // --- PAGE ROUTES ---
    case '/':
        require_once 'src/feature/landing-page/LandingPage.html';
        break;

    // --- 404 NOT FOUND ---
    default:
        http_response_code(404);
        echo '<h1>404 Not Found</h1>';
        break;
}