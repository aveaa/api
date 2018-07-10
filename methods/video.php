<?php
function video($id = 1)
{
    $result = $GLOBALS["DatabaseConnection"]->query("SELECT * FROM video WHERE id=?", $id);
    if(is_null($result)) exit(json_encode((object) ["error" => "404:VideoNotFound", "description" => "Video not found!"], 160));
    $result = mysqli_fetch_assoc($result);
    return [
      "id" => $result["id"],
      "name" => $result["name"],
      "author" => $result["aid"],
      "url" => "https://youtu.be/".$result["id_video"],
      "description" => $result["about"],
      "category" => ["GENERAL", "MUSIC"][$result["category"]],
      "banned" => (boolean) $result["ban"],
      "created" => date("l, d-M-y H:i:s T", $result["date"]),
    ];
}