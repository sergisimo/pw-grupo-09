<?php

  function sumar($a, $b) {

    return ($a + $b);
  }

  function restar($a, $b) {

    return ($a - $b);
  }

  function multiplicar($a, $b) {

    return ($a * $b);
  }

  function dividir($a, $b) {

    if ($b == 0){
      return "INDETERMINACIO";
    }
    return ($a / $b);
  }

  switch ($argv[1]) {

    case 'sumar':
        echo($argv[2] . " + " . $argv[3] . " = " . sumar($argv[2], $argv[3]));
        break;

    case 'restar':
        echo($argv[2] . " - " . $argv[3] . " = " . restar($argv[2], $argv[3]));
        break;

    case 'multiplicar':
        echo($argv[2] . " * " . $argv[3] . " = " . multiplicar($argv[2], $argv[3]));
        break;

    case 'dividir':
        echo($argv[2] . " / " . $argv[3] . " = " . dividir($argv[2], $argv[3]));
        break;

    default:
    echo ("Error, s'ha d'introduir: sumar o restar o dividir o multiplicar.");
  }
?>
