<?php

require_once "config.php";
require_once "email_config.php";
require_once "vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if (!str_ends_with($email, "@seu.edu.bd")) {

        $message = "Only Southeast University email is allowed.";

    } elseif ($password !== $confirm_password) {

        $message = "Passwords do not match.";

    } else {

        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();

        $result = $check->get_result();

        if ($result->num_rows > 0) {

            $message = "This email is already registered.";

        } else {

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $verification_code = rand(100000, 999999);

            $stmt = $conn->prepare(
                "INSERT INTO users (name, email, password, verification_code)
                 VALUES (?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "ssss",
                $name,
                $email,
                $hashed_password,
                $verification_code
            );

            if ($stmt->execute()) {

                $mail = new PHPMailer(true);

                try {

                    $mail->isSMTP();
                    $mail->Host = "smtp.gmail.com";
                    $mail->SMTPAuth = true;
                    $mail->Username = $smtp_email;
                    $mail->Password = $smtp_password;
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom($smtp_email, "Campus Lost & Found");
                    $mail->addAddress($email, $name);

                    $mail->isHTML(true);
                    $mail->Subject = "Campus Lost & Found - Email Verification";

                    $mail->Body = "
                        <h2>Campus Lost & Found</h2>
                        <p>Hello $name,</p>
                        <p>Your verification code is:</p>
                        <h1>$verification_code</h1>
                        <p>Please enter this code on the verification page.</p>
                    ";

                    $mail->send();

                    $message = "Registration successful. Verification code has been sent to your email.";

                } catch (Exception $e) {

                    $message = "Email error: " . $mail->ErrorInfo;

                }

            } else {

                $message = "Registration failed: " . $conn->error;

            }

            $stmt->close();
        }

        $check->close();
    }
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Register - Campus Lost & Found</title>

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
            <a href="login.php">Login</a>
        </div>

    </div>

</nav>

<div class="form-box">

    <h1>Student Registration</h1>

    <p>
        Create your Campus Lost & Found account.
    </p>

    <?php if ($message != ""): ?>

        <p>
            <?php echo htmlspecialchars($message); ?>
        </p>

    <?php endif; ?>

    <form method="POST">

        <label>Name</label>

        <input
            type="text"
            name="name"
            required
        >

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

        <label>Confirm Password</label>

        <input
            type="password"
            name="confirm_password"
            required
        >

        <button type="submit" class="btn">
            Register
        </button>

    </form>

    <p>
        Already have an account?
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