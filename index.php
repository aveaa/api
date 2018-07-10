<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
require "DatabaseModel.php";
use Unionity\OpenVK4\core\Database\DatabaseModel;
$GLOBALS["DatabaseConnection"] = new DatabaseModel("***", "***", "***", "***");

define("ACTION", $_SERVER["REQUEST_METHOD"] === "POST" ? isset(apache_request_headers()["X-Http-Method-Override"]) ? isset($_GET["override_method"]) ? $_GET["override_method"] : apache_request_headers()["X-Http-Method-Override"] : "POST" : "GET");

$method = preg_replace("/\?(.+)/", "", preg_replace("/\/api\//", "", $_SERVER["REQUEST_URI"])); #strip query string

$params = array_slice(preg_split("/\//", $method), 1, -1); #убрать погрешный пустой параметр
$method = strtolower(preg_split("/\//", $method)[0]);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

require "ures.inc";
$GLOBALS["USER"] = isset($_GET["access_token"]) ? resolveUser() : NULL;

if(ACTION === "GET")
{
    if(!file_exists("methods/$method.php")) exit(json_encode((object) ["error" => "404:MethodNotFound", "description" => "$method not found!"], 416));
    require_once "methods/$method.php";
} else
{
    if($GLOBALS["USER"] == NULL) exit(json_encode((object) ["error" => "401:SecurityException", "description" => "Sorry, but all Experimental Write APIs require authorization :("], 416));
    if(!file_exists("methods/write/$method.php")) exit(json_encode((object) ["error" => "404:MethodNotFound", "description" => "$method not found!"], 416));
    require_once "methods/write/$method.php";
    $method = strtolower(ACTION)."_$method"; #я хз как это прокоментировать если честно
}

try {
    exit(json_encode((object) ["state" => "202:Success", "result" => (object) call_user_func($method, ...$params)], 416));
} catch(Exception $e) {
    exit(json_encode((object) ["error" => "500:FatalError", "description" => $e], 416));
}