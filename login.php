<?php

session_start();
include "db.php";

if (isset($_SESSION['admin'])) {
    header("Location: dashboard.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username == "" || $password == "") {

        $error = "Please enter username and password.";

    } else {

        $query = "SELECT * FROM admin WHERE username = ? AND password = ?";

        $stmt = mysqli_prepare($conn, $query);

        mysqli_stmt_bind_param(
            $stmt,
            "ss",
            $username,
            $password
        );

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 1) {

            $_SESSION['admin'] = $username;

            header("Location: dashboard.php");
            exit();

        } else {

            $error = "Invalid Username or Password.";
        }

        mysqli_stmt_close($stmt);
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Sahkar Cars - Admin Login</title>

    <link rel="stylesheet" href="style.css">

</head>

<body class="login-page">

<div class="login-container">

    <div class="login-box">

        <div class="login-logo">🚘</div>

        <h1>Sahkar Cars</h1>

        <p class="login-subtitle">Admin Panel</p>

        <?php if ($error != "") { ?>

            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>

        <?php } ?>

        <form method="POST">

            <div class="form-group">

                <label>Username</label>

                <input
                    type="text"
                    name="username"
                    placeholder="Enter username"
                    required
                >

            </div>

            <div class="form-group">

                <label>Password</label>

                <input
                    type="password"
                    name="password"
                    placeholder="Enter password"
                    required
                >

            </div>

            <button type="submit" class="login-button">
                Login
            </button>

        </form>

        <a href="index.php" class="back-website">
            ← Back to Website
        </a>

    </div>

</div>

</body>

</html>