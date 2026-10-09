
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
    "SELECT id, name, email, role, email_verified, created_at
     FROM users
     ORDER BY created_at DESC"
);

$stmt->execute();

$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html>

<head>

    <title>Manage Users - Admin</title>

    <link rel="stylesheet" href="style.css">

    <style>

        .admin-users-page {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .admin-users-section {
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

        .users-table-box {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.08);
            overflow-x: auto;
        }

        .users-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 700px;
        }

        .users-table th,
        .users-table td {
            padding: 14px 12px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
        }

        .users-table th {
            background: #f4f6f8;
            font-weight: bold;
        }

        .users-table tr:last-child td {
            border-bottom: none;
        }

        .users-table tr:hover td {
            background: #f8fafc;
        }

        .role {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
        }

        .role-student {
            background: #e0e7ff;
            color: #3730a3;
        }

        .role-admin {
            background: #fef3c7;
            color: #92400e;
        }

        .verification {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
        }

        .verified {
            background: #dcfce7;
            color: #166534;
        }

        .not-verified {
            background: #fee2e2;
            color: #991b1b;
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

        .admin-users-footer {
            background: #1f2937;
            color: white;
            text-align: center;
            padding: 18px 10px;
        }

        .admin-users-footer p {
            margin: 4px 0;
        }

        @media (max-width: 600px) {

            .admin-users-section {
                padding: 30px 0;
            }

            .users-table-box {
                padding: 15px;
            }

        }

    </style>

</head>

<body>

<div class="admin-users-page">


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


    <section class="admin-users-section">

        <div class="container">

            <h1 class="page-title">
                Manage Users
            </h1>

            <p class="page-subtitle">
                View registered students and account information.
            </p>


            <?php if ($result->num_rows == 0): ?>

                <div class="empty-card">

                    <h2>
                        No Registered Users
                    </h2>

                    <p>
                        There are currently no registered users.
                    </p>

                </div>


            <?php else: ?>


                <div class="users-table-box">

                    <table class="users-table">

                        <tr>

                            <th>
                                Name
                            </th>

                            <th>
                                University Email
                            </th>

                            <th>
                                Role
                            </th>

                            <th>
                                Email Status
                            </th>

                            <th>
                                Registered On
                            </th>

                        </tr>


                        <?php while ($user = $result->fetch_assoc()): ?>

                            <tr>

                                <td>
                                    <?php echo htmlspecialchars($user["name"]); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($user["email"]); ?>
                                </td>

                                <td>

                                    <span
                                        class="role role-<?php echo strtolower($user["role"]); ?>"
                                    >
                                        <?php echo htmlspecialchars($user["role"]); ?>
                                    </span>

                                </td>

                                <td>

                                    <?php if ($user["email_verified"] == 1): ?>

                                        <span class="verification verified">
                                            Verified
                                        </span>

                                    <?php else: ?>

                                        <span class="verification not-verified">
                                            Not Verified
                                        </span>

                                    <?php endif; ?>

                                </td>

                                <td>
                                    <?php echo htmlspecialchars($user["created_at"]); ?>
                                </td>

                            </tr>

                        <?php endwhile; ?>


                    </table>

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


    <footer class="admin-users-footer">

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
