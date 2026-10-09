
<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION["role"] != "admin") {
    header("Location: student_dashboard.php");
    exit;
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Admin Dashboard - Campus Lost & Found</title>

    <link rel="stylesheet" href="style.css">

    <style>

        .admin-page {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .admin-welcome {
            padding: 30px 0 15px;
        }

        .welcome-card {
            text-align: center;
        }

        .welcome-card h1 {
            margin-top: 0;
        }

        .admin-section {
            flex: 1;
            display: flex;
            align-items: center;
            padding: 30px 0 45px;
        }

        .admin-title {
            text-align: center;
            margin-bottom: 30px;
        }

        .admin-cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        .admin-card {
            background: white;
            padding: 30px 20px;
            border-radius: 12px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.08);
            text-align: center;
            min-height: 220px;

            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .admin-card h3 {
            margin: 0 0 12px;
        }

        .admin-card p {
            color: #555;
            line-height: 1.5;
            max-width: 220px;
            margin: 0;
        }

        .admin-card .btn {
            margin-top: 18px;
        }

        .admin-footer {
            background: #1f2937;
            color: white;
            text-align: center;
            padding: 18px 10px;
            width: 100%;
        }

        .admin-footer p {
            margin: 4px 0;
        }

        @media (max-width: 900px) {

            .admin-cards {
                grid-template-columns: repeat(2, 1fr);
            }

        }

        @media (max-width: 600px) {

            .admin-cards {
                grid-template-columns: 1fr;
            }

            .admin-section {
                padding-bottom: 30px;
            }

        }

    </style>

</head>

<body>

<div class="admin-page">


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
                <a href="logout.php">Logout</a>

            </div>

        </div>

    </nav>


    <section class="admin-welcome">

        <div class="container">

            <div class="card welcome-card">

                <h1>
                    Admin Dashboard
                </h1>

                <p>
                    Welcome,
                    <strong>
                        <?php echo htmlspecialchars($_SESSION["name"]); ?>
                    </strong>!
                </p>

                <p>
                    Admin Email:
                    <?php echo htmlspecialchars($_SESSION["email"]); ?>
                </p>

            </div>

        </div>

    </section>


    <section class="admin-section">

        <div class="container">

            <h2 class="admin-title">
                Admin Menu
            </h2>


            <div class="admin-cards">


                <div class="admin-card">

                    <h3>
                        Manage Posts
                    </h3>

                    <p>
                        Review, approve, reject or delete student posts.
                    </p>

                    <a
                        href="admin_posts.php"
                        class="btn"
                    >
                        Manage Posts
                    </a>

                </div>


                <div class="admin-card">

                    <h3>
                        Manage Claims
                    </h3>

                    <p>
                        Review and manage student claim requests.
                    </p>

                    <a
                        href="admin_claims.php"
                        class="btn"
                    >
                        Manage Claims
                    </a>

                </div>


                <div class="admin-card">

                    <h3>
                        Manage Users
                    </h3>

                    <p>
                        View registered students and account information.
                    </p>

                    <a
                        href="admin_users.php"
                        class="btn"
                    >
                        Manage Users
                    </a>

                </div>


                <div class="admin-card">

                    <h3>
                        Approved Items
                    </h3>

                    <p>
                        View all currently approved Lost & Found items.
                    </p>

                    <a
                        href="items.php"
                        class="btn"
                    >
                        View Items
                    </a>

                </div>


            </div>

        </div>

    </section>


    <footer class="admin-footer">

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
