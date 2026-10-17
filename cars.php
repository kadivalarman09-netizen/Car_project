<?php

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include "db.php";

$result = mysqli_query(
    $conn,
    "SELECT * FROM cars ORDER BY id DESC"
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>All Cars - Sahkar Cars</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<div class="admin-layout">

    <aside class="sidebar">

        <div class="logo">
            🚘 Sahkar Cars
        </div>

        <nav>

            <a href="dashboard.php">
                🏠 Dashboard
            </a>

            <a href="add_car.php">
                ➕ Add Car
            </a>

            <a href="cars.php" class="active">
                🚗 All Cars
            </a>

            <a href="index.php" target="_blank">
                🌐 View Website
            </a>

        </nav>

        <a href="logout.php" class="logout-button">
            🚪 Logout
        </a>

    </aside>


    <main class="main-content">

        <div class="topbar">

            <div>

                <h1>All Cars</h1>

                <p>
                    Manage your Sahkar Cars inventory
                </p>

            </div>

            <a href="add_car.php" class="add-button">
                + Add Car
            </a>

        </div>


        <div class="car-list">

            <?php

            if (mysqli_num_rows($result) > 0) {

                while ($car = mysqli_fetch_assoc($result)) {

                    $car_id = $car['id'];

                    $photo_query =
                        "SELECT photo_name
                         FROM car_photos
                         WHERE car_id = ?
                         ORDER BY id ASC
                         LIMIT 1";

                    $photo_stmt =
                        mysqli_prepare(
                            $conn,
                            $photo_query
                        );

                    mysqli_stmt_bind_param(
                        $photo_stmt,
                        "i",
                        $car_id
                    );

                    mysqli_stmt_execute($photo_stmt);

                    $photo_result =
                        mysqli_stmt_get_result(
                            $photo_stmt
                        );

                    $photo =
                        mysqli_fetch_assoc(
                            $photo_result
                        );

                    mysqli_stmt_close($photo_stmt);

            ?>

                <div class="car-admin-card">

                    <div class="car-admin-image">

                        <?php if ($photo) { ?>

                            <img
                                src="uploads/<?php echo $car_id; ?>/<?php echo htmlspecialchars($photo['photo_name']); ?>"
                                alt="<?php echo htmlspecialchars($car['car_name']); ?>"
                            >

                        <?php } else { ?>

                            <div class="no-photo">
                                🚗
                                <span>No Photo</span>
                            </div>

                        <?php } ?>

                    </div>


                    <div class="car-admin-info">

                        <h2>
                            <?php
                            echo htmlspecialchars(
                                $car['car_name']
                            );
                            ?>
                        </h2>

                        <p>
                            Model:
                            <?php
                            echo htmlspecialchars(
                                $car['car_model']
                            );
                            ?>
                        </p>

                        <p>
                            KM:
                            <?php
                            echo number_format(
                                $car['running_km']
                            );
                            ?>
                        </p>

                        <p>
                            Price:
                            ₹<?php
                            echo number_format(
                                $car['car_price']
                            );
                            ?>
                        </p>

                        <span class="status-badge">

                            <?php
                            echo htmlspecialchars(
                                $car['status']
                            );
                            ?>

                        </span>


                        <div class="car-actions">

                            <a
                                href="view_car.php?id=<?php echo $car_id; ?>"
                                class="view-button"
                                target="_blank"
                            >
                                View
                            </a>

                            <a
                                href="edit_car.php?id=<?php echo $car_id; ?>"
                                class="edit-button"
                            >
                                Edit
                            </a>

                            <a
                                href="delete_car.php?id=<?php echo $car_id; ?>"
                                class="delete-button"
                                onclick="return confirm('Are you sure you want to delete this car?');"
                            >
                                Delete
                            </a>

                        </div>

                    </div>

                </div>

            <?php

                }

            } else {

            ?>

                <div class="empty-state">

                    <h2>No Cars Found</h2>

                    <p>
                        Start by adding your first car.
                    </p>

                    <a
                        href="add_car.php"
                        class="add-button"
                    >
                        Add First Car
                    </a>

                </div>

            <?php } ?>

        </div>

    </main>

</div>

</body>

</html>