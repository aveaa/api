<?php
function get_upload_server()
{
    if(is_null($GLOBALS["USER"])) exit(json_encode((object) ["error" => "401:SecurityException", "description" => "Sorry, but this method requires auth!"], 160));
    
    $server = $GLOBALS["DatabaseConnection"]->query("SELECT name FROM upload_servers WHERE `key`=?", $_GET["access_token"]);
    if(!is_null($server)) return [
      "upload_server" => [
        "consumes" => [
          "*"
        ],
        "for" => $_GET["access_token"],
        "id" => mysqli_fetch_array($server)[0],
      ],
    ];
    
    $name = sha1(time()*rand(1, 20));
    $GLOBALS["DatabaseConnection"]->query("INSERT INTO upload_servers (name, `key`) VALUES(?, ?)", $name, $_GET["access_token"]);
    
    return [
      "upload_server" => [
        "consumes" => [
          "*"
        ],
        "for" => $_GET["access_token"],
        "id" => $name,
      ],
      "warnings" => [
        "You had no active upload servers, so we created one for you.",
      ],
    ];
}