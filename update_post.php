
<?php

session_start();
require_once "config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: my_posts.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$item_id = isset($_POST["id"]) ? intval($_POST["id"]) : 0;

$item_name = trim($_POST["item_name"]);
$type = $_POST["type"];
$category = trim($_POST["category"]);
$description = trim($_POST["description"]);
$location = trim($_POST["location"]);

if ($item_id <= 0) {
    die("Invalid post.");
}

if ($item_name == "" || $category == "" || $location == "") {
    die("Please fill in all required fields.");
}

if ($type != "Lost" && $type != "Found") {
    die("Invalid item type.");
}

$stmt = $conn->prepare(
    "SELECT image, status
     FROM items
     WHERE id = ? AND user_id = ?"
);

$stmt->bind_param("ii", $item_id, $user_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows != 1) {
    die("Post not found or you are not allowed to update this post.");
}

$item = $result->fetch_assoc();

$stmt->close();

if ($item["status"] == "Returned") {
    die("Returned posts cannot be edited.");
}

$old_image = $item["image"];
$new_image = $old_image;

if (isset($_FILES["image"]) && $_FILES["image"]["error"] != UPLOAD_ERR_NO_FILE) {

    if ($_FILES["image"]["error"] != UPLOAD_ERR_OK) {
        die("Image upload failed.");
    }

    if ($_FILES["image"]["size"] > 5 * 1024 * 1024) {
        die("Image size must be less than 5 MB.");
    }

    $allowed_types = [
        "image/jpeg",
        "image/png"
    ];

    $file_type = mime_content_type($_FILES["image"]["tmp_name"]);

    if (!in_array($file_type, $allowed_types)) {
        die("Only JPG, JPEG and PNG images are allowed.");
    }

    $extension = strtolower(
        pathinfo(
            $_FILES["image"]["name"],
            PATHINFO_EXTENSION
        )
    );

    if (!in_array($extension, ["jpg", "jpeg", "png"])) {
        die("Invalid image file.");
    }

    $new_image = uniqid() . "." . $extension;

    if (!move_uploaded_file(
        $_FILES["image"]["tmp_name"],
        "uploads/" . $new_image
    )) {
        die("Failed to save the image.");
    }

    if (!empty($old_image) && file_exists("uploads/" . $old_image)) {
        unlink("uploads/" . $old_image);
    }
}

$stmt = $conn->prepare(
    "UPDATE items
     SET item_name = ?,
         type = ?,
         category = ?,
         description = ?,
         location = ?,
         image = ?
     WHERE id = ? AND user_id = ?"
);

$stmt->bind_param(
    "ssssssii",
    $item_name,
    $type,
    $category,
    $description,
    $location,
    $new_image,
    $item_id,
    $user_id
);

if (!$stmt->execute()) {
    die("Failed to update post.");
}

$stmt->close();

header("Location: my_posts.php");
exit;

?>
