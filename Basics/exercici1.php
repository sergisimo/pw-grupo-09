<?php

  if ($argc != 2) {
    echo "Usage: php exercici1.php [number]\n";
    exit(1);
  }

  echo "Taula del $argv[1]\n";
  for ($i = 0; $i <= 10; $i++){
    echo ("$argv[1]x$i = " . $argv[1]*$i . "\n");
  }
?>
