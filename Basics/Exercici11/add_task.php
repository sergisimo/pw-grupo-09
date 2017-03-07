<?php

  if (!empty($_GET)) {

      $db = new PDO('mysql:host=localhost;dbname=exercici11', 'testUser', '');
      $task = $_GET['activitat'];
      $done = false;

      $statement = $db->prepare("INSERT INTO Task (task, date, done) VALUES (:task, NOW(), :done)");

      $statement->bindParam(':task', $task, PDO::PARAM_STR);
      $statement->bindParam(':done', $done, PDO::PARAM_BOOL);

      $statement->execute();
  }
?>
<form action="add_task.php" method="get">
    Activitat: <input type="text" name="activitat"/>
    <input type="submit" name="submit" value="Send"/>
</form>
