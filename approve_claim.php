
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

$conn->begin_transaction();

try {

    $stmt = $conn->prepare(
        "SELECT claims.item_id, claims.user_id,
                items.item_name,
                users.name AS claimant_name,
                users.email AS claimant_email
         FROM claims
         JOIN items ON claims.item_id = items.id
         JOIN users ON claims.user_id = users.id
         WHERE claims.id = ? AND claims.status = 'Pending'
         FOR UPDATE"
    );

    $stmt->bind_param("i", $claim_id);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows != 1) {
        throw new Exception("Claim not found or already processed.");
    }

    $claim = $result->fetch_assoc();

    $item_id = $claim["item_id"];

    $stmt->close();

    $update_claim = $conn->prepare(
        "UPDATE claims
         SET status = 'Approved'
         WHERE id = ? AND status = 'Pending'"
    );

    $update_claim->bind_param("i", $claim_id);
    $update_claim->execute();

    if ($update_claim->affected_rows != 1) {
        throw new Exception("Unable to approve claim.");
    }

    $update_claim->close();

    $update_item = $conn->prepare(
        "UPDATE items
         SET status = 'Returned'
         WHERE id = ? AND status = 'Approved'"
    );

    $update_item->bind_param("i", $item_id);
    $update_item->execute();

    if ($update_item->affected_rows != 1) {
        throw new Exception("Item is no longer available.");
    }

    $update_item->close();

    $conn->commit();


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
            "Claim Approved - Campus Lost & Found";

        $mail->Body = "
            <h2>Campus Lost & Found</h2>

            <p>
                Hello {$claim["claimant_name"]},
            </p>

            <p>
                Your claim request has been
                <strong>approved</strong> by the administrator.
            </p>

            <h3>Item Information</h3>

            <p>
                <strong>Item:</strong>
                {$claim["item_name"]}
            </p>

            <p>
                The item has been marked as
                <strong>Returned</strong>
                in the system.
            </p>

            <p>
                Please contact the relevant person or
                administration to complete the collection process.
            </p>

            <p>
                Thank you for using Campus Lost & Found.
            </p>
        ";

        $mail->send();

    } catch (Exception $e) {

    }

} catch (Throwable $e) {

    $conn->rollback();
}

header("Location: admin_claims.php");
exit;

?>
