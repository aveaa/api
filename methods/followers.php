<?php
function followers($id = 1)
{
    $result = $GLOBALS["DatabaseConnection"]->query("SELECT id1 FROM subs WHERE id2=?", $id);
    if(is_null($result)) exit(json_encode((object) ["error" => "404:NoFriendsFound", "description" => "This user, sadly, has no followers."], JSON_NUMERIC_CHECK+JSON_PRETTY_PRINT));
    $xres = [];
    while($row = mysqli_fetch_array($result)) $xres[]=$row[0];
    return array_values($xres);
}