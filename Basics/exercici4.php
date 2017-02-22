<?php

  $words = array();

  for ($i = 0; $i < $argc - 1; $i++) {
    if ($words[$argv[$i + 1]] == NULL) $words[$argv[$i + 1]] = 1;
    else $words[$argv[$i + 1]]++;
  }

  echo nl2br("\n");

  foreach($words as $key => $value) echo nl2br($key . " té les següents aparicions: " . $value . "\n");

  echo nl2br("\n\n" . count($words) . " paraules\n");
?>
