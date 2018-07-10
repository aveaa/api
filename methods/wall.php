<?php
function wall($id = 1)
{
    if($id < 0)
    {
        $result = $GLOBALS["DatabaseConnection"]->query("SELECT closed FROM club WHERE id=?", abs($id));
        if(is_null($result)) exit(json_encode((object) ["error" => "404:NoGroupFound", "description" => "There is no group like this!"], 160));
        $result = mysqli_fetch_assoc($result);
        if($result["closed"] > 0) #начать проверку прав
        {
            if($GLOBALS["USER"] == NULL) exit(json_encode((object) ["error" => "401:SecurityException","description" => "This is group is private!"], 416));
            $result = $GLOBALS["DatabaseConnection"]->query("SELECT * FROM clubsub WHERE id1=? AND id2=?", $GLOBALS["USER"]->id, abs($id));
            if(is_null($result)) exit(json_encode((object) [
              "error" => "403:SecurityException",
              "description" => "This group is private and we couldn't verify your subscription!"
            ], 160));
        }
    }
    
    $result = $GLOBALS["DatabaseConnection"]->query("SELECT id, text FROM ".($id > 0 ? "wall" : "gpost")." WHERE idwall=?", abs($id));
    if(is_null($result)) exit(json_encode((object) ["error" => "404:NoPostsFound", "description" => "This entity, sadly, has no posts."], 160));
    $posts = [];
    while($row = mysqli_fetch_assoc($result)) $posts[]=$row;
    return $posts;
}