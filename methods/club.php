<?php
function club($id = 1)
{
    $result = $GLOBALS["DatabaseConnection"]->query("SELECT * FROM club WHERE id=?", $id);
    if(is_null($result)) exit(json_encode((object) ["error" => "404:ClubNotFound", "description" => "Club not found!"], JSON_NUMERIC_CHECK+JSON_PRETTY_PRINT));
    $result = mysqli_fetch_assoc($result);
    return [
      "id" => $result["id"],
      "name" => $result["name"],
      "about" => $result["about"],
      "owner" => $result["authorid"],
      "verified" => $result["verify"] > 0,
      "ban" => ($result["ban"] ? $result["comment_ban"] : false),
      "about" => $result["about"],
      "dates" => $result["type"] > 0 ? [
        "begin" => date("l, d-M-y H:i:s T", $result["datestart"]),
        "end" => date("l, d-M-y H:i:s T", $result["datefinish"]),
      ] : NULL,
      "nsfw" => $result["maturecontent"] > 0,
      "avatar" => [
        "any" => $result["avatar"],
      ],
    ];
}