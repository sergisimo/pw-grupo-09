<?php

  if (strlen($_POST['username']) > 20 ||
      !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) ||
      !is_numeric($_POST['age']) ||
      (is_numeric($_POST['age']) && ($_POST['age'] < 1 || $_POST['age'] > 120)) ||
      empty($_FILES['myFile'])) {
    header("Location:exercici7.html");
  }
  else {
    $myFile = $_FILES['myFile'];
    $name = preg_replace("/[^A-Z0-9._-]/i", "_"; $myFile['name']);
    $success = move_uploaded_file($myFile['tmp_name'], 'uploads/' . $name)

    if (!$success) echo "No se ha podido guardar la imagen.";
    else chmod('uploads/' . $name, 0644);

    echo 'El nom es: ' . $_POST['username'] . '<br/>';
    echo 'El email es: ' . $_POST['email'] . '<br/>';
    echo 'La edat es: ' . $_POST['age'] . '<br/>';
  }

?>
