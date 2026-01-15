<!DOCTYPE html>
<html>
<head>
  <title>Add Student</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

  <style>
    body {
      background-color: #f7f3ec;
      color: #5c4033; /* Dark brown text */
    }

    .form-container {
      max-width: 600px;
      margin: 50px auto;
      background-color: #fffaf0;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .form-control {
      background-color: #f5e1c8; /* Light brown */
      border: 1px solid #d2b48c;
      color: #543A14; /* Dark brown text */
    }

    .form-control::placeholder {
      color: #543A14; /* slightly lighter brown */
    }

    .btn-primary {
      background-color: #543A14;
      border-color: #543A14;
    }

    .btn-warning {
      background-color: #543A14;
      border-color: #543A14;
      color: white;
    }

    h4 small {
      color: #543A14;
    }

    label {
      font-weight: 600;
    }
  </style>
</head>
<body>

<div class="container">
  <div class="form-container">
      <!-- <h4><small>ADD STUDENT</small></h4> -->
      <hr>
      <?= session()->getFlashdata('error') ?>
          <?= validation_list_errors() ?>

          <!-- <?php
          // if(isset($UserInfo))
            // print_r($UserInfo);
          ?> -->

          <form action="<?= isset($UserInfo) ? base_url("BoardingHouse/updateUser") : base_url("BoardingHouse/insertUser");?>" method="post">
              <?= csrf_field() ?>

        <!-- <div class="form-group">
          placeholder= "Enter First Name">
        </div> -->


  <div class="form-group">
      <!-- <label for="">User ID:</label> -->
      <input type="hidden" class="form-control" id="txtUserID"  name="txtUserID"
      value="<?= isset($UserInfo) ? $UserInfo['userID'] : set_value('txtUserID')?>">
    </div>
  <div class="form-group">
      <label for="">Full Name:</label>
      <input type="text" class="form-control" id="txtFullName" placeholder="Enter Full Name" name="txtFullName"
      value="<?= isset($UserInfo) ? $UserInfo['fullName'] : set_value('txtFullName')?>">
    </div>
    <div class="form-group">
      <label for="">Username:</label>
      <input type="text" class="form-control" id="txtUsername" placeholder="Enter Username" name="txtUsername"
      value="<?= isset($UserInfo) ? $UserInfo['username'] : set_value('txtUsername')?>">
    </div>
    <div class="form-group">
      <label for="">Email:</label>
      <input type="text" class="form-control" id="txtEmail" placeholder="Enter Email" name="txtEmail"
      value="<?= isset($UserInfo) ? $UserInfo['email'] : set_value('txtEmail')?>">
    </div>
    <div class="form-group">
      <label for="">Password:</label>
      <input type="password" class="form-control" id="txtPassword" placeholder="Enter Password" name="txtPassword"
      value="<?= isset($UserInfo) ? $UserInfo['password'] : set_value('txtPassword')?>">
    </div>
    <div class="form-group">
      <label for="">UserType:</label>
      <input type="text" class="form-control" id="txtUserType" placeholder="Tenant or Owner" name="txtUserType"
      value="<?= isset($UserInfo) ? $UserInfo['userType'] : set_value('txtUserType')?>">
    </div>
    <div class="text-center">
      <?php
        if(isset($UserInfo))
          echo '<button type="submit" class="btn btn-warning">Edit User</button>';
        else
        echo '<button type="submit" class="btn btn-primary">Add User</button>';
      ?>
    </div>





    <!-- <button type="submit" class="btn btn-default">Submit</button> -->
  </form>
</div>


        </div>
      </div>
    </div>

</body>
</html>