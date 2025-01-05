<?php

include 'config.php'; // Ensure this file sets up the MySQLi connection in $conn

session_start();

if (isset($_POST['login'])) {
    // Sanitize input
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = sha1($_POST['pass']);

    // Prepare SQL query
    $query = "SELECT * FROM `user` WHERE email = '$email' AND password = '$pass'";

    // Execute query
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        // Fetch the user record
        $row = mysqli_fetch_assoc($result);

        // Set session variable for logged-in user
        $_SESSION['user_id'] = $row['id'];

        // Redirect to the index page
        header('location:index.php');
        exit(); // Always use exit after a header redirect
    } else {
        // If no match, set an error message
        $_SESSION['message'] = 'Incorrect email or password!';
        header('location:index.php'); // Redirect to login page
        exit();
    }
}
?>
