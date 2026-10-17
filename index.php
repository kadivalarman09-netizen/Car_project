<?php

include "db.php";

$query =
    "SELECT * FROM cars
     ORDER BY id DESC";

$result = mysqli_query(
    $conn,
    $query
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Sahkar Cars - Best Cars</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<header class="public-header">

    <div class="public-logo">
        🚘 Sahkar Cars
    </div>

    <div class="public-menu">

        <a href="index.php">
            Home
        </a>

        <a href="login.php">
            Admin Login
        </a>

    </div>

</header>


<section class="hero-section">

    <div class="hero-content">

        <span>
            🚘 Welcome to Sahkar Cars
        </span>

        <h1>
            Find Your<br>
            <strong>Dream Car</strong>
        </h1>

        <p>
            Explore quality cars available at Sahkar Cars.
            Check complete details, photos, price and status.
        </p>

        <a href="#cars" class="hero-button">
            Explore Cars ↓
        </a>

    </div>

</section>


<main id="cars" class="public-cars-section">

    <div class="section-heading">

        <span>OUR COLLECTION</span>

        <h2>
            Available Cars
        </h2>

        <p>
            Choose your next car from Sahkar Cars.
        </p>

    </div>


    <div class="public-car-grid">

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


                mysqli_stmt_execute(
                    $photo_stmt
                );


                $photo_result =
                    mysqli_stmt_get_result(
                        $photo_stmt
                    );


                $photo =
                    mysqli_fetch_assoc(
                        $photo_result
                    );


                mysqli_stmt_close(
                    $photo_stmt
                );

        ?>

            <article class="public-car-card">

                <div class="public-car-image">

                    <?php if ($photo) { ?>

                        <img
                            src="uploads/<?php echo $car_id; ?>/<?php echo htmlspecialchars($photo['photo_name']); ?>"
                            alt="<?php echo htmlspecialchars($car['car_name']); ?>"
                        >

                    <?php } else { ?>

                        <div class="public-no-photo">
                            🚗
                        </div>

                    <?php } ?>


                    <span
                        class="public-status <?php echo ($car['status'] == 'Sold') ? 'sold' : 'sale'; ?>"
                    >

                        <?php
                        echo htmlspecialchars(
                            $car['status']
                        );
                        ?>

                    </span>

                </div>


                <div class="public-car-content">

                    <h3>

                        <?php
                        echo htmlspecialchars(
                            $car['car_name']
                        );
                        ?>

                    </h3>


                    <p class="car-model">

                        Model
                        <?php
                        echo htmlspecialchars(
                            $car['car_model']
                        );
                        ?>

                    </p>


                    <div class="public-price">

                        ₹<?php
                        echo number_format(
                            $car['car_price']
                        );
                        ?>

                    </div>


                    <div class="public-specs">

                        <span>
                            🛣️
                            <?php
                            echo number_format(
                                $car['running_km']
                            );
                            ?>
                            KM
                        </span>

                        <span>
                            ⛽
                            <?php
                            echo htmlspecialchars(
                                $car['fuel']
                            );
                            ?>
                        </span>

                        <span>
                            ⚙️
                            <?php
                            echo htmlspecialchars(
                                $car['transmission']
                            );
                            ?>
                        </span>

                    </div>


                    <a
                        href="view_car.php?id=<?php echo $car_id; ?>"
                        class="details-button"
                    >
                        View Details →
                    </a>

                </div>

            </article>

        <?php

            }

        } else {

        ?>

            <div class="empty-public">

                <h2>
                    🚗 No Cars Available
                </h2>

                <p>
                    Cars will appear here when the admin adds them.
                </p>

            </div>

        <?php } ?>

    </div>

</main>


<footer class="public-footer">

    <h2>
        🚘 Sahkar Cars
    </h2>

    <p>
        Quality Cars • Trusted Service
    </p>

    <p>
        © <?php echo date("Y"); ?> Sahkar Cars. All Rights Reserved.
    </p>

</footer>

</body>

</html>