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

$fields = array(
    'name' => $_POST["name"], // name of the customer, currently not used
    'email' => $_POST["email"],
    'seatIds' => $_POST["seatIds"],
);

$seatIds = explode(",", $fields["seatIds"]);
$seatIdsStr = implode(",", $seatIds);
?>

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
      <?php
      // check to ensure seatIds are not ticketed yet
      $sql = "SELECT ticket_id FROM tickets WHERE seat_id IN (" . $fields["seatIds"] . ")";
      $result = $conn->query($sql);
      if ($result->num_rows > 0) {
          echo "One or more seats are already ticketed";
          
      } else {
        // otherwise, create a new ticket for each seat
        foreach ($seatIds as $seatId) {
            $sql = "START TRANSACTION;";
            $result = $conn->query($sql);
            $sql = "INSERT INTO tickets (email, seat_id) VALUES ('" . $fields["email"] . "', " . $seatId . ")";
            $result = $conn->query($sql);
            $sql = "COMMIT;";
            $result = $conn->query($sql);
        }

        // output redirect to homepage button
        if ($seatIdsStr !== '') {
          $sql = "SELECT s.seat_id, s.seat_num, m.title, ss.start_time FROM seats s JOIN showings ss ON (s.showing_id = ss.showing_id) JOIN movies m ON (ss.movie_id = m.movie_id) WHERE s.seat_id IN (" . $seatIdsStr . ")";
          $result = $conn->query($sql);
          if ($result->num_rows > 0) {
            echo "Your confirmed seats are:";
            // output data of each row
            while($row = $result->fetch_assoc()) {
                echo "<div id='seat-" . $row['seat_id'] . "' class='checkout-item'>";
                echo "<div class='checkout-item-title'>";
                echo "<h3>" . $row['title'] . "</h3>";
                echo "</div>";
                echo "<div class='checkout-item-content'>";
                echo "<p>Seat: " . $row['seat_num'] . "</p>";
                echo "<p>Time: " . $row['start_time'] . "</p>";
                echo "</div>";
                echo "</div>";
            }
          }
        }
      }
      // output redirect to homepage button
      echo "<button onclick=\"window.history.go(-1)\">Return to homepage</button>";
      ?>
  </body>

  <script defer src="script.js"></script>
</html>