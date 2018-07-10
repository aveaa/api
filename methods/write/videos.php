<?php
function put_videos()
{
    $video = json_decode(file_get_contents("php://input"));
    $video = $video->video;
    
    $GLOBALS["DatabaseConnection"]->query("INSERT INTO video (name, id_video, about, aid, `date`, category) VALUES (?, ?, ?, ?, UNIX_TIMESTAMP(), 0)", ...[
      $video->title,
      preg_replace("/https:\/\/www\.youtube\.com\/watch\?v=/", "", $video->url),
      $video->description,
      $GLOBALS["USER"]->id,
    ]);
}