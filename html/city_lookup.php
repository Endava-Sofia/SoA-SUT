<?php
require_once 'api_client.php';

function getCitiesByCountry($country) {
    $url = '/cities/'.$country;
    $rest_response = CallAPI("GET", $url);
    return $rest_response;
}

// Only process city lookup if this file is accessed directly
if (basename($_SERVER['PHP_SELF']) == 'city_lookup.php' && isset($_POST['country'])) {
    $country = $_POST['country'];
    echo getCitiesByCountry($country);
}
