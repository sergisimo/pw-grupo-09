<?php

  if ($argc != 2) {
    echo "Usage: php add_task.php [task]\n";
    exit(1);
  }

  $file = __DIR__ ."/tasks.txt";
  $content = file_get_contents($file);
  $content .= $argv[1] . "\n";
  file_put_contents($file, $content);

?>
