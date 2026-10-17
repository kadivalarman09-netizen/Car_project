```php
<?php

include "db.php";

$car_id = intval($_GET['id'] ?? 0);

if ($car_id <= 0) {
    die("Invalid car.");
}

$query = "SELECT * FROM cars WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $car_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$car = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$car) {
    die("Car not found.");
}

$photo_query = "SELECT * FROM car_photos
                WHERE car_id = ?
                ORDER BY id ASC";

$photo_stmt = mysqli_prepare($conn, $photo_query);
mysqli_stmt_bind_param($photo_stmt, "i", $car_id);
mysqli_stmt_execute($photo_stmt);

$photos = mysqli_stmt_get_result($photo_stmt);

$photo_array = [];

while ($photo = mysqli_fetch_assoc($photos)) {
    $photo_array[] = $photo;
}

mysqli_stmt_close($photo_stmt);

// Sahkar Cars contact number
$mobile_number = "9512704396";

// Car details for WhatsApp message
$car_name = $car['car_name'];
$car_model = $car['car_model'];
$car_price = number_format((float)$car['car_price']);
$car_status = $car['status'];

$whatsapp_message =
    "Hello Sahkar Cars! I am interested in this car.\n\n" .
    "Car: " . $car_name . "\n" .
    "Model: " . $car_model . "\n" .
    "Price: Rs. " . $car_price . "\n" .
    "Status: " . $car_status . "\n" .
    "Car Link: " .
    "http://localhost/car_website/view_car.php?id=" . $car_id;

$whatsapp_url =
    "https://wa.me/91" . $mobile_number .
    "?text=" . urlencode($whatsapp_message);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        <?php echo htmlspecialchars($car_name); ?> - Sahkar Cars
    </title>

    <link rel="stylesheet" href="style.css">

    <style>
        .contact-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 24px;
        }

        .contact-actions a,
        .contact-actions button {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            padding: 13px 18px;
            border: none;
            border-radius: 9px;
            text-decoration: none;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            color: white;
            transition: 0.2s;
        }

        .call-button {
            background: #1769d2;
        }

        .whatsapp-button {
            background: #20a957;
        }

        .share-button {
            background: #273449;
        }

        .contact-actions a:hover,
        .contact-actions button:hover {
            opacity: 0.86;
            transform: translateY(-2px);
        }

        .contact-number {
            margin-top: 15px;
            color: #374151;
            font-size: 15px;
        }

        .contact-number a {
            color: #1769d2;
            font-weight: bold;
            text-decoration: none;
        }

        .sold-notice {
            margin-top: 20px;
            padding: 13px;
            border-radius: 8px;
            background: #fff0f0;
            color: #b42318;
            font-weight: bold;
        }

        @media (max-width: 480px) {
            .contact-actions {
                flex-direction: column;
            }

            .contact-actions a,
            .contact-actions button {
                width: 100%;
                box-sizing: border-box;
            }
        }
    </style>
</head>

<body>

<header class="public-header">

    <div class="public-logo">
        🚘 Sahkar Cars
    </div>

    <a href="index.php">← Back to Cars</a>

</header>

<main class="car-detail-page">

    <div class="car-detail-container">

        <div class="car-gallery">

            <?php if (count($photo_array) > 0) { ?>

                <img
                    id="mainCarImage"
                    class="main-car-image"
                    src="uploads/<?php echo $car_id; ?>/<?php
                        echo rawurlencode($photo_array[0]['photo_name']);
                    ?>"
                    alt="<?php echo htmlspecialchars($car_name); ?>"
                >

                <div class="thumbnail-gallery">

                    <?php foreach ($photo_array as $photo) { ?>

                        <img
                            src="uploads/<?php echo $car_id; ?>/<?php
                                echo rawurlencode($photo['photo_name']);
                            ?>"
                            onclick="changeMainImage(this.src)"
                            class="car-thumbnail"
                            alt="Car photo"
                        >

                    <?php } ?>

                </div>

            <?php } else { ?>

                <div class="large-no-photo">
                    🚗
                    <p>No Photo Available</p>
                </div>

            <?php } ?>

        </div>

        <div class="car-detail-info">

            <div class="detail-title-row">

                <div>
                    <h1>
                        <?php echo htmlspecialchars($car_name); ?>
                    </h1>

                    <h3>
                        Model <?php echo htmlspecialchars($car_model); ?>
                    </h3>
                </div>

                <span class="detail-status">
                    <?php echo htmlspecialchars($car_status); ?>
                </span>

            </div>

            <div class="detail-price">
                ₹<?php echo $car_price; ?>
            </div>

            <div class="detail-specs">

                <div>
                    <strong>🛣️ Running</strong>
                    <span>
                        <?php echo number_format($car['running_km']); ?> KM
                    </span>
                </div>

                <div>
                    <strong>⛽ Fuel</strong>
                    <span>
                        <?php echo htmlspecialchars($car['fuel']); ?>
                    </span>
                </div>

                <div>
                    <strong>⚙️ Transmission</strong>
                    <span>
                        <?php echo htmlspecialchars($car['transmission']); ?>
                    </span>
                </div>

            </div>

            <div class="description">

                <h2>About This Car</h2>

                <p>
                    <?php
                    if (!empty($car['description'])) {
                        echo nl2br(htmlspecialchars($car['description']));
                    } else {
                        echo "No description available.";
                    }
                    ?>
                </p>

            </div>

            <?php if (strcasecmp($car_status, "Sold") === 0) { ?>

                <div class="sold-notice">
                    SOLD — This car has been sold.
                </div>

            <?php } ?>

            <div class="share-section">

                <h3>Contact Sahkar Cars</h3>

                <div class="contact-actions">

                    <a
                        class="call-button"
                        href="tel:+91<?php echo $mobile_number; ?>"
                    >
                        📞 Call Now
                    </a>

                    <a
                        class="whatsapp-button"
                        href="<?php echo htmlspecialchars($whatsapp_url); ?>"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        💬 WhatsApp Enquiry
                    </a>

                    <button
                        type="button"
                        onclick="shareCar()"
                        class="share-button"
                    >
                        📤 Share Car
                    </button>

                </div>

                <p class="contact-number">
                    Mobile:
                    <a href="tel:+91<?php echo $mobile_number; ?>">
                        <?php echo $mobile_number; ?>
                    </a>
                </p>

            </div>

        </div>

    </div>

</main>

<script>
function changeMainImage(src) {
    document.getElementById("mainCarImage").src = src;
}

async function shareCar() {
    const shareData = {
        title: <?php echo json_encode($car_name . " - Sahkar Cars"); ?>,
        text: <?php echo json_encode(
            "Check this car at Sahkar Cars. Contact: " . $mobile_number
        ); ?>,
        url: window.location.href
    };

    if (navigator.share) {
        try {
            await navigator.share(shareData);
        } catch (error) {
            // User cancelled sharing, so no alert is needed.
        }
    } else {
        try {
            await navigator.clipboard.writeText(window.location.href);
            alert("Car link copied successfully! You can share it on WhatsApp.");
        } catch (error) {
            prompt("Copy this car link:", window.location.href);
        }
    }
}
</script>

</body>
</html>
