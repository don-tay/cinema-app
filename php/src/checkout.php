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

$selectedSeatsStr = $_GET['selectedSeatIds'];
$selectedSeatIds = NULL;
if ($selectedSeatsStr !== '') {
    $selectedSeatIds = explode(',', $selectedSeatsStr);
}
?>

<!-- Checkout page -->
<!-- List all selected seats from sessionStorage -->
<html>
    <head>
        <link rel="stylesheet" href="style.css">
        <title>Cinema App</title>
        <script src="script.js"></script>
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

        <section id="checkout">
            <div class="container">
                <div class="breadcrumbs">
                    <!-- breadcrumb item -->
                    <a href="index.php">Back to Home</a>
                </div>
                <div class="checkout-container">
                    <h1>Checkout</h1>
                    <div class="checkout-list">
                        <div class="checkout-item">
                            <div class="checkout-item-title">
                                <h2>Selected Seats</h2>
                            </div>
                            <div class="checkout-item-content">
                            
                                <?php
                                    if ($selectedSeatsStr !== '') {
                                        $sql = "SELECT s.seat_id, s.seat_num, m.title, ss.start_time FROM seats s JOIN showings ss ON (s.showing_id = ss.showing_id) JOIN movies m ON (ss.movie_id = m.movie_id) WHERE s.seat_id IN (" . $selectedSeatsStr . ")";
                                        $result = $conn->query($sql);
                                        if ($result->num_rows > 0) {
                                            while($row = $result->fetch_assoc()) {
                                                echo "<div id='seat-" . $row['seat_id'] . "' class='checkout-item'>";
                                                echo "<div class='checkout-item-title'>";
                                                echo "<h3>" . $row['title'] . "</h3>";
                                                echo "</div>";
                                                echo "<div class='checkout-item-content'>";
                                                echo "<p>Seat: " . $row['seat_num'] . "</p>";
                                                echo "<p>Time: " . $row['start_time'] . "</p>";
                                                // cancel button
                                                echo "<a onclick='cancelSeat(" . $row['seat_id'] . ")'>Cancel</a>";
                                                echo "</div>";
                                                echo "</div>";
                                            }
                                            echo '
                                                <div class="checkout-item">
                                                <div class="checkout-item-title">
                                                    <h3>Payment</h3>
                                                </div>
                                                <div class="checkout-item-content">
                                                    <form id="checkout-form" action="confirm.php" method="post">
                                                        <div class="field">
                                                            <label class="label">Name</label>
                                                            <div class="control">
                                                                <input class="input" type="text" name="name" placeholder="Name">
                                                            </div>
                                                        </div>
                                                        <div class="field">
                                                            <label class="label">Email</label>
                                                            <div class="control">
                                                                <input class="input" type="email" name="email" placeholder="Email" required>
                                                            </div>
                                                        </div>
                                                        <div class="field">
                                                            <div class="hidden">
                                                                <input type="hidden" name="seatIds" value="' . $selectedSeatsStr . '">
                                                            </div>
                                                        </div>
                                                        <div class="field">
                                                            <div class="control">
                                                                <button class="button is-link" type="submit" onclick="clearSeats()">Confirm Order</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                                ';
                                        }
                                    } else {
                                        echo "<p>No seats selected</p>";
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </body>

    <script defer src="script.js"></script>
</html>