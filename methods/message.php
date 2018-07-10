<?php
function message($id)
{
    if($GLOBALS["USER"] == NULL) exit(json_encode((object) ["error" => "401:SecurityException", "description" => "This method requires authorization!"], 416));
    
    
    $result = $GLOBALS["DatabaseConnection"]->query("SELECT * FROM messages WHERE id=?", $id);
    if(is_null($result)) exit(json_encode((object) ["error" => "404:MessageNotFound", "description" => "Message not found!"], 160));
    $result = mysqli_fetch_assoc($result);
    if($GLOBALS["USER"]->id != $result["id1"] && $GLOBALS["USER"]->id != $result["id2"])
    {
        exit(json_encode((object) ["error" => "403:SecurityException", "description" => "You can not read messages, that is not yours!"], 416));
    }
    
    return [
      "id" => $result["id"],
      "from" => $result["id1"],
      "to" => $result["id2"],
      "sent" => date("l, d-M-y H:i:s T", $result["date"]),
      "red" => $result["readed"] > 0,
      "content" => [
        "subject" => $result["topic"],
        "text" => $result["text"],
      ],
    ];
}