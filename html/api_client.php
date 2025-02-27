<?php

function CallAPI($method, $url, $data = false)
{
    $curl = curl_init();
    $is_json_payload = false;

    switch ($method)
    {
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);
            $is_json_payload = true;
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

    if ($data && $is_json_payload) {
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "Content-Length: " . strlen($data)
        ));
    }

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

    $result = curl_exec($curl);
    
    if (curl_errno($curl)) {
        $error = curl_error($curl);
        curl_close($curl);
        return json_encode(array("error" => $error));
    }
    
    curl_close($curl);
    return $result;
}

// Only handle city lookup if it's a direct API call
if (isset($_POST['country']) && !isset($_POST['Update'])) {
    require_once 'city_lookup.php';
    echo getCitiesByCountry($_POST['country']);
}
?>