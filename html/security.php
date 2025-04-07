<?php
// Input validation functions
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validate_name($name) {
    return preg_match('/^[a-zA-Z\s]{2,15}$/', $name);
}

function validate_password($password) {
    return strlen($password) >= 6;
}

// Input sanitization
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?> 