<?php

  if (!empty($_GET)){
      echo 'The activity is: ' . $_GET['activitat'] . '<br>';
      $file = __DIR__ ."/tasks.txt";
      $content = file_get_contents($file);
      $content .= $_GET['activitat'] . "\n";
      file_put_contents($file, $content);
  }
?>
<form action="add_task.php" method="get">
    Activitat: <input type="text" name="activitat"/>
    <input type="submit" name="submit" value="Send"/>
</form>
