<?php
function videos($id = 1)
{
    $result = $GLOBALS["DatabaseConnection"]->query("SELECT id FROM video WHERE aid=?", $id);
    if(is_null($result)) exit(json_encode((object) ["error" => "404:NoVideosFound", "description" => "This entity, sadly, has no videos."], 160));
    $videos = [];
    while($row = mysqli_fetch_assoc($result)) $videos[]=$row;
    return $videos;
}