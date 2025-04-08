<?php

function getApiBaseUrl() {
    // Check if running locally
    $hostname = $_SERVER['HTTP_HOST'];
    if (strpos($hostname, '192.168.127.1') !== false 
    || strpos($hostname, 'localhost') !== false
    || strpos($hostname, '127.0.0.1') !== false) {
        return 'http://rest:5000';
    }
    // In Azure or other environments, use the service name
    return 'http://rest';
}

function CallAPI($method, $url, $data = false, $file = false)
{
    // If the URL doesn't start with http, assume it's a relative path
    if (strpos($url, 'http') !== 0) {
        $url = getApiBaseUrl() . $url;
        error_log("API Call URL: " . $url);
    }
    
    $curl = curl_init();
    $is_json_payload = false;
    $is_file_upload = false;

    switch ($method)
    {
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);
            if ($file) {
                $is_file_upload = true;
            } else {
                $is_json_payload = true;
            }
            break;
        case "PUT":
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
            $is_json_payload = true;
            break;
        default:
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
            if ($data)
                $url = sprintf("%s?%s", $url, http_build_query($data));
    }

    // Log the request details
    error_log("API Request - Method: $method, URL: $url");
    if ($data) {
        error_log("API Request Data: " . print_r($data, true));
    }
    if ($file) {
        error_log("API Request File: " . print_r($file, true));
    }

    if ($is_file_upload && $file) {
        // Handle file upload
        if (function_exists('curl_file_create')) {
            $cfile = curl_file_create(
                $file['tmp_name'],
                $file['type'],
                $file['name']
            );
        } else {
            $cfile = '@' . realpath($file['tmp_name']) . 
                    ';filename=' . $file['name'] . 
                    ';type=' . $file['type'];
        }
        
        $post_data = array('file' => $cfile);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: multipart/form-data'
        ));
        error_log("Uploading file with curl_file_create");
    } elseif ($data && $is_json_payload) {
        if (!is_string($data)) {
            $data = json_encode($data);
        }
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "Content-Length: " . strlen($data)
        ));
    }

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

    // Add timeout to prevent hanging
    curl_setopt($curl, CURLOPT_TIMEOUT, 30);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);

    $result = curl_exec($curl);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    
    // Log the response
    error_log("API Response Code: " . $http_code);
    error_log("API Raw Response: " . $result);
    
    if (curl_errno($curl)) {
        $error = curl_error($curl);
        error_log("API Error: " . $error);
        curl_close($curl);
        return json_encode(array("error" => $error));
    }

    // Check for non-200 response codes
    if ($http_code !== 200) {
        error_log("API Non-200 Response: Code $http_code, Response: $result");
        return $result; // Return the actual error response from the API
    }
    
    curl_close($curl);
    
    // Validate JSON response
    if (!empty($result)) {
        $json_valid = json_decode($result);
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("API Invalid JSON Response: " . json_last_error_msg());
            return json_encode(array("error" => "Invalid JSON response from API"));
        }
    }
    
    return $result;
}

// Only handle city lookup if it's a direct API call
if (isset($_POST['country']) && !isset($_POST['Update'])) {
    require_once 'city_lookup.php';
    echo getCitiesByCountry($_POST['country']);
}
?>