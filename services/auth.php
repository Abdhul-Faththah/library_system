<?php
include __DIR__ . '/../config.php';
include __DIR__ . '/../helpers/AppManager.php';

$pm = AppManager::getPM();
$sm = AppManager::getSM();

$email = $_POST['email'];
$password = $_POST['password'];

if (empty($email) || empty($password)) {
    $sm->setAttribute("error", 'Please fill all required fields!');
    header("Location: " . $_SERVER['HTTP_REFERER']);
} else {
    $param = array(':email' => $email);
    $user = $pm->run("SELECT * FROM users WHERE email = :email", $param, true);
    if ($user != null) {
        $correct = password_verify($password, $user['password']);
        if ($correct) {

            $sm->setAttribute("userId", $user['id']);
            $sm->setAttribute("username", $user['username']);
            $sm->setAttribute("permission", $user['permission']);

            header('location: ../index.php');
            exit;
        } else {
            $sm->setAttribute("error", 'Invalid username or password!');
        }
    } else {
        $sm->setAttribute("error", 'Invalid username or password!');
    }
    header("Location: " . $_SERVER['HTTP_REFERER']);
}
exit;
