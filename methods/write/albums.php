<?php
function put_albums($target = 0)
{
    if($target > 0) exit(json_encode((object) ["error" => "400:InvalidTarget", "description" => "You can not upload as this user."], 160));
    if($target < 0) #чекнуть права
    {
        $author = $GLOBALS["DatabaseConnection"]->query("SELECT authorid FROM club WHERE id=?", abs($target));
        if(is_null($author)) exit(json_encode((object) ["error" => "404:NoGroupFound", "description" => "There is no group like this!"], 160));
        if($GLOBALS["USER"]->id != mysqli_fetch_array($author)[0]) exit(json_encode((object) [
          "error" => "404:SecurityException",
          "description" => "You do not posses enough rights to post as this group!"
        ], 160));
    }
    
    $album = json_decode(file_get_contents("php://input"));
    $album = $album->album;
    $sql = "INSERT INTO ".($target < 0 ? "g" : "")."albums (name, note, aid, `date`) VALUES (?, ?, ?, UNIX_TIMESTAMP())";
    $GLOBALS["DatabaseConnection"]->query($sql, $album->title, $album->description, $target < 0 ? abs($target) : $GLOBALS["USER"]->id);
}