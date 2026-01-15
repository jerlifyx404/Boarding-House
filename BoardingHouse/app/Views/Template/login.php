<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boarding House Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('https://img.freepik.com/free-photo/vintage-textured-paper-background_53876-124393.jpg');
            background-size: cover;
      background-position: center;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background-color: #FFFFFF;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.3);
            text-align: center;
            width: 350px;
            border: 4px solid #543A14;
        }
        .login-container h1 {
            font-size: 24px;
            color: #543A14;
            margin-bottom: 25px;
            font-weight: bold;
        }
        .login-container input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 2px solid #543A14;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }
        .login-button {
            width: 100%;
            background-color: #543A14;
            color: #FFF0DC;
            border: none;
            padding: 14px 0;
            font-size: 18px;
            border-radius: 5px;
            cursor: pointer;
        }
        .login-button:hover {
            background-color: #402B10;
        }
        .error-message {
            color: red;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>BOARDING HOUSE</h1>
        <form action="<?= base_url('/login') ?>" method="post">
            <?= csrf_field() ?>
            <input type="text" name="username" placeholder="Enter Your Username" required>
            <input type="password" name="password" placeholder="Enter Your Password" required>
            <button type="submit" class="login-button">LOGIN</button>
        </form>
        <?php if (isset($error)): ?>
            <p class="error-message"><?= esc($error) ?></p>
        <?php endif; ?>
    </div>
</body>
</html>