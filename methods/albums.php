<?php
function albums($id = 1)
{
    $result = $GLOBALS["DatabaseConnection"]->query("SELECT id, name, note, `date` FROM ".($id > 0 ? "" : "g")."albums WHERE aid=?", abs($id));
    if(is_null($result)) exit(json_encode((object) ["error" => "404:NoAlbumsFound", "description" => "This entity, sadly, has no albums."], 160));
    $albums = [];
    while($row = mysqli_fetch_assoc($result))
    {
      $albums[]=[
        "id" => $row["id"],
        "name" => $row["name"],
        "description" => $row["note"],
        "created" => date("l, d-M-y H:i:s T", $row["date"]),
      ];
    };
    return $albums;
}