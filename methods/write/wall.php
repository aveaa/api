<?php
function put_wall($id)
{
    $group = [];
    
    if($id > 0)
    {
        $user = $GLOBALS["DatabaseConnection"]->query("SELECT closedwall FROM users WHERE id=?", $id);
        if(is_null($user)) exit(json_encode((object) ["error" => "404:NoGroupFound", "description" => "There is no user like this!"], 160));
        
        if(mysqli_fetch_array($user)[0] > 0 && $GLOBALS["USER"]->id != $id) exit(json_encode((object) [ #если у юзера стена закрыта
          "error" => "403:SecurityException",
          "description" => "You can not post to this user!"
        ], 416));
    } else if($id < 0)
    {
        $group = $GLOBALS["DatabaseConnection"]->query("SELECT authorid, closed, wall FROM club WHERE id=?", abs($id));
        if(is_null($group)) exit(json_encode((object) ["error" => "404:NoGroupFound", "description" => "There is no group like this!"], 160));
        $group = mysqli_fetch_assoc($group);
        
        if($group["closed"] > 0) #проверить членство группы
        {
            $result = $GLOBALS["DatabaseConnection"]->query("SELECT * FROM clubsub WHERE id1=? AND id2=?", $GLOBALS["USER"]->id, abs($id));
            if(is_null($result)) exit(json_encode((object) [
              "error" => "403:SecurityException",
              "description" => "This group is private and we couldn't verify your subscription!"
            ], 160));
        }
        if($group["wall"] > 0 && $GLOBALS["USER"]->id != $group["authorid"])
        {
            exit(json_encode((object) ["error" => "403:SecurityException", "description" => "You can not post to this group!"], 416));
        }
    }
    
    $post = json_decode(file_get_contents("php://input"));
    if($id < 0 && $post->post->group && $GLOBALS["USER"]->id != $group["authorid"])
    {
        exit(json_encode((object) ["error" => "403:SecurityException", "description" => "You can not post as this group!"], 416));
    }
    $sql = $id > 0 ? "INSERT INTO wall (iduser, idwall, text, `date`) VALUES (?, ?, ?, UNIX_TIMESTAMP())" : "INSERT INTO gpost (iduser, idwall, text, `date`, bygroup) VALUES (?, ?, ?, UNIX_TIMESTAMP(), ?)";
    $post_arr = [$GLOBALS["USER"]->id, abs($id), $post->post->text,];
    $id < 0 ? $post_arr[] = $post->post->group : !1;
    
    $GLOBALS["DatabaseConnection"]->query($sql, ...$post_arr);
}