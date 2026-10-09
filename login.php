<?php

session_start();
require_once "config.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare(
        "SELECT id, name, email, password, role, email_verified
         FROM users
         WHERE email = ?"
    );

    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows == 1) {

        $user = $result->fetch_assoc();

        if (!password_verify($password, $user["password"])) {

            $message = "Incorrect password.";

        } elseif ($user["email_verified"] != 1) {

            $message = "Please verify your email first.";

        } else {

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["name"] = $user["name"];
            $_SESSION["email"] = $user["email"];
            $_SESSION["role"] = $user["role"];

            if ($user["role"] == "admin") {

                header("Location: admin_dashboard.php");

            } else {

                header("Location: student_dashboard.php");

            }

            exit;
        }

    } else {

        $message = "No account found with this email.";

    }

    $stmt->close();
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Login - Campus Lost & Found</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

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
            <a href="register.php">Register</a>

        </div>

    </div>

</nav>

<div class="form-box">

    <h1>Student Login</h1>

    <p>
        Login to access your account.
    </p>

    <?php if ($message != ""): ?>

        <p>
            <?php echo htmlspecialchars($message); ?>
        </p>

    <?php endif; ?>

    <form method="POST">

        <label>University Gmail</label>

        <input
            type="email"
            name="email"
            placeholder="example@seu.edu.bd"
            required
        >

        <label>Password</label>

        <input
            type="password"
            name="password"
            required
        >

        <button type="submit" class="btn">
            Login
        </button>

    </form>

    <p>
        Don't have an account?
        <a href="register.php">Register here</a>
    </p>

</div>

<footer class="footer">

    <p>
        © 2026 Campus Lost & Found System
    </p>
</footer>

</body>

</html>
