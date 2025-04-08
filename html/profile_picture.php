<?php
session_start();
require_once 'api_client.php';

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header('HTTP/1.0 403 Forbidden');
    exit('Access denied');
}

// Get the requested filename
$filename = isset($_GET['file']) ? basename($_GET['file']) : 'default_profile.png';

// Validate filename format (userid_filename.ext)
if (!preg_match('/^\d+_[a-zA-Z0-9_-]+\.[a-zA-Z]{3,4}$/', $filename) && $filename !== 'default_profile.png') {
    header('HTTP/1.0 400 Bad Request');
    exit('Invalid filename format');
}

// Set the path to the profile pictures directory
$upload_dir = 'uploads/profile_pictures/';
$file_path = $upload_dir . $filename;

// Check if file exists
if (!file_exists($file_path)) {
    // If file doesn't exist, use default profile picture
    $file_path = $upload_dir . 'default_profile.png';
}

// Get file information
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime_type = finfo_file($finfo, $file_path);
finfo_close($finfo);

// Set appropriate headers
header('Content-Type: ' . $mime_type);
header('Content-Length: ' . filesize($file_path));
header('Cache-Control: public, max-age=31536000');

// Output the file
readfile($file_path); 