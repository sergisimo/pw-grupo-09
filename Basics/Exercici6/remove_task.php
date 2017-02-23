<?php

  if ($argc != 2) {
    echo "Usage: php remove_tasks.php [task]";
    exit (1);
  }

  $file = __DIR__."/tasks.txt";
  $content = file_get_contents($file);
  $contentExploded = explode("\n", $content);

  if (!in_array($argv[1], $contentExploded)) echo ("La tasca ".$argv[1]."no existeix");
  else {
    $key = array_search($argv[1], $contentExploded);
    array_splice($contentExploded, $key, 1);
    $content = implode("\n", $contentExploded);
    file_put_contents($file, $content);
    $file = __DIR__ ."/finished_tasks.txt";
    $content = file_get_contents($file);
    $content .= $argv[1] . "\n";
    file_put_contents($file, $content);
  }
?>
