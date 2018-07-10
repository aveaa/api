<?php
function put_photo($target)
{
    if($target < 0)
    {
        $group = $GLOBALS["DatabaseConnection"]->query("SELECT aid FROM galbums WHERE id=?", abs($target));
        if(is_null($group)) exit(json_encode((object) ["error" => "404:NoAlbumFound", "description" => "There is no album like this!"], 160));
        $author = $GLOBALS["DatabaseConnection"]->query("SELECT authorid FROM club WHERE id=?", mysqli_fetch_array($group)[0]);
        if(is_null($author)) exit(json_encode((object) ["error" => "404:NoGroupFound", "description" => "There is no group like this!"], 160));
        if($GLOBALS["USER"]->id != mysqli_fetch_array($author)[0]) exit(json_encode((object) [
          "error" => "403:SecurityException",
          "description" => "You do not possess enough rights to post as this group!"
        ], 160));
    } else {
        $author = $GLOBALS["DatabaseConnection"]->query("SELECT aid FROM albums WHERE id=?", $target);
        if(is_null($author)) exit(json_encode((object) ["error" => "404:NoAlbumFound", "description" => "There is no album like this!"], 160));
        if($GLOBALS["USER"]->id != mysqli_fetch_array($author)[0]) exit(json_encode((object) [
          "error" => "403:SecurityException",
          "description" => "You do not possess enough rights to post as this user!"
        ], 160));
    }
    
    $photo = json_decode(file_get_contents("php://input"));
    $photo = $photo->photo;
    $upid = preg_replace("/nfs:\/\/|\@l-lsoc\.cf/", "", $photo->server);
    $server = $GLOBALS["DatabaseConnection"]->query("SELECT `key` FROM upload_servers WHERE name=?", $upid);
    if(is_null($server)) exit(json_encode((object) [
      "error" => "410:ServerMissing",
      "description" => "There is no user like $upid on this mediaserver!"
    ], 160));
    $GLOBALS["DatabaseConnection"]->query("DELETE FROM upload_servers WHERE name=?", $upid);
    
    $name = "wx-upload-1-".base64_encode(time().sha1(time()*rand(1, 22222)));
    $file = "../content/cdn/$name.jpeg";
    file_put_contents($file, hex2bin($photo->photo));
    
    $sql = "INSERT INTO photo (aid, image, note, album, galbum, user, `date`) VALUES (?, ?, ?, ?, ?, ?, UNIX_TIMESTAMP())";
    $GLOBALS["DatabaseConnection"]->query($sql, ...[
      $target < 0 ? 0 : $GLOBALS["USER"]->id,
      "content/cdn/$name.jpeg",
      $photo->description,
      $target < 0 ? 0 : $target,
      $target > 0 ? 0 : abs($target),
      $target > 0 ? 0 : $GLOBALS["USER"]->id,
    ]);
}