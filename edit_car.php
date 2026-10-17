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


$query = "SELECT * FROM cars WHERE id = ?";

$stmt = mysqli_prepare($conn, $query);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $car_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$car = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


if (!$car) {
    die("Car not found.");
}


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

        $update_query =
            "UPDATE cars SET
                car_name = ?,
                car_model = ?,
                running_km = ?,
                car_price = ?,
                fuel = ?,
                transmission = ?,
                status = ?,
                description = ?
             WHERE id = ?";


        $update_stmt =
            mysqli_prepare(
                $conn,
                $update_query
            );


        mysqli_stmt_bind_param(
            $update_stmt,
            "ssidssssi",
            $car_name,
            $car_model,
            $running_km,
            $car_price,
            $fuel,
            $transmission,
            $status,
            $description,
            $car_id
        );


        if (mysqli_stmt_execute($update_stmt)) {

            $message = "Car updated successfully.";

            $car['car_name'] = $car_name;
            $car['car_model'] = $car_model;
            $car['running_km'] = $running_km;
            $car['car_price'] = $car_price;
            $car['fuel'] = $fuel;
            $car['transmission'] = $transmission;
            $car['status'] = $status;
            $car['description'] = $description;

        } else {

            $error = "Failed to update car.";

        }

        mysqli_stmt_close($update_stmt);
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Edit Car - Sahkar Cars</title>

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

                <h1>Edit Car</h1>

                <p>
                    Update car information
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
                            value="<?php echo htmlspecialchars($car['car_name']); ?>"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>Car Model *</label>

                        <input
                            type="text"
                            name="car_model"
                            value="<?php echo htmlspecialchars($car['car_model']); ?>"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>Running Kilometres *</label>

                        <input
                            type="number"
                            name="running_km"
                            value="<?php echo htmlspecialchars($car['running_km']); ?>"
                            min="0"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>Car Price *</label>

                        <input
                            type="number"
                            name="car_price"
                            value="<?php echo htmlspecialchars($car['car_price']); ?>"
                            min="0"
                            step="0.01"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>Fuel *</label>

                        <select name="fuel" required>

                            <option value="Petrol"
                                <?php if ($car['fuel'] == 'Petrol') echo 'selected'; ?>>
                                Petrol
                            </option>

                            <option value="Diesel"
                                <?php if ($car['fuel'] == 'Diesel') echo 'selected'; ?>>
                                Diesel
                            </option>

                            <option value="Petrol + Diesel"
                                <?php if ($car['fuel'] == 'Petrol + Diesel') echo 'selected'; ?>>
                                Petrol + Diesel
                            </option>

                            <option value="CNG"
                                <?php if ($car['fuel'] == 'CNG') echo 'selected'; ?>>
                                CNG
                            </option>

                            <option value="Electric"
                                <?php if ($car['fuel'] == 'Electric') echo 'selected'; ?>>
                                Electric
                            </option>

                            <option value="Hybrid"
                                <?php if ($car['fuel'] == 'Hybrid') echo 'selected'; ?>>
                                Hybrid
                            </option>

                        </select>

                    </div>


                    <div class="form-group">

                        <label>Transmission *</label>

                        <select name="transmission" required>

                            <option value="Automatic"
                                <?php if ($car['transmission'] == 'Automatic') echo 'selected'; ?>>
                                Automatic
                            </option>

                            <option value="Gear / Manual"
                                <?php if ($car['transmission'] == 'Gear / Manual') echo 'selected'; ?>>
                                Gear / Manual
                            </option>

                        </select>

                    </div>


                    <div class="form-group">

                        <label>Status *</label>

                        <select name="status" required>

                            <option value="For Sale"
                                <?php if ($car['status'] == 'For Sale') echo 'selected'; ?>>
                                For Sale
                            </option>

                            <option value="Sold"
                                <?php if ($car['status'] == 'Sold') echo 'selected'; ?>>
                                Sold
                            </option>

                        </select>

                    </div>

                </div>

            </div>


            <div class="form-section">

                <h2>📝 Description</h2>

                <div class="form-group">

                    <textarea
                        name="description"
                        rows="6"
                    ><?php echo htmlspecialchars($car['description']); ?></textarea>

                </div>

            </div>


            <div class="form-buttons">

                <button
                    type="submit"
                    class="add-button"
                >
                    💾 Update Car
                </button>

                <a
                    href="cars.php"
                    class="cancel-button"
                >
                    Cancel
                </a>

            </div>

        </form>

    </main>

</div>

</body>

</html>