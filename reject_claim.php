
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

if ($_SESSION["role"] != "admin") {
    header("Location: student_dashboard.php");
    exit;
}

$claim_id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

$stmt = $conn->prepare(
    "SELECT claims.item_id, claims.user_id,
            items.item_name,
            users.name AS claimant_name,
            users.email AS claimant_email
     FROM claims
     JOIN items ON claims.item_id = items.id
     JOIN users ON claims.user_id = users.id
     WHERE claims.id = ? AND claims.status = 'Pending'"
);

$stmt->bind_param("i", $claim_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows != 1) {

    $stmt->close();

    header("Location: admin_claims.php");
    exit;
}

$claim = $result->fetch_assoc();

$stmt->close();


$update = $conn->prepare(
    "UPDATE claims
     SET status = 'Rejected'
     WHERE id = ? AND status = 'Pending'"
);

$update->bind_param("i", $claim_id);
$update->execute();

$updated = $update->affected_rows == 1;

$update->close();


if ($updated) {

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
            $claim["claimant_email"],
            $claim["claimant_name"]
        );

        $mail->isHTML(true);

        $mail->Subject =
            "Claim Rejected - Campus Lost & Found";

        $mail->Body = "
            <h2>Campus Lost & Found</h2>

            <p>
                Hello {$claim["claimant_name"]},
            </p>

            <p>
                Your claim request has been
                <strong>rejected</strong> by the administrator.
            </p>

            <h3>Item Information</h3>

            <p>
                <strong>Item:</strong>
                {$claim["item_name"]}
            </p>

            <p>
                Unfortunately, your claim could not be approved.
            </p>

            <p>
                You can browse other Lost & Found items
                available on the platform.
            </p>

            <p>
                Thank you for using Campus Lost & Found.
            </p>
        ";

        $mail->send();

    } catch (Exception $e) {

    }
}

header("Location: admin_claims.php");
exit;

?>
