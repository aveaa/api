<html>
<head>
  <meta charset="utf-8" />
  <link rel="stylesheet" href="https://unpkg.com/unnamed" />
</head>
<center>
<?php
session_start();
require "DatabaseModel.php";
use Unionity\OpenVK4\core\Database\DatabaseModel;
$DatabaseConnection = new DatabaseModel("***", "***", "***", "***");

if($_SESSION["loginin"] !== "1") exit("<h1>Войдите, чтобы получить ключ доступа</h1>");

$mykey = $DatabaseConnection->query("SELECT token FROM api_tokens WHERE owner=?", $_SESSION["id"]);
if(!is_null($mykey))
{
    exit("<h1>У вас уже есть ключ</h1><button class='btn btn-rounded gradient-purple' onclick='prompt(\"Ваш ключ\", \"".mysqli_fetch_array($mykey)[0]."\");'>Показать</button><br/><button onclick='window.open(\"/api2/docs.html\", \"_top\");' class='btn btn-rounded gradient-orange' style='margin-top: 5px;'>К Документации</button>");
} else {
    echo "<h1>Получить ключ</h1><form method='POST'><input type='hidden' name='boundary' value='".rand()."' /><button class='btn btn-rounded gradient-purple'>Получить ключ</button></form>";
}

if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["boundary"]))
{
    $DatabaseConnection->query("INSERT INTO api_tokens (token,owner) VALUES (?,?)", hash("sha512", $_SESSION["id"].time().rand(0, time())), $_SESSION["id"]);
    header("HTTP/1.1 301 Redirect");
    header("Location: /api2/register.php");
}
?>
</center>
</html>