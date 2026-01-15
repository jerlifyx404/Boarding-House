<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet" />

<div class="col-sm-12 text-center">
  <h4><small>Boarding House Details for <?= esc($OwnerInfo[0]['ownerName'] ?? 'Owner') ?></small></h4>

  <!-- Navigation and Search Bar -->
  <div style="margin: 35px auto 25px auto; width: 90%; display: flex; align-items: center; justify-content: space-between; gap: 15px;">
    <!-- Back Button (Icon Only) -->
    <a href="<?= base_url('BoardingHouse/owner') ?>" class="icon-btn" title="Back to Owners" aria-label="Back to Owners">
      <i class="fa fa-arrow-left"></i>
    </a>
    <!-- Search Bar -->
    <div style="position: relative; flex: 1;">
      <input
        type="text"
        id="searchBar"
        onkeyup="filterTable()"
        placeholder="Search..."
        aria-label="Search boarding houses"
        style="width: 100%; padding: 8px 35px 8px 30px; font-size: 1em; font-family: sans-serif; border: 1px solid #543A14; background-color: #D2B48C; border-radius: 5px;"
      />
      <i class="fa fa-search" style="position: absolute; top: 50%; left: 10px; transform: translateY(-50%); color: #543A14;"></i>
    </div>
  </div>

  <!-- Session Feedback -->
  <?php if (session()->has('success')): ?>
    <div style="width: 90%; margin: 0 auto; padding: 10px; background-color: #dff0d8; color: #3c763d; border: 1px solid #d6e9c6; border-radius: 5px;">
      <?= esc(session('success')) ?>
    </div>
  <?php endif; ?>
  <?php if (session()->has('error')): ?>
    <div style="width: 90%; margin: 0 auto; padding: 10px; background-color: #f2dede; color: #a94442; border: 1px solid #ebccd1; border-radius: 5px;">
      <?= esc(session('error')) ?>
    </div>
  <?php endif; ?>

  <!-- Table Container -->
  <div style="width: 90%; margin: 0 auto;">
    <table class="center">
      <style>
        .center {
          border-collapse: collapse;
          font-size: 0.9em;
          font-family: sans-serif;
          width: 100%;
          background-color: #D2B48C;
          table-layout: auto;
        }
        .center thead tr {
          background-color: #543A14;
          color: #FFF0DC;
          text-align: center;
        }
        .center th, .center td {
          padding: 12px 10px;
          border-left: 1px solid #543A14;
          border-right: 1px solid #543A14;
          text-align: center;
          vertical-align: middle;
          word-wrap: break-word;
          max-width: 200px;
        }
        .center th:first-child, .center td:first-child {
          border-left: none;
        }
        .center th:last-child, .center td:last-child {
          border-right: none;
        }
        .center tbody tr:nth-of-type(even) {
          background-color: #FFEBCD;
        }
        .center tbody tr.active-row {
          font-weight: bold;
          color: #543A14;
        }
        .icon-btn {
          color: #543A14;
          font-size: 1.2em;
          text-decoration: none;
          margin: 0 8px;
        }
        .icon-btn:hover {
          color: #6F4E37;
        }
        .action-buttons {
          display: flex;
          justify-content: center;
          gap: 10px;
        }
        .map-btn {
          background-color: #543A14;
          color: #FFF0DC;
          border: none;
          padding: 8px 12px;
          border-radius: 5px;
          text-decoration: none;
          font-size: 0.9em;
          display: inline-block;
        }
        .map-btn:hover {
          background-color: #6F4E37;
          color: #FFF0DC;
        }
        .custom-photo-slider {
          display: flex;
          align-items: center;
          justify-content: center;
          max-width: 150px;
          margin: 0 auto;
          position: relative;
        }
        .slider-image {
          width: 100%;
          height: 100px;
          object-fit: cover;
          border-radius: 5px;
        }
        .prev-btn, .next-btn {
          background-color: #543A14;
          color: white;
          border: none;
          padding: 5px 8px;
          cursor: pointer;
          font-size: 14px;
          border-radius: 50%;
          margin: 0 5px;
          opacity: 0;
          pointer-events: auto;
        }
        .prev-btn:hover, .next-btn:hover {
          background-color: #6F4E37;
          opacity: 0;
        }
      </style>

      <thead>
        <tr>
          <th>House ID</th>
          <th>Owner Name</th>
          <th>Name</th>
          <th>Address</th>
          <th>Rooms</th>
          <th>Phone</th>
          <th>Price</th>
          <th>Photos</th>
          <th>Location Map</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody id="boardingTable">
        <?php if (empty($OwnerInfo)): ?>
          <tr>
            <td colspan="10">No boarding houses found for this owner.</td>
          </tr>
        <?php else: ?>
          <?php foreach ($OwnerInfo as $Owner): ?>
            <tr>
              <td><?= esc($Owner['houseID']) ?></td>
              <td><?= esc($Owner['ownerName']) ?></td>
              <td><?= esc($Owner['name']) ?></td>
              <td><?= esc($Owner['address']) ?></td>
              <td><?= esc($Owner['NumberOfRooms']) ?></td>
              <td><?= esc($Owner['pNum']) ?></td>
              <td><?= esc($Owner['price']) ?></td>
              <td>
                <?php $photos = $Owner['photos'] ?? []; ?>
                <?php if (!empty($photos)): ?>
                  <div class="custom-photo-slider" data-houseid="<?= esc($Owner['houseID']) ?>">
                    <button class="prev-btn" onclick="prevSlide(<?= esc($Owner['houseID']) ?>)">❮</button>
                    <img id="photo-<?= esc($Owner['houseID']) ?>" src="http://192.168.1.14:8080<?= esc($photos[0]['photoUrl']) ?>" alt="Boarding Photo" class="slider-image">
                    <button class="next-btn" onclick="nextSlide(<?= esc($Owner['houseID']) ?>)">❯</button>
                  </div>
                  <script>
                    window.photoIndex = window.photoIndex || {};
                    window.photos = window.photos || {};

                    photoIndex[<?= esc($Owner['houseID']) ?>] = 0;
                    photos[<?= esc($Owner['houseID']) ?>] = <?= json_encode(array_map(function($p) {
                      return 'http://192.168.1.14:8080' . $p['photoUrl'];
                    }, $photos)) ?>;
                  </script>
                <?php else: ?>
                  No photos available
                <?php endif; ?>
              </td>
              <td>
                <a href="https://www.google.com/maps?q=<?= urlencode($Owner['address']) ?>" target="_blank" class="map-btn" title="View Location on Google Maps">
                  View Map
                </a>
              </td>
              <td>
                <div class="action-buttons">
                  <a href="<?= base_url('BoardingHouse/EditOwner/' . esc($Owner['houseID'])) ?>" class="icon-btn" title="Edit">
                    <i class="fas fa-edit"></i>
                  </a>
                  <a href="<?= base_url('BoardingHouse/DeleteOwner/' . esc($Owner['houseID'])) ?>" class="icon-btn" title="Delete" onclick="return confirm('Are you sure you want to delete this boarding house?');">
                    <i class="fas fa-trash-alt"></i>
                  </a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Search Filter Script -->
<script>
  function filterTable() {
    const searchInput = document.getElementById("searchBar");
    const filter = searchInput.value.toLowerCase().trim();
    const table = document.getElementById("boardingTable");
    const rows = table.getElementsByTagName("tr");

    const searchableColumns = [0, 1, 2, 3, 4, 5, 6];

    for (let i = 0; i < rows.length; i++) {
      const cells = rows[i].getElementsByTagName("td");
      let match = false;

      for (let j of searchableColumns) {
        if (cells[j]) {
          const cellText = cells[j].innerText || cells[j].textContent;
          if (cellText.toLowerCase().includes(filter)) {
            match = true;
            break;
          }
        }
      }

      rows[i].style.display = match ? "" : "none";
    }
  }
</script>

<!-- Bootstrap JS for Carousel -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<!-- Initialize Carousels -->
<script>
  function prevSlide(houseID) {
    if (photos[houseID]) {
      photoIndex[houseID] = (photoIndex[houseID] - 1 + photos[houseID].length) % photos[houseID].length;
      document.getElementById("photo-" + houseID).src = photos[houseID][photoIndex[houseID]];
    }
  }

  function nextSlide(houseID) {
    if (photos[houseID]) {
      photoIndex[houseID] = (photoIndex[houseID] + 1) % photos[houseID].length;
      document.getElementById("photo-" + houseID).src = photos[houseID][photoIndex[houseID]];
    }
  }
</script>