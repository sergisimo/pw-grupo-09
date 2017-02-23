<?php

  $file = file_get_contents(__DIR__."/finished_tasks.txt");
  $file = substr($file, 0, strlen($file) - 1);
  echo $file;

?>
