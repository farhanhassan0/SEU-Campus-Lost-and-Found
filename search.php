
<?php

require_once "config.php";

$search = isset($_GET["search"]) ? trim($_GET["search"]) : "";
$type = isset($_GET["type"]) ? $_GET["type"] : "";

$sql = "SELECT items.id, items.item_name, items.type, items.category,
               items.description, items.location, items.image,
               items.created_at, users.name
        FROM items
        JOIN users ON items.user_id = users.id
        WHERE items.status = 'Approved'";

$params = [];
$types = "";

if ($search != "") {

    $sql .= " AND (
        items.item_name LIKE ?
        OR items.category LIKE ?
        OR items.description LIKE ?
        OR items.location LIKE ?
    )";

    $search_value = "%" . $search . "%";

    $params[] = $search_value;
    $params[] = $search_value;
    $params[] = $search_value;
    $params[] = $search_value;

    $types .= "ssss";
}

if ($type == "Lost" || $type == "Found") {

    $sql .= " AND items.type = ?";

    $params[] = $type;
    $types .= "s";
}

$sql .= " ORDER BY items.created_at DESC";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();

$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html>

<head>

    <title>Search - Campus Lost & Found</title>

    <link rel="stylesheet" href="style.css">

    <style>

        .search-page {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .search-section {
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

        .search-box {
            width: 100%;
            max-width: 850px;
            margin: 0 auto 35px;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.08);
        }

        .search-form {
            display: grid;
            grid-template-columns: 1fr 200px 120px;
            gap: 15px;
            align-items: end;
        }

        .search-field,
        .type-field {
            width: 100%;
        }

        .search-form label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .search-input {
            display: block !important;
            width: 100% !important;
            height: 45px !important;
            padding: 10px 12px !important;
            margin: 0 !important;
            border: 1px solid #ccc !important;
            border-radius: 6px !important;
            background: white !important;
            color: #222 !important;
            font-size: 15px !important;
            box-sizing: border-box !important;
        }

        .search-input:focus {
            outline: none;
            border-color: #2563eb !important;
        }

        .type-select {
            display: block !important;
            width: 100% !important;
            height: 45px !important;
            padding: 10px 12px !important;
            margin: 0 !important;
            border: 1px solid #ccc !important;
            border-radius: 6px !important;
            background: white !important;
            color: #222 !important;
            font-size: 15px !important;
            box-sizing: border-box !important;
        }

        .type-select:focus {
            outline: none;
            border-color: #2563eb !important;
        }

        .search-button {
            width: 100%;
            height: 45px;
            margin: 0;
        }

        .results-title {
            text-align: center;
            margin-bottom: 25px;
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

        .bottom-links {
            text-align: center;
            margin-top: 35px;
        }

        .search-footer {
            background: #1f2937;
            color: white;
            text-align: center;
            padding: 18px 10px;
        }

        .search-footer p {
            margin: 4px 0;
        }

        @media (max-width: 700px) {

            .search-form {
                grid-template-columns: 1fr;
            }

            .search-box {
                padding: 20px;
            }

        }

    </style>

</head>

<body>

<div class="search-page">


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


    <section class="search-section">

        <div class="container">

            <h1 class="page-title">
                Search Lost & Found Items
            </h1>

            <p class="page-subtitle">
                Find items by name, category, description or location.
            </p>


            <div class="search-box">

                <form method="GET" class="search-form">


                    <div class="search-field">

                        <label for="search">
                            Search Item
                        </label>

                        <input
                            type="text"
                            id="search"
                            name="search"
                            class="search-input"
                            placeholder="Example: Wallet, Mobile, ID Card..."
                            value="<?php echo htmlspecialchars($search); ?>"
                        >

                    </div>


                    <div class="type-field">

                        <label for="type">
                            Item Type
                        </label>

                        <select
                            id="type"
                            name="type"
                            class="type-select"
                        >

                            <option value="">
                                All Items
                            </option>

                            <option
                                value="Lost"
                                <?php echo $type == "Lost" ? "selected" : ""; ?>
                            >
                                Lost
                            </option>

                            <option
                                value="Found"
                                <?php echo $type == "Found" ? "selected" : ""; ?>
                            >
                                Found
                            </option>

                        </select>

                    </div>


                    <div>

                        <button
                            type="submit"
                            class="btn search-button"
                        >
                            Search
                        </button>

                    </div>


                </form>

            </div>


            <?php if ($search != "" || $type != ""): ?>

                <h2 class="results-title">
                    Search Results
                </h2>

            <?php else: ?>

                <h2 class="results-title">
                    All Approved Items
                </h2>

            <?php endif; ?>


            <?php if ($result->num_rows == 0): ?>

                <div class="empty-card">

                    <h2>
                        No Items Found
                    </h2>

                    <p>
                        No approved items match your search.
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


            <div class="bottom-links">

                <a href="items.php" class="btn">
                    View All Items
                </a>

                <a href="index.php" class="btn">
                    Back to Home
                </a>

            </div>


        </div>

    </section>


    <footer class="search-footer">

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
