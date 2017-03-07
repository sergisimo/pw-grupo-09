<?php

  $db = new PDO('mysql:host=localhost;dbname=exercici11', 'testUser', '');
  if (!empty($_GET)){

    $id = $_GET['num'];
    $statement = $db->prepare("UPDATE Task SET done = true WHERE id = $id");
    $statement->execute();
  }
  $tasks = $db->query('SELECT * FROM Task');
  foreach ($tasks as $task) {
    if ($task['done']) $done = 'DONE';
    else $done = 'TO DO';
    echo $task['id'] . '. ' . $task['task'] . ' [ ' . $done . ' ]<br/>' ;
  }
  echo '<br/><br/>';
?>

<form action="remove_task.php" method="get">
    Num: <input type="number" min="1" name="num"/>
    <input type="submit" name="submit" value="Send"/>
</form>
