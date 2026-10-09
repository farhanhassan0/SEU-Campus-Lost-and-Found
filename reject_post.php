<?php

session_start();
require_once "config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION["role"] != "admin") {
    header("Location: student_dashboard.php");
    exit;
}

$item_id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

$stmt = $conn->prepare(
    "UPDATE items
     SET status = 'Rejected'
     WHERE id = ? AND status = 'Pending'"
);

$stmt->bind_param("i", $item_id);
$stmt->execute();

$stmt->close();

header("Location: admin_posts.php");
exit;

?>