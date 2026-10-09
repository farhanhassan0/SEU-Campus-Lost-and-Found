
<?php

session_start();
require_once "config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

$stmt = $conn->prepare(
    "SELECT id, item_name, type, category, description,
            location, image, status, created_at
     FROM items
     WHERE user_id = ?
     ORDER BY created_at DESC"
);

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html>

<head>

    <title>My Posts - Campus Lost & Found</title>

    <link rel="stylesheet" href="style.css">

    <style>

        .my-posts-page {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .my-posts-section {
            flex: 1;
            padding: 45px 0;
        }

        .page-title {
            text-align: center;
            margin-bottom: 10px;
        }

        .page-subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 35px;
        }

        .posts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
        }

        .post-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.08);
        }

        .post-card h2 {
            margin-top: 0;
            margin-bottom: 15px;
        }

        .post-card p {
            color: #555;
            line-height: 1.5;
            margin: 8px 0;
        }

        .post-image {
            width: 100%;
            height: 220px;
            object-fit: contain;
            border-radius: 8px;
            margin-bottom: 18px;
            background: #f4f6f8;
        }

        .status {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
            margin-top: 8px;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-approved {
            background: #d4edda;
            color: #155724;
        }

        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }

        .status-returned {
            background: #d1ecf1;
            color: #0c5460;
        }

        .post-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 18px;
        }

        .post-actions .btn {
            margin: 0;
        }

        .delete-btn {
            display: inline-block;
            padding: 12px 22px;
            background: #dc2626;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }

        .delete-btn:hover {
            background: #b91c1c;
        }

        .empty-card {
            background: white;
            padding: 40px;
            text-align: center;
            border-radius: 12px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.08);
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 35px;
        }

        .my-posts-footer {
            background: #1f2937;
            color: white;
            text-align: center;
            padding: 18px 10px;
        }

        .my-posts-footer p {
            margin: 4px 0;
        }

        @media (max-width: 600px) {

            .my-posts-section {
                padding: 30px 0;
            }

            .post-card {
                padding: 20px;
            }

            .post-actions {
                flex-direction: column;
            }

            .post-actions .btn,
            .delete-btn {
                width: 100%;
                text-align: center;
            }

        }

    </style>

</head>

<body>

<div class="my-posts-page">


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


    <section class="my-posts-section">

        <div class="container">

            <h1 class="page-title">
                My Posts
            </h1>

            <p class="page-subtitle">
                View and manage the items you have posted.
            </p>


            <?php if ($result->num_rows == 0): ?>

                <div class="empty-card">

                    <h2>
                        No Posts Yet
                    </h2>

                    <p>
                        You have not created any Lost or Found posts yet.
                    </p>

                    <a
                        href="create_post.php"
                        class="btn"
                    >
                        Create Your First Post
                    </a>

                </div>


            <?php else: ?>


                <div class="posts-grid">


                    <?php while ($item = $result->fetch_assoc()): ?>

                        <div class="post-card">


                            <?php if (!empty($item["image"])): ?>

                                <img
                                    src="uploads/<?php echo htmlspecialchars($item["image"]); ?>"
                                    class="post-image"
                                    alt="Item Picture"
                                >

                            <?php endif; ?>


                            <h2>
                                <?php echo htmlspecialchars($item["item_name"]); ?>
                            </h2>


                            <p>
                                <strong>Type:</strong>
                                <?php echo htmlspecialchars($item["type"]); ?>
                            </p>


                            <p>
                                <strong>Category:</strong>
                                <?php echo htmlspecialchars($item["category"]); ?>
                            </p>


                            <p>
                                <strong>Description:</strong>
                                <?php echo htmlspecialchars($item["description"]); ?>
                            </p>


                            <p>
                                <strong>Location:</strong>
                                <?php echo htmlspecialchars($item["location"]); ?>
                            </p>


                            <p>
                                <strong>Posted On:</strong>
                                <?php echo htmlspecialchars($item["created_at"]); ?>
                            </p>


                            <span
                                class="status status-<?php echo strtolower($item["status"]); ?>"
                            >
                                <?php echo htmlspecialchars($item["status"]); ?>
                            </span>


                            <?php if ($item["status"] != "Returned"): ?>

                                <div class="post-actions">

                                    <a
                                        href="edit_post.php?id=<?php echo $item["id"]; ?>"
                                        class="btn"
                                    >
                                        Edit Post
                                    </a>

                                    <a
                                        href="delete_post.php?id=<?php echo $item["id"]; ?>"
                                        class="delete-btn"
                                        onclick="return confirm('Are you sure you want to delete this post?');"
                                    >
                                        Delete Post
                                    </a>

                                </div>

                            <?php endif; ?>


                        </div>

                    <?php endwhile; ?>


                </div>


            <?php endif; ?>


            <a
                href="student_dashboard.php"
                class="back-link"
            >
                ← Back to Dashboard
            </a>


        </div>

    </section>


    <footer class="my-posts-footer">

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
