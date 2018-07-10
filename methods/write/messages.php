<?php
function put_messages($from, $to)
{
  if($GLOBALS["USER"]->id != $from) exit(json_encode((object) [
    "error" => "403:SecurityException",
    "description" => "You can not send messages as this user!"
  ], 416));
  $friendship = $GLOBALS["DatabaseConnection"]->query("SELECT id1 FROM friends WHERE id1=? AND id2=?", $from, $to);
  if(is_null($friendship)) exit(json_encode((object) [
    "error" => "403:SecurityException",
    "description" => "You can not send messages to this user!"
  ], 416));
  
  $message = json_decode(file_get_contents("php://input"));
  
  $GLOBALS["DatabaseConnection"]->query("INSERT INTO messages (id1, id2, topic, text, date) VALUES (?, ?, ?, ?, UNIX_TIMESTAMP())", ...[
    $from,
    $to,
    $message->message->subject,
    $message->message->text,
  ]);
}