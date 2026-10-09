
<?php

session_start();
require_once "config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

$stmt = $conn->prepare(
    "SELECT claims.id, claims.message, claims.status, claims.created_at,
            items.item_name, items.type, items.location
     FROM claims
     JOIN items ON claims.item_id = items.id
     WHERE claims.user_id = ?
     ORDER BY claims.created_at DESC"
);

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html>

<head>

    <title>My Claim Requests - Campus Lost & Found</title>

    <link rel="stylesheet" href="style.css">

    <style>

        .claims-page {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .claims-section {
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
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
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
            margin: 9px 0;
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
            padding: 7px 14px;
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

        .claims-footer {
            background: #1f2937;
            color: white;
            text-align: center;
            padding: 18px 10px;
        }

        .claims-footer p {
            margin: 4px 0;
        }

        @media (max-width: 600px) {

            .claims-section {
                padding: 30px 0;
            }

            .claim-card {
                padding: 20px;
            }

        }

    </style>

</head>

<body>

<div class="claims-page">


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


    <section class="claims-section">

        <div class="container">

            <h1 class="page-title">
                My Claim Requests
            </h1>

            <p class="page-subtitle">
                View the status of your submitted claim requests.
            </p>


            <?php if ($result->num_rows == 0): ?>

                <div class="empty-card">

                    <h2>
                        No Claim Requests
                    </h2>

                    <p>
                        You have not submitted any claim requests yet.
                    </p>

                    <a href="items.php" class="btn">
                        Browse Items
                    </a>

                </div>


            <?php else: ?>


                <div class="claims-grid">


                    <?php while ($claim = $result->fetch_assoc()): ?>

                        <div class="claim-card">


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
                                <strong>Your Claim Message:</strong>
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


                        </div>

                    <?php endwhile; ?>


                </div>


            <?php endif; ?>


            <div class="bottom-links">

                <a
                    href="student_dashboard.php"
                    class="btn"
                >
                    Back to Dashboard
                </a>

                <a
                    href="

