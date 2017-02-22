<?php

  $numbers = array();

  for ($i = 0; $i < $argc - 2; $i++) array_push($numbers, $argv[$i + 2]);

  function sumar() {

    $operands = func_get_args()[0];
    $result = 0;

    for ($i = 0; $i < count($operands); $i++) $result += intval($operands[$i]);

    return ($result);
  }

  function restar() {

    $operands = func_get_args()[0];
    $result = intval($operands[0]);

    for ($i = 1; $i < count($operands); $i++) $result -= intval($operands[$i]);

    return ($result);
  }

  function multiplicar() {

    $operands = func_get_args()[0];
    $result = intval($operands[0]);

    for ($i = 1; $i < count($operands); $i++) $result *= intval($operands[$i]);

    return ($result);
  }

  function dividir() {

    $operands = func_get_args()[0];
    $result = intval($operands[0]);

    for ($i = 1; $i < count($operands); $i++) {
      if (intval($operands[$i]) == 0) return "INDETERMINACIÓ";

      $result /= intval($operands[$i]);
    }

    return ($result);
  }

  switch ($argv[1]) {

    case 'sumar':
        echo(sumar($numbers));
        break;

    case 'restar':
        echo(restar($numbers));
        break;

    case 'multiplicar':
        echo(multiplicar($numbers));
        break;

    case 'dividir':
        echo(dividir($numbers));
        break;

    default:
    echo ("Error, s'ha d'introduir: sumar o restar o dividir o multiplicar.");
  }
?>
