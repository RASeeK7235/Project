

<?php

// Supabase credentials
$supabaseUrl = 'https://lvsogpbcuauofmjsqrde.supabase.co';

$supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Imx2c29ncGJjdWF1b2ZtanNxcmRlIiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTc2NTI5MjA3NCwiZXhwIjoyMDgwODY4MDc0fQ.8hZlKHc6i3sxWBzVsK2TUFLWD-xCOxabXqtUFZFMntI' ;


// Main CURL function

function supabaseRequest($endpoint, $method = "GET", $data = null) {
    global $supabaseUrl, $supabaseKey;

    $url = $supabaseUrl . $endpoint; //yesle full url banauxa

    $ch = curl_init($url); //curl session start garxa

    $headers = [
        "apikey: $supabaseKey",
        "Authorization: Bearer $supabaseKey",
        "Content-Type: application/json",
        "Prefer: return=representation"
    ];

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);//header haru set garxa
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);//output lai screen ma print huna didaina
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);//method set garxa

    if ($data) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));//yedi data cha bhane tyo json ma convert garera set garxa
    }

    $response = curl_exec($ch);//request execute garxa
    $err = curl_error($ch);
    curl_close($ch);//curl session close garxa

    if ($err) {
        return ["error" => $err];
    }

    return json_decode($response, true);//response lai php suitable form ma return garxa
}


// Sub functions


// Fetch data (GET)
function fetchData($table, $filter = "") {//filter is condition(optional)
    $endpoint = "/rest/v1/$table" . ($filter ? "?$filter" : "");
    return supabaseRequest($endpoint, "GET");
}

// Add new data (POST)
function addData($table, $data) {
    $endpoint = "/rest/v1/$table";
    return supabaseRequest($endpoint, "POST", $data);
}

// Update data (PATCH)
function updateData($table, $filter, $data) {
    $endpoint = "/rest/v1/$table?$filter";
    return supabaseRequest($endpoint, "PATCH", $data);
}

// Delete data (DELETE)
function deleteData($table, $filter) {
    $endpoint = "/rest/v1/$table?$filter";
    return supabaseRequest($endpoint, "DELETE");
}



?>
