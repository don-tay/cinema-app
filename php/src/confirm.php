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
        if ($seatIdsStr !== '') {
            $sql = "INSERT INTO tickets (seat_id, email) VALUES " . implode(',', array_map(function ($seatId) use ($fields) {
            return "($seatId, '{$fields['email']}')";
            }, $seatIds));
            if ($conn->query($sql) === TRUE) {
                $sql = "SELECT s.seat_id, s.seat_num, m.title, ss.start_time FROM seats s JOIN showings ss ON (s.showing_id = ss.showing_id) JOIN movies m ON (ss.movie_id = m.movie_id) WHERE s.seat_id IN (" . $seatIdsStr . ")";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    echo "Your confirmed seats are:";
                    $email_str = "Your confirmed seats are:\n\n";
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
                        $email_str .= $row['title'] . " Seat " . $row['seat_num'] . "\n";
                    }
                    $email_str .= "\n\nThank you for booking with Pacific Cinema! We hope you enjoy the show!";
                    $email_header = "From: Pacific Cinema < pacific-cinema@mail.hellodon.dev >";
                    $status = mail($fields['email'], "Pacific Cinema Ticket Confirmation", $email_str, $email_header);
                    if ($status) {
                        echo "Email sent successfully";
                    } else {
                        echo "Email failed to send";
                    }
                } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
                }
            }
        }
      }
      // output redirect to homepage button
      echo "<button onclick=\"window.location.href='index.php'\">Return to homepage</button>";
      ?>
  </body>

  <script defer src="script.js"></script>
</html>