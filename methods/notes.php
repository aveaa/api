<?php
function notes($id)
{
    $result = $GLOBALS["DatabaseConnection"]->query("SELECT id FROM note WHERE aid=?", $id);
    if(is_null($result)) exit(json_encode((object) ["error" => "404:NoNotesFound", "description" => "This user, sadly, has no notes."], 160));
    $notes = [];
    while($row = mysqli_fetch_assoc($result)) $notes[]=$row;
    return $notes;
}