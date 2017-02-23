<?php
  if ($argc !== 2) {
    echo "No se han pasado los argumentos correctamente.";
    exit(1);
  }

  $tmp = explode(" ", $argv[1]);
  $words = array();

  for ($i = 0; $i < count($tmp); $i++) {
    if ($words[$tmp[$i]] == NULL) $words[$tmp[$i]] = 1;
    else $words[$tmp[$i]]++;
  }

  foreach($words as $key => $value) echo ($key . " té les següents aparicions: " . $value . "\n");

  echo ("\n" . count($words) . " paraules\n");
?>
