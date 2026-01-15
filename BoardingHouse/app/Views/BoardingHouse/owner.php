<!-- Font Awesome Link -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" />

<div class="col-sm-12 text-center">
  <h4><small>Owner Information</small></h4>

  <div style="margin: 35px auto 25px auto; width: 80%; display: flex; align-items: center; justify-content: space-between;">
    <div style="position: relative; flex-grow: 1; margin-right: 10px;">
      <input
        type="text"
        id="searchBar"
        onkeyup="filterTable()"
        placeholder="Search..."
        style="width: 100%; padding: 8px 35px 8px 30px; font-size: 1em; font-family: sans-serif; border: 1px solid #543A14; background-color: #D2B48C;"
      />
      <i class="fa fa-search" style="position: absolute; top: 50%; left: 10px; transform: translateY(-50%); color: #543A14;"></i>
    </div>
    <!-- Add Owner Info Button -->
    <a href="<?= base_url('BoardingHouse/AddOwner') ?>" style="margin-left: 10px; background-color: #543A14; color: white; border: none; padding: 8px 12px; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center;"
      >
        <i class="fa fa-plus" style="margin-right: 5px;"></i> Add
      </a>
  </div>

  <table class="center">
    <style>
      .center {
        border-collapse: collapse;
        margin: 0 auto;
        font-size: 0.9em;
        font-family: sans-serif;
        width: 80%;
        background-color: #D2B48C;
      }

      .center thead tr {
        background-color: #543A14;
        color: #FFF0DC;
        text-align: center;
      }

      .center th, .center td {
        padding: 12px 15px;
        border-left: 1px solid #543A14;
        border-right: 1px solid #543A14;
        text-align: center;
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
        margin: 0 5px;
      }

      .icon-btn:hover {
        color: #6F4E37;
      }
    </style>

    <thead>
      <tr>
        <th>ID</th>
        <th>Fullname</th>
        <th>Username</th>
        <th>Email</th>
        <!-- <th>Password</th> -->
        <th>UserType</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody id="userTable">
      <?php foreach ($UserInfo as $User): ?>
        <tr>
          <td><?= $User['userID'] ?></td>
          <td><?= $User['fullName'] ?></td>
          <td><?= $User['username'] ?></td>
          <td><?= $User['email'] ?></td>
          <!-- <td><?= $User['password'] ?></td> -->
          <td><?= $User['userType'] ?></td>
          <td>
            <!-- View Button -->
             
            <a class="icon-btn" title="View Owner Info" href="<?= base_url('BoardingHouse/ViewOwner?ownerID=' . esc($User['userID'])) ?>">
              <i class="fa fa-eye"></i>
              
            </a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<script>
  function filterTable() {
    const searchInput = document.getElementById("searchBar");
    const filter = searchInput.value.toLowerCase();
    const table = document.getElementById("userTable");
    const rows = table.getElementsByTagName("tr");

    for (let i = 0; i < rows.length; i++) {
      const cells = rows[i].getElementsByTagName("td");
      let match = false;

      for (let j = 0; j < cells.length; j++) {
        if (cells[j].innerText.toLowerCase().indexOf(filter) > -1) {
          match = true;
          break;
        }
      }

      rows[i].style.display = match ? "" : "none";
    }
  }
</script>