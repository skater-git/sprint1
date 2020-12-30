<?php
session_start();

if(isset($_GET['action']) and $_GET['action'] == 'logout'){
    session_start();
    unset($_SESSION['username']);
    unset($_SESSION['password']);
    unset($_SESSION['logged_in']);
    print('Logged out!');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h2> Enter Username and Password</h2>
    <div>
        <?php
        $msg = '';
        if (isset($_POST['login']) && !empty($_POST['username'])&& !empty($_POST['password'])){
        if ($_POST['username'] == 'admin' && $_POST['password'] == 'admin123'){
            $_SESSION['logged_in'] = true;
            $_SESSION['timeout'] = time();
            $_SESSION['username'] = 'admin';
            echo 'You have entered valid username and password';
        } else {
            $msg = 'Wrong username or password';
        }
        }
    ?>
    </div>
    <div>
        <?php
        if ($_SESSION['logged_in'] == true){
            header('Location: index.php');
        }
        ?>
        <form action="" method="post">
            <h4><?php echo $msg; ?></h4>
            <input type="text" name="username" placeholder="username" required autofocus></br>
            <input type="password" name="password" placeholder="password" required>
            <button class="btn btn-lg btn-primary btn-block" type="submit" name="login">Login</button>
        </form>
    </div>
</body>
</html>