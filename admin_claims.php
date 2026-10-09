
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
    "SELECT claims.id, claims.message, claims.status, claims.created_at,
            items.item_name, items.type, items.location, items.image,
            users.name, users.email
     FROM claims
     JOIN items ON claims.item_id = items.id
     JOIN users ON claims.user_id = users.id
     ORDER BY claims.created_at DESC"
);

$stmt->execute();

$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html>

<head>

    <title>Manage Claims - Admin</title>

    <link rel="stylesheet" href="style.css">

    <style>

        .admin-claims-page {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .admin-claims-section {
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

        .claims-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 25px;
        }

        .claim-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.08);
        }

        .claim-card h2 {
            margin-top: 0;
            margin-bottom: 15px;
        }

        .claim-card p {
            color: #555;
            line-height: 1.5;
            margin: 8px 0;
        }

        .item-image {
            width: 100%;
            height: 220px;
            object-fit: contain;
            border-radius: 8px;
            margin-bottom: 18px;
            background: #f4f6f8;
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

        .claim-status {
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

        .admin-claims-footer {
            background: #1f2937;
            color: white;
            text-align: center;
            padding: 18px 10px;
        }

        .admin-claims-footer p {
            margin: 4px 0;
        }

        @media (max-width: 600px) {

            .admin-claims-section {
                padding: 30px 0;
            }

            .claim-card {
                padding: 20px;
            }

        }

    </style>

</head>

<body>

<div class="admin-claims-page">


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


    <section class="admin-claims-section">

        <div class="container">

            <h1 class="page-title">
                Manage Claim Requests
            </h1>

            <p class="page-subtitle">
                Review and manage student claim requests.
            </p>


            <?php if ($result->num_rows == 0): ?>

                <div class="empty-card">

                    <h2>
                        No Claim Requests
                    </h2>

                    <p>
                        There are currently no claim requests.
                    </p>

                </div>


            <?php else: ?>


                <div class="claims-grid">


                    <?php while ($claim = $result->fetch_assoc()): ?>

                        <div class="claim-card">


                            <?php if (!empty($claim["image"])): ?>

                                <img
                                    src="uploads/<?php echo htmlspecialchars($claim["image"]); ?>"
                                    class="item-image"
                                    alt="Item Picture"
                                >

                            <?php endif; ?>


                            <span
                                class="item-type type-<?php echo strtolower($claim["type"]); ?>"
                            >
                                <?php echo htmlspecialchars($claim["type"]); ?>
                            </span>


                            <h2>
                                <?php echo htmlspecialchars($claim["item_name"]); ?>
                            </h2>


                            <p>
                                <strong>Location:</strong>
                                <?php echo htmlspecialchars($claim["location"]); ?>
                            </p>


                            <p>
                                <strong>Claimed By:</strong>
                                <?php echo htmlspecialchars($claim["name"]); ?>
                            </p>


                            <p>
                                <strong>Email:</strong>
                                <?php echo htmlspecialchars($claim["email"]); ?>
                            </p>


                            <p>
                                <strong>Claim Message:</strong>
                            </p>

                            <p>
                                <?php echo htmlspecialchars($claim["message"]); ?>
                            </p>


                            <p>
                                <strong>Status:</strong>
                            </p>


                            <span
                                class="claim-status status-<?php echo strtolower($claim["status"]); ?>"
                            >
                                <?php echo htmlspecialchars($claim["status"]); ?>
                            </span>


                            <p>
                                <strong>Submitted On:</strong>
                                <?php echo htmlspecialchars($claim["created_at"]); ?>
                            </p>


                            <?php if ($claim["status"] == "Pending"): ?>

                                <div class="action-buttons">

                                    <a
                                        href="approve_claim.php?id=<?php echo $claim["id"]; ?>"
                                        class="approve-btn"
                                        onclick="return confirm('Are you sure you want to approve this claim?');"
                                    >
                                        Approve Claim
                                    </a>

                                    <a
                                        href="reject_claim.php?id=<?php echo $claim["id"]; ?>"
                                        class="reject-btn"
                                        onclick="return confirm('Are you sure you want to reject this claim?');"
                                    >
                                        Reject Claim
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


    <footer class="admin-claims-footer">

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
