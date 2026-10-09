<?php

require_once "config.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"]);
    $code = trim($_POST["verification_code"]);

    $stmt = $conn->prepare(
        "SELECT id FROM users
         WHERE email = ? AND verification_code = ? AND email_verified = 0"
    );

    $stmt->bind_param("ss", $email, $code);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows == 1) {

        $update = $conn->prepare(
            "UPDATE users
             SET email_verified = 1, verification_code = NULL
             WHERE email = ?"
        );

        $update->bind_param("s", $email);
        $update->execute();
        $update->close();

        $message = "Email verified successfully! You can now login.";

    } else {

        $message = "Invalid verification code.";

    }

    $stmt->close();
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Verify Email - Campus Lost & Found</title>

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
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>

        </div>

    </div>

</nav>

<div class="form-box">

    <h1>Verify Your Email</h1>

    <p>
        Enter your university email and the verification code
        sent to your email.
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

        <label>Verification Code</label>

        <input
            type="text"
            name="verification_code"
            placeholder="Enter 6-digit code"
            maxlength="6"
            required
        >

        <button type="submit" class="btn">
            Verify Email
        </button>

    </form>

    <p>
        Already verified?
        <a href="login.php">Login here</a>
    </p>

</div>

<footer class="footer">

    <p>
        © 2026 Campus Lost & Found System
    </p>

</footer>

</body>

</html>
