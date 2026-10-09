
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

$stmt = $conn->prepare(
    "SELECT items.id, items.item_name, items.type, items.category,
            items.description, items.location, items.image,
            items.status, items.created_at, items.updated_at,
            users.name, users.email
     FROM items
     JOIN users ON items.user_id = users.id
     ORDER BY items.created_at DESC"
);

$stmt->execute();

$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html>

<head>

    <title>Manage Posts - Admin</title>

    <link rel="stylesheet" href="style.css">

    <style>

        .admin-posts-page {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .admin-posts-section {
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
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 25px;
        }

        .post-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
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

        .post-type {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 12px;
        }

        .type-lost {
            background: #fee2e2;
            color: #991b1b;
        }

        .type-found {
            background: #dcfce7;
            color: #166534;
        }

        .post-status {
            display: inline-block;
            padding: 7px 13px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
            margin-top: 5px;
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

        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 20px;
        }

        .action-buttons a {
            display: inline-block;
            padding: 9px 15px;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
        }

        .approve-btn {
            background: #16a34a;
        }

        .approve-btn:hover {
            background: #15803d;
        }

        .reject-btn {
            background: #f59e0b;
        }

        .reject-btn:hover {
            background: #d97706;
        }

        .delete-btn {
            background: #dc2626;
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

        .bottom-links {
            text-align: center;
            margin-top: 35px;
        }

        .admin-posts-footer {
            background: #1f2937;
            color: white;
            text-align: center;
            padding: 18px 10px;
        }

        .admin-posts-footer p {
            margin: 4px 0;
        }

        @media (max-width: 600px) {

            .admin-posts-section {
                padding: 30px 0;
            }

            .post-card {
                padding: 20px;
            }

        }

    </style>

</head>

<body>

<div class="admin-posts-page">


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
                <a href="admin_dashboard.php">Dashboard</a>
                <a href="logout.php">Logout</a>

            </div>

        </div>

    </nav>


    <section class="admin-posts-section">

        <div class="container">

            <h1 class="page-title">
                Manage Posts
            </h1>

            <p class="page-subtitle">
                Review, approve, reject or delete student posts.
            </p>


            <?php if ($result->num_rows == 0): ?>

                <div class="empty-card">

                    <h2>
                        No Posts Found
                    </h2>

                    <p>
                        There are currently no Lost or Found posts.
                    </p>

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


                            <span
                                class="post-type type-<?php echo strtolower($item["type"]); ?>"
                            >
                                <?php echo htmlspecialchars($item["type"]); ?>
                            </span>


                            <h2>
                                <?php echo htmlspecialchars($item["item_name"]); ?>
                            </h2>


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
                                <strong>Posted By:</strong>
                                <?php echo htmlspecialchars($item["name"]); ?>
                            </p>


                            <p>
                                <strong>Email:</strong>
                                <?php echo htmlspecialchars($item["email"]); ?>
                            </p>


                            <p>
                                <strong>Posted On:</strong>
                                <?php echo htmlspecialchars($item["created_at"]); ?>
                            </p>


                            <p>
                                <strong>Last Updated:</strong>
                                <?php echo htmlspecialchars($item["updated_at"]); ?>
                            </p>


                            <p>
                                <strong>Status:</strong>
                            </p>


                            <span
                                class="post-status status-<?php echo strtolower($item["status"]); ?>"
                            >
                                <?php echo htmlspecialchars($item["status"]); ?>
                            </span>


                            <?php if ($item["status"] == "Pending"): ?>

                                <div class="action-buttons">

                                    <a
                                        href="approve_post.php?id=<?php echo $item["id"]; ?>"
                                        class="approve-btn"
                                        onclick="return confirm('Are you sure you want to approve this post?');"
                                    >
                                        Approve
                                    </a>

                                    <a
                                        href="reject_post.php?id=<?php echo $item["id"]; ?>"
                                        class="reject-btn"
                                        onclick="return confirm('Are you sure you want to reject this post?');"
                                    >
                                        Reject
                                    </a>

                                    <a
                                        href="admin_delete_post.php?id=<?php echo $item["id"]; ?>"
                                        class="delete-btn"
                                        onclick="return confirm('Are you sure you want to delete this post?');"
                                    >
                                        Delete
                                    </a>

                                </div>


                            <?php else: ?>

                                <div class="action-buttons">

                                    <a
                                        href="admin_delete_post.php?id=<?php echo $item["id"]; ?>"
                                        class="delete-btn"
                                        onclick="return confirm('Are you sure you want to delete this post?');"
                                    >
                                        Delete
                                    </a>

                                </div>

                            <?php endif; ?>


                        </div>

                    <?php endwhile; ?>


                </div>


            <?php endif; ?>


            <div class="bottom-links">

                <a
                    href="admin_dashboard.php"
                    class="btn"
                >
                    Back to Admin Dashboard
                </a>

            </div>


        </div>

    </section>


    <footer class="admin-posts-footer">

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