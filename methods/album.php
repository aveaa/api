<?php
function album($id = 1)
{
    $result = $GLOBALS["DatabaseConnection"]->query("SELECT id FROM photo WHERE ".($id > 0 ? "" : "g")."album=?", abs($id));
    if(is_null($result)) exit(json_encode((object) ["error" => "404:NoPhotosFound", "description" => "This album, sadly, has no photos."], 160));
    $photos = [];
    while($row = mysqli_fetch_assoc($result)) $photos[]=$row;
    return $photos;
}