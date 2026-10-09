
<?php

require_once "config.php";

$total_lost = 0;
$total_found = 0;
$total_returned = 0;
$total_active = 0;

$result = $conn->query(
    "SELECT
        SUM(type = 'Lost' AND status = 'Approved') AS total_lost,
        SUM(type = 'Found' AND status = 'Approved') AS total_found,
        SUM(status = 'Returned') AS total_returned,
        SUM(status = 'Approved') AS total_active
     FROM items"
);

if ($result) {

    $stats = $result->fetch_assoc();

    $total_lost = (int) $stats["total_lost"];
    $total_found = (int) $stats["total_found"];
    $total_returned = (int) $stats["total_returned"];
    $total_active = (int) $stats["total_active"];
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Statistics - Campus Lost & Found</title>

    <link rel="stylesheet" href="style.css">

    <style>

        .statistics-page {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .statistics-section {
            flex: 1;
            padding: 50px 0;
        }

        .page-title {
            text-align: center;
            margin-bottom: 10px;
        }

        .page-subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 40px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 45px;
        }

        .stat-card {
            background: white;
            padding: 30px 20px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.08);
        }

        .stat-card h3 {
            margin-top: 0;
            color: #555;
            font-size: 16px;
        }

        .stat-number {
            font-size: 38px;
            font-weight: bold;
            margin: 10px 0 0;
            color: #2563eb;
        }

        .statistics-table-box {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.08);
        }

        .statistics-table-box h2 {
            text-align: center;
            margin-top: 0;
            margin-bottom: 25px;
        }

        .statistics-table {
            width: 100%;
            border-collapse: collapse;
        }

        .statistics-table th,
        .statistics-table td {
            padding: 15px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        .statistics-table th {
            background: #f4f6f8;
        }

        .statistics-table tr:last-child td {
            border-bottom: none;
        }

        .table-count {
            font-weight: bold;
            color: #2563eb;
        }

        .bottom-links {
            text-align: center;
            margin-top: 35px;
        }

        .statistics-footer {
            background: #1f2937;
            color: white;
            text-align: center;
            padding: 18px 10px;
        }

        .statistics-footer p {
            margin: 4px 0;
        }

        @media (max-width: 900px) {

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

        }

        @media (max-width: 600px) {

            .statistics-section {
                padding: 30px 0;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .statistics-table-box {
                padding: 20px;
                overflow-x: auto;
            }

            .statistics-table {
                min-width: 500px;
            }

        }

    </style>

</head>

<body>

<div class="statistics-page">


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


    <section class="statistics-section">

        <div class="container">

            <h1 class="page-title">
                Campus Lost & Found Statistics
            </h1>

            <p class="page-subtitle">
                Overview of Lost & Found activities on the platform.
            </p>


            <div class="stats-grid">


                <div class="stat-card">

                    <h3>
                        Total Lost Items
                    </h3>

                    <p class="stat-number">
                        <?php echo $total_lost; ?>
                    </p>

                </div>


                <div class="stat-card">

                    <h3>
                        Total Found Items
                    </h3>

                    <p class="stat-number">
                        <?php echo $total_found; ?>
                    </p>

                </div>


                <div class="stat-card">

                    <h3>
                        Successfully Returned
                    </h3>

                    <p class="stat-number">
                        <?php echo $total_returned; ?>
                    </p>

                </div>


                <div class="stat-card">

                    <h3>
                        Active Approved Posts
                    </h3>

                    <p class="stat-number">
                        <?php echo $total_active; ?>
                    </p>

                </div>


            </div>


            <div class="statistics-table-box">

                <h2>
                    Statistics Summary
                </h2>


                <table class="statistics-table">

                    <tr>

                        <th>
                            Statistics
                        </th>

                        <th>
                            Count
                        </th>

                    </tr>


                    <tr>

                        <td>
                            Total Lost Items
                        </td>

                        <td class="table-count">
                            <?php echo $total_lost; ?>
                        </td>

                    </tr>


                    <tr>

                        <td>
                            Total Found Items
                        </td>

                        <td class="table-count">
                            <?php echo $total_found; ?>
                        </td>

                    </tr>


                    <tr>

                        <td>
                            Successfully Returned
                        </td>

                        <td class="table-count">
                            <?php echo $total_returned; ?>
                        </td>

                    </tr>


                    <tr>

                        <td>
                            Active Approved Posts
                        </td>

                        <td class="table-count">
                            <?php echo $total_active; ?>
                        </td>

                    </tr>


                </table>

            </div>


            <div class="bottom-links">

                <a
                    href="items.php"
                    class="btn"
                >
                    View Lost & Found Items
                </a>

                <a
                    href="index.php"
                    class="btn"
                >
                    Back to Home
                </a>

            </div>


        </div>

    </section>


    <footer class="statistics-footer">

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
