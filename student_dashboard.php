
<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Student Dashboard - Campus Lost & Found</title>

    <link rel="stylesheet" href="style.css">

    <style>

        html,
        body {
            margin: 0;
            padding: 0;
            min-height: 100%;
        }

        body {
            min-height: 100vh;
        }

        .dashboard-content {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .welcome-section {
            padding: 30px 0 15px;
        }

        .welcome-card {
            text-align: center;
        }

        .dashboard-section {
            flex: 1;
            display: flex;
            align-items: center;
            padding: 20px 0 40px;
        }

        .dashboard-section .container {
            width: 90%;
            max-width: 1100px;
        }

        .dashboard-title {
            text-align: center;
            margin-bottom: 30px;
        }

        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        .dashboard-card {
            background: white;
            padding: 30px 20px;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.08);
            text-align: center;
            min-height: 220px;

            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .dashboard-card h3 {
            margin: 0 0 12px;
        }

        .dashboard-card p {
            color: #555;
            line-height: 1.5;
            max-width: 220px;
            margin: 0;
        }

        .dashboard-card .btn {
            margin-top: 18px;
        }

        .dashboard-footer {
            background: #1f2937;
            color: white;
            text-align: center;
            padding: 18px 10px;
            width: 100%;
            margin-top: auto;
        }

        .dashboard-footer p {
            margin: 4px 0;
        }

        @media (max-width: 900px) {

            .dashboard-cards {
                grid-template-columns: repeat(2, 1fr);
            }

        }

        @media (max-width: 600px) {

            .dashboard-cards {
                grid-template-columns: 1fr;
            }

            .dashboard-section {
                padding-bottom: 30px;
            }

        }

    </style>

</head>

<body>

<div class="dashboard-content">


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


    <section class="welcome-section">

        <div class="container">

            <div class="card welcome-card">

                <h1>Student Dashboard</h1>

                <p>
                    Welcome,
                    <strong>
                        <?php echo htmlspecialchars($_SESSION["name"]); ?>
                    </strong>!
                </p>

                <p>
                    University Gmail:
                    <?php echo htmlspecialchars($_SESSION["email"]); ?>
                </p>

            </div>

        </div>

    </section>


    <section class="dashboard-section">

        <div class="container">

            <h2 class="dashboard-title">
                Dashboard Menu
            </h2>

            <div class="dashboard-cards">


                <div class="dashboard-card">

                    <h3>Lost & Found Items</h3>

                    <p>
                        View all approved lost and found items.
                    </p>

                    <a href="items.php" class="btn">
                        View Items
                    </a>

                </div>


                <div class="dashboard-card">

                    <h3>Create Post</h3>

                    <p>
                        Report a lost or found item.
                    </p>

                    <a href="create_post.php" class="btn">
                        Create Post
                    </a>

                </div>


                <div class="dashboard-card">

                    <h3>My Posts</h3>

                    <p>
                        View and manage your own posts.
                    </p>

                    <a href="my_posts.php" class="btn">
                        My Posts
                    </a>

                </div>


                <div class="dashboard-card">

                    <h3>My Claim Requests</h3>

                    <p>
                        Check the status of your submitted claims.
                    </p>

                    <a href="claim_requests.php" class="btn">
                        View Claims
                    </a>

                </div>


            </div>

        </div>

    </section>


    <footer class="dashboard-footer">

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
