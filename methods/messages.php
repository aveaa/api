<?php
function messages($from, $to, $offset = 0)
{
    if($GLOBALS["USER"] == NULL) exit(json_encode((object) ["error" => "401:SecurityException", "description" => "This method requires authorization!"], 416));
    
    if($GLOBALS["USER"]->id != $from && $GLOBALS["USER"]->id != $to)
    {
        exit(json_encode((object) ["error" => "403:SecurityException", "description" => "You can not read messages, that is not yours!"], 416));
    }
    
    $result = $GLOBALS["DatabaseConnection"]->query("SELECT id FROM messages WHERE id1=? AND id2=? AND id>=?", $from, $to, $offset);
    if(is_null($result)) exit(json_encode((object) ["error" => "410:NoMessages", "description" => "There are no messages in this conversation."], 160));
    $msgs = [];
    while($row = mysqli_fetch_assoc($result)) $msgs[]=$row;
    return $msgs;
}