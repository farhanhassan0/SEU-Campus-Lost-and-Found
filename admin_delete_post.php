
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

$item_id = 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_id = isset($_POST["id"]) ? intval($_POST["id"]) : 0;
} else {
    $item_id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;
}

if ($item_id > 0) {

    $stmt = $conn->prepare(
        "DELETE FROM items WHERE id = ?"
    );

    $stmt->bind_param("i", $item_id);
    $stmt->execute();

    $stmt->close();
}

header("Location: admin_posts.php");
exit;

?>
