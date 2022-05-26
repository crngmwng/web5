<?php

/**
 * Файл login.php для не авторизованного пользователя выводит форму логина.
 * При отправке формы проверяет логин/пароль и создает сессию,
 * записывает в нее логин и id пользователя.
 * После авторизации пользователь перенаправляется на главную страницу
 * для изменения ранее введенных данных.
 **/

// Отправляем браузеру правильную кодировку,
// файл login.php должен быть в кодировке UTF-8 без BOM.
header('Content-Type: text/html; charset=UTF-8');

// Начинаем сессию.
session_start();

// В суперглобальном массиве $_SESSION хранятся переменные сессии.
// Будем сохранять туда логин после успешной авторизации.
if (!empty($_SESSION['login'])) {
  // Если есть логин в сессии, то пользователь уже авторизован.
  // TODO: Сделать выход (окончание сессии вызовом session_destroy()
  //при нажатии на кнопку Выход).
  session_destroy();
  // Делаем перенаправление на форму.
  header('Location: ./');
}

// В суперглобальном массиве $_SERVER PHP сохраняет некторые заголовки запроса HTTP
// и другие сведения о клиненте и сервере, например метод текущего запроса $_SERVER['REQUEST_METHOD'].
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
   $errors = array();
   $errors['log_error'] = !empty($_COOKIE['log_error']);
  if ($errors['log_error']) {
    setcookie('log_error', '', 100000);
    printf('Неверный логин и пароль. Попробуйте снова.');
  }
  
?>

<form action="" method="post">
  <input name="login" />
  <input name="pass" />
  <input type="submit" value="Войти" />
</form>

<?php
}
// Иначе, если запрос был методом POST, т.е. нужно сделать авторизацию с записью логина в сессию.
else {

  // TODO: Проверть есть ли такой логин и пароль в базе данных.
  // Выдать сообщение об ошибках.
  $user = 'u47590';
$pass = '3205407';
  $login = $_POST['login'];
  $password = ($_POST['pass']);
  
$db = new PDO('mysql:host=localhost;dbname=u47590', $user, $pass, array(PDO::ATTR_PERSISTENT => true));
  $stmt = $db->prepare("SELECT * FROM app WHERE login=:login and password =:password");
  $stmt -> bindParam(':login', $login);
  $stmt -> bindParam(':password', $password);
  $stmt->execute();
  $count = 0;
  foreach ($stmt as $row) {
    $count = 1;
  }

  if ($count){
  // Если все ок, то авторизуем пользователя.
  $_SESSION['login'] = $_POST['login'];
  // Записываем ID пользователя.
  $_SESSION['uid'] = 123;

  // Делаем перенаправление.
  header('Location: ./');
}
  else {
    setcookie('log_error', '1', time()+24*60*60);
  $errors = TRUE;
  }
  if ($errors) {
    // При наличии ошибок перезагружаем страницу и завершаем работу скрипта.
    header('Location: login.php');
    exit();
    }
}
