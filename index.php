<?php
/**
 * Реализовать возможность входа с паролем и логином с использованием
 * сессии для изменения отправленных данных в предыдущей задаче,
 * пароль и логин генерируются автоматически при первоначальной отправке формы.
 */

// Отправляем браузеру правильную кодировку,
// файл index.php должен быть в кодировке UTF-8 без BOM.
header('Content-Type: text/html; charset=UTF-8');

// В суперглобальном массиве $_SERVER PHP сохраняет некторые заголовки запроса HTTP
// и другие сведения о клиненте и сервере, например метод текущего запроса $_SERVER['REQUEST_METHOD'].
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  // Массив для временного хранения сообщений пользователю.
  $messages = array();

  // В суперглобальном массиве $_COOKIE PHP хранит все имена и значения куки текущего запроса.
  // Выдаем сообщение об успешном сохранении.
  if (!empty($_COOKIE['save'])) {
    // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('save', '', 100000);
    setcookie('login', '', 100000);
    setcookie('password', '', 100000);
    // Выводим сообщение пользователю.
    $messages[] = 'Спасибо, результаты сохранены.';
    // Если в куках есть пароль, то выводим сообщение.
    if (!empty($_COOKIE['password'])) {
      $messages[] = sprintf('Вы можете <a href="login.php">войти</a> с логином <strong>%s</strong>
        и паролем <strong>%s</strong> для изменения данных.',
        strip_tags($_COOKIE['login']),
        strip_tags($_COOKIE['password']));
    }
  }

  // Складываем признак ошибок в массив.
  $errors = array();
  $errors['name_empty'] = !empty($_COOKIE['name_error']);
	$errors['name_wrong'] = !empty($_COOKIE['name_err']);
	$errors['email'] = !empty($_COOKIE['email_error']);
	$errors['email_empty'] = !empty($_COOKIE['email_empty']);
	$errors['bio_empty'] = !empty($_COOKIE['bio_empty']);

  // TODO: аналогично все поля.

  // Выдаем сообщения об ошибках.
  if ($errors['name_empty']) {
    // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('name_error', '', 100000);
    // Выводим сообщение.
    $messages[] = '<div class="error">Заполните имя.</div>';
  }
	 if ($errors['name_wrong']) {
    setcookie('name_err', '', 100000);
    $messages[] = '<div class="error">Заполните имя правильно.</div>';
  }
	
  if ($errors['email']) {
    setcookie('email_error', '', 100000);
    $messages[] = '<div class="error">Введите корректный email.</div>';
  }
	if ($errors['email_empty']) {
    setcookie('email_error', '', 100000);
    $messages[] = '<div class="error">Введите email.</div>';
  }
	if($errors['bio_empty']) {
	setcookie('bio_empty', '', 100000);
    $messages[] = '<div class="error">Введите биографию.</div>';
	}
  // TODO: тут выдать сообщения об ошибках в других полях.

  // Складываем предыдущие значения полей в массив, если есть.
  // При этом санитизуем все данные для безопасного отображения в браузере.
  $values = array();
 $values['name'] = empty($_COOKIE['name_value']) ? '' : $_COOKIE['name_value'];
$values['email'] = empty($_COOKIE['email_value']) ? '' : $_COOKIE['email_value'];
	$values['bio'] = empty($_COOKIE['bio_value']) ? '' : $_COOKIE['bio_value'];
  // TODO: аналогично все поля.

  // Если нет предыдущих ошибок ввода, есть кука сессии, начали сессию и
  // ранее в сессию записан факт успешного логина.
  if (//empty($errors) && !empty($_COOKIE[session_name()]) &&
      session_start() && !empty($_SESSION['login'])) {
    // TODO: загрузить данные пользователя из БД
    // и заполнить переменную $values,
    // предварительно санитизовав.
    printf('Вход с логином %s, uid %d', $_SESSION['login'], $_SESSION['uid']);
	  $messages[] = sprintf(' <a href="login.php">Выйти</a>');
  }

  // Включаем содержимое файла form.php.
  // В нем будут доступны переменные $messages, $errors и $values для вывода 
  // сообщений, полей с ранее заполненными данными и признаками ошибок.
  include('form.php');
}
// Иначе, если запрос был методом POST, т.е. нужно проверить данные и сохранить их в XML-файл.
else {
  // Проверяем ошибки.
  $errors = FALSE;
  if (empty($_POST['name'])) {
    // Выдаем куку на день с флажком об ошибке в поле fio.
    setcookie('name_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
    // Сохраняем ранее введенное в форму значение на месяц.
    setcookie('name_value', $_POST['name'], time() + 30 * 24 * 60 * 60);
  }
if( !preg_match("/^[a-zа-яё]+$/i", $_POST['name'])) {
	setcookie('name_err', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
} 
	 else {
    setcookie('name_value', $_POST['name'], time() + 30 * 24 * 60 * 60);
  }


if (empty($_POST['email'])) {
 setcookie('email_empty', '1', time()+24*60*60);
  $errors = TRUE;
}
	else {
    setcookie('email_value', $_POST['email'], time() + 30 * 24 * 60 * 60);
  }

if (!preg_match("/^([a-z0-9_-]+\.)*[a-z0-9_-]+@[a-z0-9_-]+(\.[a-z0-9_-]+)*\.[a-z]{2,6}$/", $_POST['email'])){
  setcookie('email_error', '1', time()+24*60*60);
  $errors = TRUE;
}
	else {
    setcookie('email_value', $_POST['email'], time() + 30 * 24 * 60 * 60);
  }



	if (empty($_POST['bio'])) {
 setcookie('bio_empty', '1', time()+24*60*60);
  $errors = TRUE;
}
	else {
    setcookie('bio_value', $_POST['bio'], time() + 30 * 24 * 60 * 60);
  }

// *************
// TODO: тут необходимо проверить правильность заполнения всех остальных полей.
// Сохранить в Cookie признаки ошибок и значения полей.
// *************

  if ($errors) {
    // При наличии ошибок перезагружаем страницу и завершаем работу скрипта.
    header('Location: index.php');
    exit();
  }
  else {
    // Удаляем Cookies с признаками ошибок.
   setcookie('name_error', '', 100000);
	setcookie('email_error', '', 100000);
	setcookie('name_err', '', 100000);
	  setcookie('email_empty', '', 100000);
	  setcookie('bio_empty', '', 100000);
    // TODO: тут необходимо удалить остальные Cookies.
  }

  // Проверяем меняются ли ранее сохраненные данные или отправляются новые.
  if (!empty($_COOKIE[session_name()]) &&
      session_start() && !empty($_SESSION['login'])) {
    // TODO: перезаписать данные в БД новыми данными,
    // кроме логина и пароля.
  }
  else {
    // Генерируем уникальный логин и пароль.
    // TODO: сделать механизм генерации, например функциями rand(), uniquid(), md5(), substr().
    $password = '';
  for($length = 0; $length < 6; $length++) {
    $password .= chr(rand(48, 122));
  }
$login = '';
for($length = 0; $length < 6; $length++) {
    $login .= chr(rand(48, 122));
  }
    // Сохраняем в Cookies.
    setcookie('login', $login);
    setcookie('password', $password);

    // TODO: Сохранение данных формы, логина и хеш md5() пароля в базу данных.
    $user = 'u47590';
$pass = '3205407';
$db = new PDO('mysql:host=localhost;dbname=u47590', $user, $pass, array(PDO::ATTR_PERSISTENT => true));

try {
  $stmt = $db->prepare("INSERT INTO app (login, password, name, email, year, sex, limbs, ability_immortality, ability_pass_thr_walls, ability_levitation, bio, checkbox ) 
  VALUES (:login, :pass, :name, :email, :year, :sex, :limbs, :imm, :walls, :lev, :bio, :checkbox)");
	$stmt -> bindParam(':pass', $password);
	$stmt -> bindParam(':login', $login);
  $stmt -> bindParam(':name', $name);
  $stmt -> bindParam(':email', $email);
  $stmt -> bindParam(':year', $year);
  $stmt -> bindParam(':sex', $sex);
  $stmt -> bindParam(':limbs', $limbs);
  $stmt -> bindParam(':imm', $imm);
  $stmt -> bindParam(':walls', $walls);
  $stmt -> bindParam(':lev', $lev);
  $stmt -> bindParam(':bio', $bio);
  $stmt -> bindParam(':checkbox', $checkbox);

  $name = $_POST['name'];
  $email = $_POST['email'];
  $year = $_POST['year'];
  $sex = $_POST['radio-group-1'];
  $limbs = $_POST['radio-group-2'];
	
   $imm = $_POST['power'];
   $walls = $_POST['power'];
   $lev = $_POST['power'];
	
  $bio = $_POST['bio'];

  if (empty($_POST['check-1']))
    $checkbox = "No";
  else
    $checkbox = $_POST['check-1'];

  
  $stmt -> execute();
}
catch(PDOException $e){
  print('Error : ' . $e->getMessage());
  exit();
}
  }

  // Сохраняем куку с признаком успешного сохранения.
  setcookie('save', '1');

  // Делаем перенаправление.
  header('Location: ./');
}
