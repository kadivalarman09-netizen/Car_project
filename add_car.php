<?php

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include "db.php";

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $car_name = trim($_POST['car_name'] ?? '');
    $car_model = trim($_POST['car_model'] ?? '');
    $running_km = trim($_POST['running_km'] ?? '');
    $car_price = trim($_POST['car_price'] ?? '');
    $fuel = trim($_POST['fuel'] ?? '');
    $transmission = trim($_POST['transmission'] ?? '');
    $status = trim($_POST['status'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (
        $car_name == "" ||
        $car_model == "" ||
        $running_km == "" ||
        $car_price == "" ||
        $fuel == "" ||
        $transmission == "" ||
        $status == ""
    ) {

        $error = "Please fill all required fields.";

    } elseif (!is_numeric($running_km)) {

        $error = "Running kilometres must be a number.";

    } elseif (!is_numeric($car_price)) {

        $error = "Car price must be a number.";

    } else {

        $query = "INSERT INTO cars
        (
            car_name,
            car_model,
            running_km,
            car_price,
            fuel,
            transmission,
            status,
            description
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($conn, $query);

        mysqli_stmt_bind_param(
            $stmt,
            "ssidssss",
            $car_name,
            $car_model,
            $running_km,
            $car_price,
            $fuel,
            $transmission,
            $status,
            $description
        );

        if (mysqli_stmt_execute($stmt)) {

            $car_id = mysqli_insert_id($conn);

            $upload_directory = "uploads/" . $car_id;

            if (!is_dir($upload_directory)) {
                mkdir($upload_directory, 0777, true);
            }


            if (
                isset($_FILES['car_photos']) &&
                !empty($_FILES['car_photos']['name'][0])
            ) {

                $total_files = count($_FILES['car_photos']['name']);

                for ($i = 0; $i < $total_files; $i++) {

                    if ($_FILES['car_photos']['error'][$i] != 0) {
                        continue;
                    }

                    $original_name = $_FILES['car_photos']['name'][$i];

                    $tmp_name = $_FILES['car_photos']['tmp_name'][$i];

                    $extension = strtolower(
                        pathinfo($original_name, PATHINFO_EXTENSION)
                    );

                    $allowed_extensions = [
                        "jpg",
                        "jpeg",
                        "png",
                        "webp"
                    ];

                    if (!in_array($extension, $allowed_extensions)) {
                        continue;
                    }

                    $new_name =
                        uniqid("car_", true) . "." . $extension;

                    $destination =
                        $upload_directory . "/" . $new_name;


                    if (move_uploaded_file($tmp_name, $destination)) {

                        $photo_query =
                            "INSERT INTO car_photos
                            (car_id, photo_name)
                            VALUES (?, ?)";

                        $photo_stmt =
                            mysqli_prepare(
                                $conn,
                                $photo_query
                            );

                        mysqli_stmt_bind_param(
                            $photo_stmt,
                            "is",
                            $car_id,
                            $new_name
                        );

                        mysqli_stmt_execute($photo_stmt);

                        mysqli_stmt_close($photo_stmt);
                    }
                }
            }

            $message = "Car added successfully.";

        } else {

            $error = "Failed to add car.";

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

    <title>Add Car - Sahkar Cars</title>

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

            <a href="add_car.php" class="active">
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

                <h1>Add New Car</h1>

                <p>
                    Add car information and photos
                </p>

            </div>

        </div>


        <?php if ($message != "") { ?>

            <div class="success-message">
                <?php echo $message; ?>
            </div>

        <?php } ?>


        <?php if ($error != "") { ?>

            <div class="error-message">
                <?php echo $error; ?>
            </div>

        <?php } ?>


        <form
            method="POST"
            enctype="multipart/form-data"
            class="car-form"
        >

            <div class="form-section">

                <h2>🚗 Car Information</h2>

                <div class="form-grid">


                    <div class="form-group">

                        <label>Car Name *</label>

                        <input
                            type="text"
                            name="car_name"
                            placeholder="Example: BMW"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>Car Model *</label>

                        <input
                            type="text"
                            name="car_model"
                            placeholder="Example: 2008"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>Running Kilometres *</label>

                        <input
                            type="number"
                            name="running_km"
                            placeholder="Example: 85000"
                            min="0"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>Car Price *</label>

                        <input
                            type="number"
                            name="car_price"
                            placeholder="Example: 550000"
                            min="0"
                            step="0.01"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>Fuel *</label>

                        <select name="fuel" required>

                            <option value="">
                                Select Fuel
                            </option>

                            <option value="Petrol">
                                Petrol
                            </option>

                            <option value="Diesel">
                                Diesel
                            </option>

                            <option value="Petrol + Diesel">
                                Petrol + Diesel
                            </option>

                            <option value="CNG">
                                CNG
                            </option>

                            <option value="Electric">
                                Electric
                            </option>

                            <option value="Hybrid">
                                Hybrid
                            </option>

                        </select>

                    </div>


                    <div class="form-group">

                        <label>Transmission *</label>

                        <select name="transmission" required>

                            <option value="">
                                Select Transmission
                            </option>

                            <option value="Automatic">
                                Automatic
                            </option>

                            <option value="Gear / Manual">
                                Gear / Manual
                            </option>

                        </select>

                    </div>


                    <div class="form-group">

                        <label>Status *</label>

                        <select name="status" required>

                            <option value="For Sale">
                                For Sale
                            </option>

                            <option value="Sold">
                                Sold
                            </option>

                        </select>

                    </div>

                </div>

            </div>


            <div class="form-section">

                <h2>📸 Car Photos</h2>

                <div class="form-group">

                    <label>
                        Select Multiple Photos
                    </label>

                    <input
                        type="file"
                        name="car_photos[]"
                        id="carPhotos"
                        accept="image/jpeg,image/png,image/webp"
                        multiple
                    >

                    <div
                        id="photoPreview"
                        class="photo-preview"
                    ></div>

                </div>

            </div>


            <div class="form-section">

                <h2>📝 Description</h2>

                <div class="form-group">

                    <textarea
                        name="description"
                        rows="6"
                        placeholder="Write car details..."
                    ></textarea>

                </div>

            </div>


            <div class="form-buttons">

                <button
                    type="submit"
                    class="add-button"
                >
                    ➕ Add Car
                </button>

                <a
                    href="dashboard.php"
                    class="cancel-button"
                >
                    Cancel
                </a>

            </div>

        </form>

    </main>

</div>


<script>

const carPhotos = document.getElementById("carPhotos");

const photoPreview =
    document.getElementById("photoPreview");


carPhotos.addEventListener("change", function () {

    photoPreview.innerHTML = "";

    const files = this.files;

    for (let i = 0; i < files.length; i++) {

        const reader = new FileReader();

        reader.onload = function (event) {

            const img =
                document.createElement("img");

            img.src = event.target.result;

            img.className = "preview-image";

            photoPreview.appendChild(img);
        };

        reader.readAsDataURL(files[i]);
    }

});

</script>

</body>

</html>