<?php

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include "db.php";

$total_cars = 0;
$for_sale = 0;
$sold_cars = 0;

$result = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM cars"
);

if ($result) {

    $row = mysqli_fetch_assoc($result);

    $total_cars = $row['total'];
}


$result = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM cars WHERE status = 'For Sale'"
);

if ($result) {

    $row = mysqli_fetch_assoc($result);

    $for_sale = $row['total'];
}


$result = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM cars WHERE status = 'Sold'"
);

if ($result) {

    $row = mysqli_fetch_assoc($result);

    $sold_cars = $row['total'];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Sahkar Cars - Dashboard</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<div class="admin-layout">

    <aside class="sidebar">

        <div class="logo">
            🚘 Sahkar Cars
        </div>

        <nav>

            <a href="dashboard.php" class="active">
                🏠 Dashboard
            </a>

            <a href="add_car.php">
                ➕ Add Car
            </a>

            <a href="cars.php">
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

                <h1>Dashboard</h1>

                <p>
                    Welcome to Sahkar Cars Admin Panel
                </p>

            </div>

            <a href="add_car.php" class="add-button">
                + Add New Car
            </a>

        </div>


        <div class="stats">

            <div class="stat-card">

                <div class="stat-icon">
                    🚗
                </div>

                <div>

                    <h3>Total Cars</h3>

                    <strong>
                        <?php echo $total_cars; ?>
                    </strong>

                </div>

            </div>


            <div class="stat-card">

                <div class="stat-icon">
                    🟢
                </div>

                <div>

                    <h3>For Sale</h3>

                    <strong>
                        <?php echo $for_sale; ?>
                    </strong>

                </div>

            </div>


            <div class="stat-card">

                <div class="stat-icon">
                    🔴
                </div>

                <div>

                    <h3>Sold</h3>

                    <strong>
                        <?php echo $sold_cars; ?>
                    </strong>

                </div>

            </div>

        </div>


        <section class="dashboard-section">

            <h2>Quick Actions</h2>

            <div class="action-grid">

                <a href="add_car.php" class="action-card">

                    <div class="action-icon">
                        ➕
                    </div>

                    <h3>Add New Car</h3>

                    <p>
                        Add a new car with photos and details.
                    </p>

                </a>


                <a href="cars.php" class="action-card">

                    <div class="action-icon">
                        🚘
                    </div>

                    <h3>Manage Cars</h3>

                    <p>
                        Edit, delete and manage cars.
                    </p>

                </a>


                <a href="index.php" target="_blank" class="action-card">

                    <div class="action-icon">
                        🌐
                    </div>

                    <h3>View Website</h3>

                    <p>
                        Open the public Sahkar Cars website.
                    </p>

                </a>

            </div>

        </section>

    </main>

</div>

</body>

</html>