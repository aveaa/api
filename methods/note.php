<?php
function note($id = 1)
{
    $result = $GLOBALS["DatabaseConnection"]->query("SELECT * FROM note WHERE id=?", $id);
    if(is_null($result)) exit(json_encode((object) ["error" => "404:NoteNotFound", "description" => "Note not found!"], 160));
    $result = mysqli_fetch_assoc($result);
    return [
      "id" => $result["id"],
      "name" => $result["name"],
      "author" => $result["aid"],
      "edited" => $result["edited"] > 0,
      "created" => date("l, d-M-y H:i:s T", $result["date"]),
      "text" => $result["text"],
    ];
}