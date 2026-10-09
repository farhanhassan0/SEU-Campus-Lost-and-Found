<?php

session_start();
require_once "config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$item_id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

$stmt = $conn->prepare(
    "DELETE FROM items WHERE id = ? AND user_id = ?"
);

$stmt->bind_param("ii", $item_id, $user_id);
$stmt->execute();

$stmt->close();

header("Location: my_posts.php");
exit;

?>