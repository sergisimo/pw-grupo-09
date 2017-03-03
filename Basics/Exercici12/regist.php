<?php

 list($yyyy,$mm,$dd) = explode('-',$_POST['naixament']);

  $letra = substr($_POST['DNI'], -1);
	$numeros = substr($_POST['DNI'], 0, -1);

  if (ctype_alnum ($_POST['nom'])
  && ctype_alnum ($_POST['cognom'])
  && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)
  && !strcmp ($_POST['password1'] , $_POST['password2'] )
  && preg_match('/^(?=.*\d)(?=.*[@#\-_$%^&+=§!\?])(?=.*[a-z])(?=.*[A-Z])[0-9A-Za-z@#\-_$%^&+=§!\?]{6,20}$/',$_POST['password1'])
  && checkdate($mm,$dd,$yyyy)
  && ( substr("TRWAGMYFPDXBNJZSQVHLCKE", $numeros%23, 1) == $letra && strlen($letra) == 1 && strlen ($numeros) == 8 )
  && isset($_POST['condicions']) && ($_POST['condicions'] == '1')) {

    $db = new PDO('mysql:host=localhost;dbname=esteve', 'root', '');
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $statement = $db->prepare("INSERT INTO usuaris(nom, cognom, email, password, DNI, date)
    VALUES(:nom, :cognom, :email, :password, :DNI, :date)");
    $statement->execute(array(
    "nom" => $_POST['nom'],
    "cognom" => $_POST['cognom'],
    "email" => $_POST['email'],
    "password" => $_POST['password1'],
    "DNI" => $_POST['DNI'],
    "date" => $_POST['naixament']));
    header("Location: index.html");

  } else {
    header("Location: index.html");
  }

?>
