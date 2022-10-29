// helper method to promisify xhr requests
function request(method, url) {
  return new Promise((resolve, reject) => {
    var xhr = new XMLHttpRequest();
    xhr.open(method, url);
    xhr.onload = resolve;
    xhr.onerror = reject;
    xhr.send();
  });
}

async function showSeats(showing_id) {
  const response = await request('POST', 'seats.php?showing_id=' + showing_id);
  document.getElementById('seats').innerHTML = response.target.responseText;
  checkSelectedSeats(showing_id);
}

async function showCheckout() {
  const selectedSeats = getSelectedSeats();
  // merge selectedSeats object values into one array
  const selectedSeatsArr = Object.values(selectedSeats).reduce(
    (acc, val) => acc.concat(val),
    []
  );
  // route to checkout.php
  window.location.href = `checkout.php?selectedSeatIds=${selectedSeatsArr}`;
}

function getSelectedSeats() {
  return JSON.parse(sessionStorage.getItem('selectedSeats')) || {};
}

function selectSeat(element, seat_id, showing_id) {
  // get object of selected seats from sessionStorage, if any
  const selectedSeats = getSelectedSeats();
  const selectedSeatsForShow = selectedSeats[showing_id] || [];

  if (element.classList.contains('selected')) {
    element.classList.remove('selected');
    // remove seat from array
    const index = selectedSeatsForShow.indexOf(seat_id);
    if (index > -1) {
      selectedSeatsForShow.splice(index, 1);
    }
  } else {
    element.classList.add('selected');
    // add seat to array
    selectedSeatsForShow.push(seat_id);
  }
  // update object of selected seats
  selectedSeats[showing_id] = [...new Set(selectedSeatsForShow)];
  // save array to sessionStorage
  sessionStorage.setItem('selectedSeats', JSON.stringify(selectedSeats));
  checkSelectedSeats(showing_id);
}

function cancelSeat(seat_id) {
  const selectedSeats = getSelectedSeats();
  for (const [showing_id, seat_ids] of Object.entries(selectedSeats)) {
    const index = seat_ids.indexOf(seat_id);
    if (index > -1) {
      seat_ids.splice(index, 1);
      selectedSeats[showing_id] = [...new Set(seat_ids)];
      sessionStorage.setItem('selectedSeats', JSON.stringify(selectedSeats));
      showCheckout();
      break;
    }
  }
}

function clearSeats() {
  sessionStorage.removeItem('selectedSeats');
}

// Check sessionStorage selectedSeats field, if seat is selected, mark with class 'selected', and enable checkout-btn
function checkSelectedSeats(showing_id) {
  const selectedSeats = getSelectedSeats();
  const selectedSeatsForShow = selectedSeats[showing_id] || [];
  selectedSeatsForShow.forEach(seat_id => {
    document.getElementById(`seat-${seat_id}`).classList.add('selected');
  });
  if (selectedSeatsForShow.length > 0) {
    document.getElementById('checkout-btn').disabled = false;
  } else {
    document.getElementById('checkout-btn').disabled = true;
  }
}
