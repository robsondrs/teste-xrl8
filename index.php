<?php
require 'vendor/autoload.php'; // Include Composer autoloader

use GetawayFinder\Models\Search;

$latitude = $_POST['latitude'] ?? null;
$longitude = $_POST['longitude'] ?? null;
$orderBy = $_POST['orderBy'] ?? null;

if(isset($_POST['latitude']) && isset($_POST['longitude'])){
    $results = Search::getNearbyHotels($latitude, $longitude, $orderBy);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Hotel Search Results</title>
</head>
<body>
    <header>
        <h1>Hotel Search Results</h1>
    </header>
    <main>
        <form method="POST">
            <label for="latitude">Latitude:</label>
            <input type="text" name="latitude" id="latitude" value="<?= $latitude ?>" required><br>

            <label for="longitude">Longitude:</label>
            <input type="text" name="longitude" id="longitude" value="<?= $longitude ?>" required><br>

            <label for="orderBy">Order By:</label>
            <select name="orderBy" id="orderBy" required>
                <?php foreach (Search::ORDER_BY_LIST as $option => $value):
                    echo "<option value='{$option}' " . ($orderBy === $option ? 'selected' : '') . ">{$value}</option>";
                endforeach ?>
            </select><br>

            <input type="submit" value="Search">
        </form>
        <div class="hotel-list">
            <ul>
                <?php if (empty($results)):
                    echo "<li>No hotels found</li>";
                else:
                    foreach ($results as $result):
                        echo "<li>{$result}</li>";
                    endforeach;
                endif ?>
            </ul>
        </div>
    </main>
</body>
</html>
