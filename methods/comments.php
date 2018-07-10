<?php
$GLOBALS["c_targets"] = [ #Признавайтесь, кто тот дебик который посоздавал 5 разных таблиц для одной цели, а?
  "post" => ["comments", "idpost", "iduser"],
  "group" => ["gcomments", "idpost", "iduser"],
  "note" => ["ncomments", "idnote", "idauthor"],
  "photo" => ["pcomments", "idphoto", "aid"],
  "video" => ["vcomments", "idvideo", "idauthor"], #на этом моменте я точно понял что мне пора завести тянку
];

function comments($target, $id)
{
    if(is_null($GLOBALS["c_targets"][$target])) exit(json_encode((object) ["error" => "400:InvalidTarget", "description" => "$target is not a valid selector."], 160));
    
    $result = $GLOBALS["DatabaseConnection"]->query("SELECT id FROM ".$GLOBALS["c_targets"][$target][0]." WHERE ".$GLOBALS["c_targets"][$target][1]."=?", $id);
    if(is_null($result)) exit(json_encode((object) ["error" => "404:NoComments", "description" => "This entity, sadly, has no comments."], 160));
    
    $comments = [];
    while($row = mysqli_fetch_assoc($result)) $comments[]=$row;
    return $comments;
}

function comment($target, $id)
{
    if(is_null($GLOBALS["c_targets"][$target])) exit(json_encode((object) ["error" => "400:InvalidTarget", "description" => "$target is not a valid selector."], 160));
    
    $result = $GLOBALS["DatabaseConnection"]->query("SELECT * FROM ".$GLOBALS["c_targets"][$target][0]." WHERE id=?", $id);
    if(is_null($result)) exit(json_encode((object) ["error" => "404:CommentNotFound", "description" => "Comment not found!"], 160));
    $result = mysqli_fetch_assoc($result);
    
    return [
      "id" => $result["id"],
      "target" => $result[$GLOBALS["c_targets"][$target][1]],
      "author" => $result[$GLOBALS["c_targets"][$target][2]],
      "created" => date("l, d-M-y H:i:s T", $result["date"]),
      "text" => $result["text"],
    ];
}

function put_comments($target, $id = 0)
{
    if($target === "group")
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
    
    $comment = json_decode(file_get_contents("php://input"));
    
    $sql = "INSERT INTO ".$GLOBALS["c_targets"][$target][0]." (".implode(", ", [
      $GLOBALS["c_targets"][$target][1],
      $GLOBALS["c_targets"][$target][2],
    ]).", text, `date`) VALUES (?, ?, ?, UNIX_TIMESTAMP())";
    $GLOBALS["DatabaseConnection"]->query($sql, ...[
      $comment->comment->target,
      $GLOBALS["USER"]->id,
      $comment->comment->text,
    ]);
}