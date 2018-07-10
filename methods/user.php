<?php
function user($id = 1)
{
    $result = $GLOBALS["DatabaseConnection"]->query("SELECT * FROM users WHERE id=?", $id);
    if(is_null($result)) exit(json_encode((object) ["error" => "404:UserNotFound", "description" => "User not found!"], 160));
    $result = mysqli_fetch_assoc($result);
    return [
      "id" => $result["id"],
      "name" => [$result["name"], $result["surname"]],
      "gender" => $result["gender"] === 2 ? "female" : "male",
      "user_group" => $result["groupu"] === 2 ? "ADMINISTRATOR" : ($result["groupu"] === 1 ? "TESTER" : "USER"),
      "verified" => $result["verify"] > 0,
      "ban" => ($result["ban"] ? $result["comment_ban"] : false),
      "dead" => $result["is_dead"],
      "rating" => $result["rating"],
      "have_donated" => $result["is_donater"],
      "status" => $result["status"],
      "last_action" => date("l, d-M-y H:i:s T", $result["lastonline"]),
      "avatar" => [
        "any" => $result["avatar"],
      ],
    ];
}