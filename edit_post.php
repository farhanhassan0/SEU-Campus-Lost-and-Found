
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
    "SELECT id, item_name, type, category, description, location, image, status
     FROM items
     WHERE id = ? AND user_id = ?"
);

$stmt->bind_param("ii", $item_id, $user_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows != 1) {
    die("Post not found or you are not allowed to edit this post.");
}

$item = $result->fetch_assoc();

$stmt->close();

$message = "";

if ($item["status"] == "Returned") {
    $message = "Returned posts cannot be edited.";
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Edit Post - Campus Lost & Found</title>

    <link rel="stylesheet" href="style.css">

    <style>

        .edit-page {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .edit-section {
            flex: 1;
            padding: 45px 0;
        }

        .edit-box {
            width: 90%;
            max-width: 650px;
            margin: auto;
            background: white;
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.08);
        }

        .edit-box h1 {
            text-align: center;
            margin-top: 0;
        }

        .edit-subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
        }

        .edit-box label {
            display: block;
            font-weight: bold;
            margin-bottom: 7px;
        }

        .edit-box input[type="text"],
        .edit-box select,
        .edit-box textarea {
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

        .edit-box textarea {
            resize: vertical;
        }

        .edit-box input[type="text"]:focus,
        .edit-box select:focus,
        .edit-box textarea:focus {
            outline: none;
            border-color: #2563eb;
        }

        .current-image {
            width: 100%;
            max-height: 250px;
            object-fit: contain;
            border-radius: 8px;
            background: #f4f6f8;
            margin-bottom: 15px;
        }

        .image-upload {
            width: 100%;
            border: 2px dashed #cbd5e1;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin-bottom: 8px;
            background: #f8fafc;
            box-sizing: border-box;
        }

        .image-upload input[type="file"] {
            width: 100%;
        }

        .image-note {
            color: #777;
            font-size: 13px;
            margin: 8px 0 20px;
        }

        .status-box {
            background: #f4f6f8;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .update-btn {
            width: 100%;
            margin: 0;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
        }

        .edit-footer {
            background: #1f2937;
            color: white;
            text-align: center;
            padding: 18px 10px;
        }

        .edit-footer p {
            margin: 4px 0;
        }

        @media (max-width: 600px) {

            .edit-box {
                padding: 25px 20px;
            }

        }

    </style>

</head>

<body>

<div class="edit-page">


    <nav class="navbar">

        <div class="container">

            <a href="index.php" class="logo">
                Campus Lost & Found
            </a>

            <div class="nav-links">

                <a href="index.php">Home</a>
                <a href="items.php">Items</a>
                <a href="search.php">Search</a>
                <a href="statistics.php">Statistics</a>
                <a href="student_dashboard.php">Dashboard</a>
                <a href="logout.php">Logout</a>

            </div>

        </div>

    </nav>


    <section class="edit-section">

        <div class="edit-box">

            <h1>
                Edit Post
            </h1>

            <p class="edit-subtitle">
                Update the information of your Lost or Found item.
            </p>


            <?php if ($message != ""): ?>

                <div class="status-box">

                    <?php echo htmlspecialchars($message); ?>

                </div>

            <?php endif; ?>


            <?php if ($item["status"] != "Returned"): ?>

                <form
                    method="POST"
                    action="update_post.php"
                    enctype="multipart/form-data"
                >

                    <input
                        type="hidden"
                        name="id"
                        value="<?php echo $item["id"]; ?>"
                    >


                    <label for="item_name">
                        Item Name
                    </label>

                    <input
                        type="text"
                        id="item_name"
                        name="item_name"
                        value="<?php echo htmlspecialchars($item["item_name"]); ?>"
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

                        <option
                            value="Lost"
                            <?php echo $item["type"] == "Lost" ? "selected" : ""; ?>
                        >
                            Lost
                        </option>

                        <option
                            value="Found"
                            <?php echo $item["type"] == "Found" ? "selected" : ""; ?>
                        >
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
                        value="<?php echo htmlspecialchars($item["category"]); ?>"
                        required
                    >


                    <label for="description">
                        Description
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="5"
                    ><?php echo htmlspecialchars($item["description"]); ?></textarea>


                    <label for="location">
                        Location
                    </label>

                    <input
                        type="text"
                        id="location"
                        name="location"
                        value="<?php echo htmlspecialchars($item["location"]); ?>"
                        required
                    >


                    <?php if (!empty($item["image"])): ?>

                        <label>
                            Current Picture
                        </label>

                        <img
                            src="uploads/<?php echo htmlspecialchars($item["image"]); ?>"
                            class="current-image"
                            alt="Current Item Picture"
                        >

                    <?php endif; ?>


                    <label>
                        Change Picture
                    </label>

                    <div class="image-upload">

                        <input
                            type="file"
                            name="image"
                            accept=".jpg,.jpeg,.png"
                        >

                    </div>

                    <p class="image-note">
                        Leave empty if you do not want to change the picture.
                    </p>


                    <div class="status-box">

                        <strong>Current Status:</strong>

                        <?php echo htmlspecialchars($item["status"]); ?>

                    </div>


                    <button
                        type="submit"
                        class="btn update-btn"
                    >
                        Update Post
                    </button>

                </form>

            <?php endif; ?>


            <a
                href="my_posts.php"
                class="back-link"
            >
                ← Back to My Posts
            </a>

        </div>

    </section>


    <footer class="edit-footer">

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
