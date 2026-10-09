
<?php

require_once "config.php";

$stmt = $conn->prepare(
    "SELECT items.id, items.item_name, items.type, items.category,
            items.description, items.location, items.image,
            items.created_at, users.name
     FROM items
     JOIN users ON items.user_id = users.id
     WHERE items.status = 'Approved'
     ORDER BY items.created_at DESC"
);

$stmt->execute();

$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html>

<head>

    <title>Lost & Found Items - Campus Lost & Found</title>

    <link rel="stylesheet" href="style.css">

    <style>

        .items-page {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .items-section {
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

        .items-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
        }

        .item-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.08);
        }

        .item-card h2 {
            margin-top: 0;
            margin-bottom: 15px;
        }

        .item-card p {
            color: #555;
            line-height: 1.5;
            margin: 8px 0;
        }

        .item-image {
            width: 100%;
            height: 220px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 18px;
        }

        .item-type {
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

        .claim-btn {
            display: inline-block;
            margin-top: 18px;
            padding: 11px 20px;
            background: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }

        .claim-btn:hover {
            background: #1d4ed8;
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

        .items-footer {
            background: #1f2937;
            color: white;
            text-align: center;
            padding: 18px 10px;
        }

        .items-footer p {
            margin: 4px 0;
        }

        @media (max-width: 600px) {

            .items-section {
                padding: 30px 0;
            }

            .item-card {
                padding: 20px;
            }

        }

    </style>

</head>

<body>

<div class="items-page">


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
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>

            </div>

        </div>

    </nav>


    <section class="items-section">

        <div class="container">

            <h1 class="page-title">
                Lost & Found Items
            </h1>

            <p class="page-subtitle">
                Browse approved Lost and Found items from the campus.
            </p>


            <?php if ($result->num_rows == 0): ?>

                <div class="empty-card">

                    <h2>No Items Available</h2>

                    <p>
                        There are currently no approved Lost or Found items.
                    </p>

                </div>


            <?php else: ?>


                <div class="items-grid">


                    <?php while ($item = $result->fetch_assoc()): ?>

                        <div class="item-card">


                            <?php if (!empty($item["image"])): ?>

                                <img
                                    src="uploads/<?php echo htmlspecialchars($item["image"]); ?>"
                                    class="item-image"
                                    alt="Item Picture"
                                >

                            <?php endif; ?>


                            <span
                                class="item-type type-<?php echo strtolower($item["type"]); ?>"
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
                                <strong>Posted On:</strong>
                                <?php echo htmlspecialchars($item["created_at"]); ?>
                            </p>


                            <a
                                href="claim.php?id=<?php echo $item["id"]; ?>"
                                class="claim-btn"
                            >
                                Claim This Item
                            </a>


                        </div>

                    <?php endwhile; ?>


                </div>


            <?php endif; ?>


            <a
                href="index.php"
                class="back-link"
            >
                ← Back to Home
            </a>


        </div>

    </section>


    <footer class="items-footer">

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
