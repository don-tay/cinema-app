<?php
//These are the defined authentication environment in the db service
$host = 'db';
$user = 'rwuser';
$pass = 'rwuserpwd';
$db = 'cinema';

// check the MySQL connection status
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!-- Cinema homepage -->
<html>
    <head>
        <link rel="stylesheet" href="style.css">
        <title>Cinema App</title>
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

        <!-- List of movies -->
        <section id="now-showing">
            <div class="container">
                <h2>Now Showing</h2>
                    <?php
                    // get the list of movies from the db
                    $sql = "SELECT m.movie_id, m.title, m.rating, m.genre, m.image_url, group_concat(s.start_time) as timings FROM showings s join movies m on (s.movie_id = m.movie_id) group by m.movie_id, m.title, m.rating, m.genre, m.image_url";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        // output data of each row
                        while($row = $result->fetch_assoc()) {
                            // format timings as a list and show earliest day's timings only
                            $timings = explode(",", $row['timings']);
                            sort($timings);
                            $timings = array_slice($timings, 0, 3);
                            $timings = implode(", ", $timings);
                            echo "<div class='movie-card'>";
                            echo "<div class='movie-img-container'><img src='" . $row['image_url'] . "'></div>";
                            echo "<div class='movie-details'>";
                            echo "<a href='movie.php?id=" . $row['movie_id'] . "'><h3>" . $row['title'] . "</h3></a>";
                            echo "<p>" . $row['rating'] . " | " . $row['genre'] . "</p>";
                            echo "<p>" . $timings . "</p>";
                            echo "</div>";
                            echo "</div>";
                        }
                    } else {
                        echo "No movies showing at the moment";
                    }
                    ?>
            </div>
        </section>
        <script defer src="script.js"></script>
    </body>
</html>
