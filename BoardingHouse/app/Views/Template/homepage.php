<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Boarding House</title>
  <style>
    /* Center the entire banner on the page */
    body {
      margin: 0;
      padding: 0;
      background-image: url('https://img.freepik.com/free-photo/vintage-textured-paper-background_53876-124393.jpg');
      background-size: cover;
      background-position: center;
      font-family: Arial, sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    /* Main container for the banner */
    .promo-container {
      display: flex;
      align-items: center;
      padding: 40px;
    }

    /* Text container on the left side */
    .promo-text {
      max-width: 60%;
    }
    .promo-text h1 {
      margin: 0;
      font-size: 4.5rem; /* Enlarged heading */
      font-weight: bold;
      color: #000;
    }
    /* .promo-text p { */
      /* margin-top: 10px; */
      /* font-size: 1.5rem; Enlarged subheading */
      /* color: #543A14; */
    /* } */

    /* Icon container on the right side */
    .icon-container {
      position: relative;
      width: 350px; /* Adjusted base icon container size */
      margin-left: 30px; /* Reduced margin to bring closer to text */
    }
    .icon-container .base-icon {
      display: block;
      width: 140%; /* Adjusted base icon size */
      height: auto;
      margin-left: -50px;
    }
    .icon-container .marker {
      position: absolute;
      width: 140px; /* Adjusted marker icon size */
      top: -30px; /* Adjusted positioning */
      right: -40px; /* Reduced the right offset */
    }
  </style>
</head>
<body>
  <div class="promo-container">
    <div class="promo-text">
      <a href="/login" style="text-decoration: none; color:rgb(143, 105, 49);"><h1>EASIER BOARDING, BETTER LIVING!</h1></a>
    </div>
    <div class="icon-container">
      <!-- Base house icon -->
      <img src="https://upload.wikimedia.org/wikipedia/commons/5/50/Home_icon_brown.png" alt="House Icon" class="base-icon">
      <!-- Overlaid location marker -->
      <img src="https://www.cafebrittaustralia.com.au/images/britt_icons_place.png" alt="Location Marker" class="marker">
    </div>
  </div>
</body>
</html>