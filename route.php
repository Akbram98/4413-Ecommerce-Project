<?php
// Include the controller
include_once 'controller/authController.php';

// Initialize the controller
$controller = new AuthController();

// Retrieve the URL path
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Remove the base part of the URL (adjust this depending on your directory structure)
$path = str_replace('/eecs4413/route', '', $requestUri);

// Determine the HTTP request method and match the appropriate action
switch ($path) {
    case '/signinUser':
        $controller->signinUser();
        break;
    case '/registerUser':
        $controller->registerUser();
        break;
    case '/update':
        $controller->update();
        break;
    case '/delete':
        $controller->delete();
        break;
    case '/getUser':
        $controller->getUser();
        break;
    default:
        echo json_encode(["status" => "error", "message" => "Invalid endpoint."]);
        break;
}
?>
