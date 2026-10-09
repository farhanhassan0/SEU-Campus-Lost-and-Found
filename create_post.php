
<?php

session_start();
require_once "config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $user_id = $_SESSION["user_id"];
    $item_name = trim($_POST["item_name"]);
    $type = $_POST["type"];
    $category = trim($_POST["category"]);
    $description = trim($_POST["description"]);
    $location = trim($_POST["location"]);

    if ($item_name == "" || $category == "" || $location == "") {

        $message = "Please fill in all required fields.";

    } elseif ($type != "Lost" && $type != "Found") {

        $message = "Invalid item type.";

    } else {

        $image_name = "";

        if (isset($_FILES["image"]) && $_FILES["image"]["error"] != UPLOAD_ERR_NO_FILE) {

            if ($_FILES["image"]["error"] != UPLOAD_ERR_OK) {

                $message = "Image upload failed.";

            } elseif ($_FILES["image"]["size"] > 5 * 1024 * 1024) {

                $message = "Image size must be less than 5 MB.";

            } else {

                $allowed_types = [
                    "image/jpeg",
                    "image/png"
                ];

                $file_type = mime_content_type($_FILES["image"]["tmp_name"]);

                if (!in_array($file_type, $allowed_types)) {

                    $message = "Only JPG, JPEG and PNG images are allowed.";

                } else {

                    $extension = strtolower(
                        pathinfo(
                            $_FILES["image"]["name"],
                            PATHINFO_EXTENSION
                        )
                    );

                    if (!in_array($extension, ["jpg", "jpeg", "png"])) {

                        $message = "Invalid image file.";

                    } else {

                        $image_name = uniqid() . "." . $extension;

                        if (!move_uploaded_file(
                            $_FILES["image"]["tmp_name"],
                            "uploads/" . $image_name
                        )) {

                            $message = "Failed to save the image.";
                            $image_name = "";
                        }
                    }
                }
            }
        }

        if ($message == "") {

            $stmt = $conn->prepare(
                "INSERT INTO items
                (user_id, item_name, type, category, description, location, image)
                VALUES (?, ?, ?, ?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "issssss",
                $user_id,
                $item_name,
                $type,
                $category,
                $description,
                $location,
                $image_name
            );

            if ($stmt->execute()) {

                $message = "Post submitted successfully. Waiting for admin approval.";

            } else {

                if (!empty($image_name) && file_exists("uploads/" . $image_name)) {
                    unlink("uploads/" . $image_name);
                }

                $message = "Failed to create post.";
            }

            $stmt->close();
        }
    }
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Create Post - Campus Lost & Found</title>

    <link rel="stylesheet" href="style.css">

    <style>

        .post-page {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .post-form-section {
            flex: 1;
            padding: 45px 0;
        }

        .post-form-box {
            width: 90%;
            max-width: 650px;
            margin: auto;
            background: white;
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.08);
        }

        .post-form-box h1 {
            text-align: center;
            margin-top: 0;
        }

        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
        }

        .post-form-box label {
            display: block;
            font-weight: bold;
            margin-bottom: 7px;
        }

        .post-form-box input[type="text"],
        .post-form-box select,
        .post-form-box textarea {
            display: block;
            width: 100%;
            padding: 12px;
            margin: 0 0 20px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
            background: white;
            box-sizing: border-box;
        }

        .post-form-box textarea {
            resize: vertical;
        }

        .post-form-box input[type="text"]:focus,
        .post-form-box select:focus,
        .post-form-box textarea:focus {
            outline: none;
            border-color: #2563eb;
        }

        .location-box {
            display: block;
            width: 100%;
            height: 45px;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
            box-sizing: border-box;
        }

        .location-box:focus {
            outline: none;
            border-color: #2563eb;
        }

        .upload-area {
            width: 100%;
            border: 2px dashed #cbd5e1;
            border-radius: 8px;
            padding: 25px;
            text-align: center;
            margin-bottom: 8px;
            background: #f8fafc;
            box-sizing: border-box;
        }

        .upload-area input[type="file"] {
            width: 100%;
            font-size: 14px;
        }

        .image-note {
            color: #777;
            font-size: 13px;
            margin: 8px 0 20px;
        }

        .message {
            background: #eef6ff;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            text-align: center;
        }

        .post-submit {
            width: 100%;
            margin: 0;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
        }

        .post-footer {
            background: #1f2937;
            color: white;
            text-align: center;
            padding: 18px 10px;
        }

        .post-footer p {
            margin: 4px 0;
        }

        @media (max-width: 600px) {

            .post-form-box {
                padding: 25px 20px;
            }

        }

    </style>

</head>

<body>

<div class="post-page">


    <nav class="navbar">

        <div class="container">

            <a href="index.php" class="logo">
                Campus Lost & Found
            </a>

            <div class="nav-links">

                <a href="index.php">Home</a>
                <a href="items.php">Items</a>
                <a href="search.php">Search</a>
                <a href="student_dashboard.php">Dashboard</a>
                <a href="logout.php">Logout</a>

            </div>

        </div>

    </nav>


    <section class="post-form-section">

        <div class="post-form-box">

            <h1>
                Create Lost/Found Post
            </h1>

            <p class="subtitle">
                Report a lost or found item on campus.
            </p>


            <?php if ($message != ""): ?>

                <div class="message">

                    <?php echo htmlspecialchars($message); ?>

                </div>

            <?php endif; ?>


            <form method="POST" enctype="multipart/form-data">


                <label for="item_name">
                    Item Name
                </label>

                <input
                    type="text"
                    id="item_name"
                    name="item_name"
                    placeholder="Enter item name"
                    required
                >


                <label for="type">
                    Type
                </label>

                <select
                    id="type"
                    name="type"
                    required
                >

                    <option value="">
                        Select Type
                    </option>

                    <option value="Lost">
                        Lost
                    </option>

                    <option value="Found">
                        Found
                    </option>

                </select>


                <label for="category">
                    Category
                </label>

                <input
                    type="text"
                    id="category"
                    name="category"
                    placeholder="Example: Mobile, Wallet, ID Card"
                    required
                >


                <label for="description">
                    Description
                </label>

                <textarea
                    id="description"
                    name="description"
                    rows="5"
                    placeholder="Describe the item..."
                ></textarea>


                <label for="location">
                    Location
                </label>

                <input
                    type="text"
                    id="location"
                    name="location"
                    class="location-box"
                    placeholder="Example: Southeast University, Seminar Hall"
                    required
                >


                <label>
                    Item Picture
                </label>

                <div class="upload-area">

                    <input
                        type="file"
                        name="image"
                        accept=".jpg,.jpeg,.png"
                    >

                </div>

                <p class="image-note">
                    Accepted formats: JPG, JPEG and PNG. Maximum size: 5 MB.
                </p>


                <button
                    type="submit"
                    class="btn post-submit"
                >
                    Submit Post
                </button>


            </form>


            <a
                href="student_dashboard.php"
                class="back-link"
            >
                ← Back to Dashboard
            </a>

        </div>

    </section>


    <footer class="post-footer">

        <p>
            © 2026 Campus Lost & Found System
        </p>

        <p>
            Developed by Farhan Hassan
        </p>

    </footer>


</div>

</body>

</html>
