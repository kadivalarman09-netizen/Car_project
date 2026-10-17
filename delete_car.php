<?php

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include "db.php";

$car_id = intval($_GET['id'] ?? 0);

if ($car_id <= 0) {
    header("Location: cars.php");
    exit();
}


$query = "DELETE FROM cars WHERE id = ?";

$stmt = mysqli_prepare($conn, $query);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $car_id
);

mysqli_stmt_execute($stmt);

mysqli_stmt_close($stmt);


$folder = "uploads/" . $car_id;

if (is_dir($folder)) {

    $files = scandir($folder);

    foreach ($files as $file) {

        if ($file != "." && $file != "..") {

            $file_path = $folder . "/" . $file;

            if (is_file($file_path)) {
                unlink($file_path);
            }
        }
    }

    rmdir($folder);
}


header("Location: cars.php");
exit();

?>