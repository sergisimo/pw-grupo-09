<?php

  if (strlen($_POST['username']) > 20 || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) || !is_numeric($_POST['age']) || (is_numeric($_POST['age']) && ($_POST['age'] < 1 || $_POST['age'] > 120))) {
    header("Location:exercici7.html");
  } else {
    echo 'El nom es: ' . $_POST['username'] . '<br/>';
    echo 'El email es: ' . $_POST['email'] . '<br/>';
    echo 'La edat es: ' . $_POST['age'] . '<br/>';
  }

?>
