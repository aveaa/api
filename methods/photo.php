<?php
function photo($id = 1)
{
    $result = $GLOBALS["DatabaseConnection"]->query("SELECT * FROM photo WHERE id=?", $id);
    if(is_null($result)) exit(json_encode((object) ["error" => "404:PhotoNotFound", "description" => "Photo not found!"], 160));
    $result = mysqli_fetch_assoc($result);
    return [
      "id" => $result["id"],
      "author" => $result["aid"] != 0 ? $result["aid"] : $result["user"],
      "path" => $result["image"],
      "description" => $result["note"],
      "album" => $result["album"] != 0 ? $result["album"] : $result["galbum"],
      "created" => date("l, d-M-y H:i:s T", $result["date"]),
    ];
}