<?php
// Include PHPMailer and necessary classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

// Include database connection
require 'config.php'; // Assuming you have a separate file for DB connection

// Validate GET or POST data
$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : (isset($_POST['order_id']) ? $_POST['order_id'] : null);
$email = isset($_GET['email']) ? $_GET['email'] : (isset($_POST['email']) ? $_POST['email'] : null);

// Stop execution if required parameters are missing
if ($order_id === null || $email === null) {
    die("Error: Missing required parameters. Please provide a valid Order ID and Email.");
}

// Retrieve order details from the database
$order_query = "SELECT * FROM `orders` WHERE `id` = ?";
$stmt = $conn->prepare($order_query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    die("Error: No order found with ID {$order_id}.");
}

// Retrieve user details from the database
$user_query = "SELECT * FROM `user` WHERE `id` = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $order['user_id']);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();

if (!$user) {
    die("Error: No user found for this order.");
}

// Generate the email content
$subject = "Your Order has been Delivered";
$message = "Hello {$order['name']},\n\n";
$message .= "Thank you for your order with us. Here are your order details:\n";
$message .= "Order ID: {$order['id']}\n";
$message .= "Placed on: {$order['placed_on']}\n";
$message .= "Name: {$order['name']}\n";
$message .= "Address: {$order['address']}\n";
$message .= "Total Price: ₱" . number_format($order['total_price'], 2) . "\n\n";
$message .= "We hope to serve you again soon!\n";
$message .= "Best regards,\nPaquitos Pizza";

// Display the email details for debugging
echo "<h1>Send Email for Order #{$order['id']}</h1>";
echo "<p><strong>Subject:</strong> {$subject}</p>";
echo "<p><strong>Message:</strong><br>" . nl2br(htmlspecialchars($message)) . "</p>";

// Use PHPMailer to send the email
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';  // Set the SMTP server
    $mail->SMTPAuth = true;
    $mail->Username = 'paquitospizza0@gmail.com'; // Your Gmail username
    $mail->Password = 'lphs lbzs vhhj ndvo';  // Your Gmail App Password

    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;

    $mail->setFrom('paquitospizza0@gmail.com', 'Paquitos Pizza'); // Sender's email
    $mail->addAddress($email); // Recipient's email

    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = nl2br($message);

    // Send the email
    if ($mail->send()) {
        echo "<script>alert('Email sent successfully.'); document.location.href='admin_orders.php';</script>";
    } else {
        echo "<script>alert('Error sending email.'); document.location.href='admin_orders.php';</script>";
    }
} catch (Exception $e) {
    echo "<script>alert('Mailer Error: {$mail->ErrorInfo}'); document.location.href='admin_orders.php';</script>";
}
?>

<!-- Provide a Back Link -->
<a href="admin_orders.php">Back to Orders</a>
