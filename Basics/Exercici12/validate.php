<?php

  if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $db = new PDO('mysql:host=localhost;dbname=esteve', 'root', '');
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $statement = $db->prepare ("SELECT * FROM usuaris WHERE email=:email AND password= :password");
    $statement->bindParam(':email', $_POST['email'], PDO::PARAM_STR);
    $statement->bindParam(':password', $_POST['password'], PDO::PARAM_STR);
    $statement->execute();
    $results = $statement->fetchAll(PDO::FETCH_ASSOC);
    header("Location: benvinguda.html");
    } else {
    header("Location: LogIn.html");
  }

?>
