<?php

defined('ABSPATH') or die("Well, That's smart guess, But sry I'm smarter than you (=");

require_once __DIR__ . "/information.php";
require_once __DIR__ . "/dpxlib-beta.php";

function InitDB() {

    global $conn;

    if ($conn === null) {

        global $db_info;

        $conn = new mysqli($db_info['host'], $db_info['user'], $db_info['pass'], $db_info['name']);

        if ($conn->connect_error) {

            die($conn->connect_error);

        }else{

            $conn->set_charset("utf8mb4");

        }

    }

}

function W3CAPI($endpoint) {
    
    global $w3c_backend_api;

    if (str_starts_with($endpoint, '/')) {
        
        $endpoint = $w3c_backend_api . $endpoint;

    }else{

        $endpoint = "$w3c_backend_api/$endpoint";

    }

    $curl = curl_init($endpoint);
    curl_setopt($curl, CURLOPT_URL, $endpoint);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($curl);
    curl_close($curl);

    return json_decode($result, true);

}

function UserLink($user_id, $name) {

    return "<a href='tg://user?id=$user_id'>$name</a>";

}

function FaToEn($string) {

    return strtr($string, array('۰'=>'0', '۱'=>'1', '۲'=>'2', '۳'=>'3', '۴'=>'4', '۵'=>'5', '۶'=>'6', '۷'=>'7', '۸'=>'8', '۹'=>'9', '٠'=>'0', '١'=>'1', '٢'=>'2', '٣'=>'3', '٤'=>'4', '٥'=>'5', '٦'=>'6', '٧'=>'7', '٨'=>'8', '٩'=>'9'));

}

function UserCreateIfNotExist($user_id) {

    InitDB();

    global $conn;
    
    if ($conn->query("SELECT `id` FROM `Users` WHERE `user_id` = '$user_id'")->num_rows === 0) {
        
        $conn->query("INSERT INTO `Users`(`user_id`) VALUES ('$user_id')");

    }

}

function GetUserDataBy($field, $by, $value) {

    InitDB();

    global $conn;

    return $conn->query("SELECT `$field` FROM `Users` WHERE `$by` = '$value'")->fetch_assoc()[$field] ?? null;

}