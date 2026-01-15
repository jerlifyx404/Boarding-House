<!-- Font Awesome Link -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet" />

<div class="col-sm-12 text-center">
  <h4><small>Tenant Request for <?= esc($TenantInfo[0]['tenantName'] ?? 'Tenant') ?></small></h4>

  <!-- Navigation and Search Bar -->
  <div style="margin: 35px auto 25px auto; width: 90%; display: flex; align-items: center; justify-content: space-between; gap: 15px;">
    <!-- Back Button (Icon Only) -->
    <a href="<?= base_url('BoardingHouse/tenant') ?>" class="icon-btn" title="Back to Tenants" aria-label="Back to Tenants">
      <i class="fa fa-arrow-left"></i>
    </a>
    <!-- Search Bar -->
    <div style="position: relative; flex: 1;">
      <input
        type="text"
        id="searchBar"
        onkeyup="filterTable()"
        placeholder="Search..."
        aria-label="Search tenant requests"
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
      </style>

      <thead>
        <tr>
          <th>Request ID</th>
          <th>Tenant Name</th>
          <th>Mobile Number</th>
          <th>Email</th>
          <th>Room Preference</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody id="tenantTable">
        <?php if (empty($TenantInfo)): ?>
          <tr>
            <td colspan="7">No pending tenant requests found for this tenant.</td>
          </tr>
        <?php else: ?>
          <?php foreach ($TenantInfo as $Tenant): ?>
            <tr>
              <td><?= esc($Tenant['requestID']) ?></td>
              <td><?= esc($Tenant['tenantName']) ?></td>
              <td><?= esc($Tenant['mobileNumber']) ?></td>
              <td><?= esc($Tenant['email']) ?></td>
              <td><?= esc($Tenant['roomPreference']) ?></td>
              <td><?= esc($Tenant['status']) ?></td>
              <td>
                <div class="action-buttons">
                  <a href="<?= base_url('BoardingHouse/EditTenant/' . esc($Tenant['requestID'])) ?>" class="icon-btn" title="Edit">
                    <i class="fas fa-edit"></i>
                  </a>
                  <a href="<?= base_url('BoardingHouse/DeleteTenant/' . esc($Tenant['requestID'])) ?>" class="icon-btn" title="Delete" onclick="return confirm('Are you sure you want to delete this tenant request?');">
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
    const table = document.getElementById("tenantTable");
    const rows = table.getElementsByTagName("tr");

    // Skip the "Action" (index 6) column
    const searchableColumns = [0, 1, 2, 3, 4, 5];

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