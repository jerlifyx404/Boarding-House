<!DOCTYPE html>
<html>
<head>
  <title><?= esc($page_title ?? 'Add Tenant Request') ?></title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

  <style>
    html, body {
      margin: 0;
      padding: 0;
      background-color: #f7f3ec;
      color: #5c4033;
      font-family: Arial, sans-serif;
      min-height: 100vh;
    }

    .full-wrapper {
      display: flex;
      justify-content: center;
      align-items: flex-start;
      min-height: 100vh;
      padding: 20px;
      box-sizing: border-box;
    }

    .form-container {
      width: 600px;
      background-color: #fffaf0;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      margin-bottom: 20px;
    }

    .form-control {
      background-color: #f5e1c8;
      border: 1px solid #d2b48c;
      color: #543A14;
    }

    .form-control::placeholder {
      color: #543A14;
    }

    .btn-primary, .btn-back {
      background-color: #543A14;
      border-color: #543A14;
      color: white;
      padding: 8px 16px;
      border-radius: 5px;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      transition: background-color 0.3s;
    }

    .btn-primary:hover, .btn-back:hover {
      background-color: #6b4e1e;
      border-color: #6b4e1e;
    }

    h4, label {
      color: #543A14;
      font-weight: 600;
    }

    .error-message {
      background-color: #f2dede;
      color: #a94442;
      border: 1px solid #ebccd1;
      border-radius: 5px;
      padding: 10px;
      margin-bottom: 20px;
    }

    .back-button {
      margin-bottom: 20px;
    }

    .back-button i {
      margin-right: 5px;
    }
  </style>
</head>
<body>

<div class="full-wrapper">
  <div class="form-container">
    <h4 class="text-center"><?= esc($page_title ?? 'Add Tenant Request') ?></h4>
    <hr>

    <!-- Session Feedback -->
    <?php if (session()->has('error')): ?>
      <div class="error-message">
        <?= esc(session('error')) ?>
      </div>
    <?php endif; ?>

    <!-- Back Button -->
    <!-- <div class="back-button">
      <a href="<?= base_url('BoardingHouse/tenant') ?>" class="btn-back" title="Back to Tenants" aria-label="Back to Tenants">
        <i class="fa fa-arrow-left"></i> Back
      </a>
    </div> -->

    <!-- Form -->
    <form action="<?= isset($TenantInfo) ? base_url('BoardingHouse/updateTenant') : base_url('BoardingHouse/insertTenant') ?>" method="post">
      <?= csrf_field() ?>
      <?php if (isset($TenantInfo)): ?>
        <input type="hidden" name="requestID" value="<?= esc($TenantInfo['requestID']) ?>">
      <?php endif; ?>

      <div class="form-group">
        <label for="tenantID">Tenant</label>
        <select name="tenantID" id="tenantID" class="form-control" required>
          <option value="">Select Tenant</option>
          <?php foreach ($tenants as $tenant): ?>
            <option value="<?= esc($tenant['userID']) ?>" <?= isset($TenantInfo) && $TenantInfo['tenantID'] == $tenant['userID'] ? 'selected' : '' ?>>
              <?= esc($tenant['fullName']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group">
        <label for="houseID">Boarding House</label>
        <select name="houseID" id="houseID" class="form-control" required>
          <option value="">Select Boarding House</option>
          <?php foreach ($houses as $house): ?>
            <option value="<?= esc($house['houseID']) ?>" <?= isset($TenantInfo) && $TenantInfo['houseID'] == $house['houseID'] ? 'selected' : '' ?>>
              <?= esc($house['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group">
        <label for="fullName">Full Name</label>
        <input type="text" name="fullName" id="fullName" class="form-control" value="<?= esc($TenantInfo['fullName'] ?? '') ?>" required>
      </div>

      <div class="form-group">
        <label for="mobileNumber">Mobile Number</label>
        <input type="text" name="mobileNumber" id="mobileNumber" class="form-control" value="<?= esc($TenantInfo['mobileNumber'] ?? '') ?>" required>
      </div>

      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" class="form-control" value="<?= esc($TenantInfo['email'] ?? '') ?>" required>
      </div>

      <div class="form-group">
        <label for="roomPreference">Room Preference</label>
        <select name="roomPreference" id="roomPreference" class="form-control" required>
          <option value="Single Room" <?= isset($TenantInfo) && $TenantInfo['roomPreference'] == 'Single Room' ? 'selected' : '' ?>>Single Room</option>
          <option value="Shared Room" <?= isset($TenantInfo) && $TenantInfo['roomPreference'] == 'Shared Room' ? 'selected' : '' ?>>Shared Room</option>
        </select>
      </div>

      <div class="text-center mt-3">
      <?php if (isset($TenantInfo)): ?>
          <button type="submit" class="btn btn-warning">Edit Request</button>
        <?php else: ?>
          <button type="submit" class="btn btn-primary">Add Request</button>
        <?php endif; ?>
      </div>
    </form>
  </div>
</div>

</body>
</html>