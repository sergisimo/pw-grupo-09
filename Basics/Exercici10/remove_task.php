<?php

  if (!empty($_GET)){

      $file = __DIR__."/tasks.txt";
      $content = file_get_contents($file);
      $contentExploded = explode("\n", $content);
      $n = count($contentExploded);
      if ($n >= 0 && ($n-1) >= $_GET['num']){
        echo 'The activity number is: ' . $_GET['num'] . '<br>';
        array_splice($contentExploded, $_GET['num'], 1);
        $content = implode("\n", $contentExploded);
        file_put_contents($file, $content);

      }else {
        echo "Incorrect number!";
      }


  }

?>

<form action="remove_task.php" method="get">
    Num: <input type="text" name="num"/>
    <input type="submit" name="submit" value="Send"/>
</form>
