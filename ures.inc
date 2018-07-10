<?php
function resolveUser() {
    $result = $GLOBALS["DatabaseConnection"]->query("SELECT owner FROM api_tokens WHERE token=?", $_GET["access_token"]);
    if(is_null($result)) exit(json_encode((object) ["error" => "401:SecurityException", "description" => "Token is invalid!"], 416));
    $id = mysqli_fetch_assoc($result)["owner"];
    $user = $GLOBALS["DatabaseConnection"]->query("SELECT * FROM users WHERE id=?", $id);
    return is_null($user) ? $user : (object) mysqli_fetch_assoc($user);    
}