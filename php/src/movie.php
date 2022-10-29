<?php
$host = 'db';
$user = 'rwuser';
$pass = 'rwuserpwd';
$db = 'cinema';

// check the MySQL connection status
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$movie_id = $_GET["id"];
$sql = "SELECT m.title, m.rating, m.genre, m.image_url, m.description, s.showing_id, s.start_time FROM showings s join movies m on (s.movie_id = m.movie_id) where m.movie_id = " . $movie_id;
$result = $conn->query($sql);
$title = "";
$timings = array();
if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        $title = $row["title"];
        $rating = $row["rating"];
        $genre = $row["genre"];
        $image_url = $row["image_url"];
        $description = $row["description"];
        $timings[] = ["start_time" => $row["start_time"], "showing_id" => $row["showing_id"]];
    }
} else {
    echo "No movies showing at the moment";
}
?>

<!-- Movie Schedule Page -->
<html>
    <head>
        <link rel="stylesheet" href="style.css">
        <title>Pacific Cinema</title>
    </head>
    <body>
        <nav class="navbar">
            <div class="container">
                <div class="navbar-brand">
                    <a class="navbar-item" href="index.php"><h1>Pacific Cinema</h1></a>
                </div>
                <div class="navbar-menu">
                    <div class="navbar-end">
                        <a class="navbar-item" href="javascript:showCheckout()">Checkout Cart</a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- show movie details -->
        <section id="movie-details">
            <div class="container">
                <div class="breadcrumbs">
                    <!-- breadcrumb item -->
                    <a href="index.php">Back to Home</a>
                </div>
                <div class="movie-container">
                    <div class="movie-img-container"><img src="<?php echo $image_url; ?>"></div>
                    <div class="movie-details">
                        <h2><?php echo $title; ?></h2>
                        <p><?php echo $rating; ?> | <?php echo $genre; ?></p>
                        <p><?php echo $description; ?></p>

                        <!-- Get movie and its schedule from movie_id query params, on clicking a schedule, invoke javascript to show available seats -->
                        <?php
                            echo "<h3>Movie Schedule</h3>";
                            echo "<div class=\"schedule-container\">";
                            sort($timings);
                            foreach ($timings as $timing) {
                                echo "<div class=\"schedule\"><a href='javascript:showSeats(\"" . $timing['showing_id'] . "\")'>" . substr($timing['start_time'], 0, -3) . "</a></div>";
                            }
                            echo "</div>";
                        ?>
                    </div>
                </div>
            </div>

            <!-- Show available seats -->
            <div id="seats"></div>

        </section>

        <script defer src="script.js"></script>
    </body>
</html>
