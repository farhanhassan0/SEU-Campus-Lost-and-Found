
<?php

session_start();
require_once "config.php";
require_once "email_config.php";
require_once "vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$item_id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

if ($item_id <= 0) {
    die("Invalid item.");
}

$stmt = $conn->prepare(
    "SELECT items.id, items.item_name, items.type, items.user_id,
            users.name AS owner_name, users.email AS owner_email
     FROM items
     JOIN users ON items.user_id = users.id
     WHERE items.id = ? AND items.status = 'Approved'"
);

$stmt->bind_param("i", $item_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows != 1) {
    die("Item not found or no longer available.");
}

$item = $result->fetch_assoc();

$stmt->close();

if ($item["user_id"] == $user_id) {
    die("You cannot claim your own post.");
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $claim_message = trim($_POST["message"]);

    if ($claim_message == "") {

        $message = "Please provide a claim message.";

    } elseif (strlen($claim_message) < 10) {

        $message = "Claim message must contain at least 10 characters.";

    } else {

        $check = $conn->prepare(
            "SELECT id
             FROM claims
             WHERE item_id = ? AND user_id = ?"
        );

        $check->bind_param("ii", $item_id, $user_id);
        $check->execute();

        $check_result = $check->get_result();

        if ($check_result->num_rows > 0) {

            $message = "You have already submitted a claim for this item.";

        } else {

            $insert = $conn->prepare(
                "INSERT INTO claims (item_id, user_id, message)
                 VALUES (?, ?, ?)"
            );

            $insert->bind_param(
                "iis",
                $item_id,
                $user_id,
                $claim_message
            );

            if ($insert->execute()) {

                $claimer_name = $_SESSION["name"];
                $claimer_email = $_SESSION["email"];

                $mail = new PHPMailer(true);

                try {

                    $mail->isSMTP();
                    $mail->Host = "smtp.gmail.com";
                    $mail->SMTPAuth = true;
                    $mail->Username = $smtp_email;
                    $mail->Password = $smtp_password;
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom(
                        $smtp_email,
                        "Campus Lost & Found"
                    );

                    $mail->addAddress(
                        $item["owner_email"],
                        $item["owner_name"]
                    );

                    $mail->isHTML(true);

                    $mail->Subject =
                        "New Claim Request - Campus Lost & Found";

                    $mail->Body = "
                        <h2>Campus Lost & Found</h2>

                        <p>Hello {$item["owner_name"]},</p>

                        <p>
                            Someone has submitted a claim request
                            for your posted item.
                        </p>

                        <h3>Item Information</h3>

                        <p>
                            <strong>Item:</strong>
                            {$item["item_name"]}
                        </p>

                        <p>
                            <strong>Type:</strong>
                            {$item["type"]}
                        </p>

                        <h3>Claimant Information</h3>

                        <p>
                            <strong>Name:</strong>
                            {$claimer_name}
                        </p>

                        <p>
                            <strong>Email:</strong>
                            {$claimer_email}
                        </p>

                        <h3>Claim Message</h3>

                        <p>
                            {$claim_message}
                        </p>

                        <p>
                            Please login to the Campus Lost & Found
                            system to check the claim request.
                        </p>
                    ";

                    $mail->send();

                    $message =
                        "Claim request submitted successfully. "
                        . "The post owner has been notified by email.";

                } catch (Exception $e) {

                    $message =
                        "Claim submitted successfully, "
                        . "but email notification could not be sent.";

                }

            } else {

                $message = "Failed to submit claim request.";

            }

            $insert->close();
        }

        $check->close();
    }
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Claim Item - Campus Lost & Found</title>

    <link rel="stylesheet" href="style.css">

    <style>

        .claim-page {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .claim-section {
            flex: 1;
            padding: 50px 0;
        }

        .claim-box {
            width: 90%;
            max-width: 650px;
            margin: auto;
            background: white;
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.08);
        }

        .claim-box h1 {
            text-align: center;
            margin-top: 0;
        }

        .claim-subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
        }

        .item-info {
            background: #f4f6f8;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }

        .item-info h2 {
            margin-top: 0;
            margin-bottom: 15px;
        }

        .item-info p {
            margin: 8px 0;
            color: #555;
        }

        .message-box {
            background: #eef6ff;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            text-align: center;
        }

        .claim-box label {
            display: block;
            font-weight: bold;
            margin-bottom: 7px;
        }

        .claim-box textarea {
            display: block;
            width: 100%;
            min-height: 150px;
            padding: 12px;
            margin: 0 0 20px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
            resize: vertical;
            box-sizing: border-box;
        }

        .claim-box textarea:focus {
            outline: none;
            border-color: #2563eb;
        }

        .submit-claim {
            width: 100%;
            margin: 0;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
        }

        .claim-footer {
            background: #1f2937;
            color: white;
            text-align: center;
            padding: 18px 10px;
        }

        .claim-footer p {
            margin: 4px 0;
        }

        @media (max-width: 600px) {

            .claim-box {
                padding: 25px 20px;
            }

        }

    </style>

</head>

<body>

<div class="claim-page">

    <nav class="navbar">

        <div class="container">

            <a href="index.php" class="logo">
                Campus Lost & Found
            </a>

            <div class="nav-links">

                <a href="index.php">Home</a>
                <a href="items.php">Items</a>
                <a href="search.php">Search</a>
                <a href="student_dashboard.php">Dashboard</a>
                <a href="logout.php">Logout</a>

            </div>

        </div>

    </nav>

    <section class="claim-section">

        <div class="claim-box">

            <h1>
                Claim Item
            </h1>

            <p class="claim-subtitle">
                Submit a request if you believe this item belongs to you.
            </p>

            <div class="item-info">

                <h2>
                    <?php echo htmlspecialchars($item["item_name"]); ?>
                </h2>

                <p>
                    <strong>Type:</strong>
                    <?php echo htmlspecialchars($item["type"]); ?>
                </p>

            </div>

            <?php if ($message != ""): ?>

                <div class="message-box">

                    <?php echo htmlspecialchars($message); ?>

                </div>

            <?php endif; ?>

            <form method="POST">

                <label for="message">
                    Why do you believe this item belongs to you?
                </label>

                <textarea
                    id="message"
                    name="message"
                    placeholder="Provide details that can help verify your ownership..."
                    minlength="10"
                    required
                ></textarea>

                <button
                    type="submit"
                    class="btn submit-claim"
                >
                    Submit Claim
                </button>

            </form>

            <a
                href="items.php"
                class="back-link"
            >
                ← Back to Items
            </a>

        </div>

    </section>

    <footer class="claim-footer">

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
