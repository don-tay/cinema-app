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

$showing_id = $_GET["showing_id"];

$sql = "SELECT s.seat_id, s.seat_num, t.ticket_id FROM seats s LEFT JOIN tickets t ON (s.seat_id = t.seat_id) where showing_id = " . $showing_id;
$result = $conn->query($sql);
$seats = array();
if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        $seats[] = $row;
    }
}
// sort seats from 2nd character of string onwards
usort($seats, function($a, $b) {
    return substr($a["seat_num"], 1) <=> substr($b["seat_num"], 1);
});
?>

<html>
    <body>
        <div class="container">
            <h3>Choosing seats for 
                <?php
                    $sql = "SELECT m.title, s.start_time FROM showings s join movies m on (s.movie_id = m.movie_id) where s.showing_id = " . $showing_id;
                    $result = $conn->query($sql);
                    $title = "";
                    $start_time = "";
                    if ($result->num_rows > 0) {
                        // output data of each row
                        while($row = $result->fetch_assoc()) {
                            $title = $row["title"];
                            $start_time = $row["start_time"];
                        }
                    }
                    echo $title . " at " . $start_time;
                ?>
            </h3>
            <!-- Display seats in rows of 8 buttons, if ticket_id is NULL mark button as disabled -->
            <div class="seat-container">
                <?php
                    $i = 0;
                    foreach ($seats as $seat) {
                        if ($i % 8 == 0) {
                            echo "</div><div class=\"seat-row\">";
                        }
                        if ($seat["ticket_id"] == NULL) {
                            echo "<button id=seat-" . $seat['seat_id'] . " class='seat-btn' onclick=\"selectSeat(this," . $seat["seat_id"] . ", " . $showing_id . ")\">" . $seat["seat_num"] . "</button>";
                        } else {
                            echo "<button id=seat-" . $seat['seat_id'] . " class='seat-btn' disabled>" . $seat["seat_num"] . "</button>";
                        }
                        $i++;
                    }
                ?>
            </div>

            <!-- legend indicator -->
            <div class="legend">
                <div class="legend-item">
                    <button class="seat-btn">1</button>
                    <span>Available</span>
                </div>
                <div class="legend-item">
                    <button class="seat-btn selected">2</button>
                    <span>Selected</span>
                </div>
                <div class="legend-item">
                    <button class="seat-btn" disabled>3</button>
                    <span>Unavailable</span>
                </div>
            </div>

            <!-- once any seats are selected, show checkout button -->
            <div class="checkout-btn-container">
                <button id="checkout-btn" class="checkout-btn" onclick="showCheckout()" disabled>Checkout</button>
            </div>
        </div>
    </body>
</html>
