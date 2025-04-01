<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

echo json_encode([
    "status" => "success", 
    "message" => "PHP server is working!",
    "time" => date("Y-m-d H:i:s")
]);

