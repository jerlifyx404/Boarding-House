<!DOCTYPE html>
<html>
<head>
  <title>Add Boarding House</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

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

    .form-control, .form-control select {
      background-color: #f5e1c8;
      border: 1px solid #d2b48c;
      color: #543A14;
    }

    .form-control::placeholder {
      color: #543A14;
    }

    .btn-primary, .btn-warning {
      background-color: #543A14;
      border-color: #543A14;
      color: white;
    }

    h4, label {
      color: #543A14;
      font-weight: 600;
    }

    .photo-preview img {
      max-width: 100px;
      max-height: 100px;
      margin: 5px;
      border-radius: 5px;
    }

    .photo-preview .photo-item {
      display: inline-block;
      position: relative;
      margin-right: 10px;
    }

    .photo-preview input[type="checkbox"] {
      position: absolute;
      top: 5px;
      right: 5px;
      cursor: pointer;
    }

    .photo-preview label {
      font-size: 0.9em;
      margin-left: 5px;
      color: #543A14;
    }

    .delete-icon {
      cursor: pointer;
      color: red;
      font-size: 20px;
      display: inline-block;
      margin-top: 5px;
    }

  </style>
</head>
<body>

<div class="full-wrapper">
  <div class="form-container">
    <h4 class="text-center"><?= isset($OwnerInfo) ? 'Edit Boarding House' : 'Add Boarding House' ?></h4>
    <hr>

    <?= session()->getFlashdata('error') ?>
    <?= validation_list_errors() ?>

    <form action="<?= isset($OwnerInfo) ? base_url('BoardingHouse/updateOwner') : base_url('BoardingHouse/insertOwner') ?>" method="post" enctype="multipart/form-data">
      <?= csrf_field() ?>

      <div class="form-group">
        <input type="hidden" class="form-control" name="txtHouseID" value="<?= isset($OwnerInfo) ? esc($OwnerInfo['houseID']) : set_value('txtHouseID') ?>">
      </div>

      <div class="form-group">
        <label for="ownerID">Owner</label>
        <select class="form-control" id="ownerID" name="ownerID" required>
          <option value="">Select Owner</option>
          <?php foreach ($owners as $owner): ?>
            <option value="<?= esc($owner['userID']) ?>" <?= isset($OwnerInfo) && $OwnerInfo['ownerID'] == $owner['userID'] ? 'selected' : '' ?>>
              <?= esc($owner['fullName']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group">
        <label for="txtName">Boarding House Name</label>
        <input type="text" class="form-control" id="txtName" name="txtName" required value="<?= isset($OwnerInfo) ? esc($OwnerInfo['name']) : set_value('txtName') ?>">
      </div>

      <div class="form-group">
        <label for="txtAddress">Address</label>
        <textarea class="form-control" id="txtAddress" name="txtAddress" required><?= isset($OwnerInfo) ? esc($OwnerInfo['address']) : set_value('txtAddress') ?></textarea>
      </div>

      <div class="form-group">
        <label for="txtNumberOfRooms">Number of Rooms</label>
        <input type="number" class="form-control" id="txtNumberOfRooms" name="txtNumberOfRooms" required value="<?= isset($OwnerInfo) ? esc($OwnerInfo['NumberOfRooms']) : set_value('txtNumberOfRooms') ?>">
      </div>

      <div class="form-group">
        <label for="txtPhoneNum">Phone Number</label>
        <input type="text" class="form-control" id="txtPhoneNum" name="txtPhoneNum" required value="<?= isset($OwnerInfo) ? esc($OwnerInfo['pNum']) : set_value('txtPhoneNum') ?>">
      </div>

      <div class="form-group">
        <label for="txtPrice">Price</label>
        <input type="number" class="form-control" id="txtPrice" name="txtPrice" required value="<?= isset($OwnerInfo) ? esc($OwnerInfo['price']) : set_value('txtPrice') ?>">
      </div>

      <div class="form-group">
        <label for="photos">Upload New Photos (JPEG, PNG, max 2MB each)</label>
        <input type="file" class="form-control" id="photos" name="photos[]" multiple accept="image/jpeg,image/png">
      </div>

      <?php if (isset($photos) && !empty($photos)): ?>
        <div class="form-group">
          <label>Existing Photos (Click Trash to Delete)</label>
          <div class="photo-preview">
            <?php foreach ($photos as $photo): ?>
              <div class="photo-item">
                <img src="http://192.168.1.14:8080<?= esc($photo['photoUrl']) ?>" alt="Boarding House Photo">
                <span class="delete-icon" data-photo-id="<?= esc($photo['photoID']) ?>" title="Delete">🗑</span>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>

      <div class="text-center mt-3">
        <?php if (isset($OwnerInfo)): ?>
          <button type="submit" class="btn btn-warning">Edit Information</button>
        <?php else: ?>
          <button type="submit" class="btn btn-primary">Add Information</button>
        <?php endif; ?>
      </div>
    </form>
  </div>
</div>

</body>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const icons = document.querySelectorAll('.delete-icon');
    icons.forEach(icon => {
      icon.addEventListener('click', function () {
        const photoID = this.getAttribute('data-photo-id');
        this.closest('.photo-item').remove();
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'delete_photos[]';
        input.value = photoID;
        document.querySelector('form').appendChild(input);
      });
    });
  });
</script>
</html>