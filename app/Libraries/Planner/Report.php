<?php

namespace App\Libraries\Planner;

enum Verbosity: int
{
  case Fail = 0;
  case Warning = 1;
  case Info = 2;
  case Debug = 3;
  case Trace = 4;
}

$verbosity = Verbosity::Trace;

class Report
{

  public static function SetVerbosity(Verbosity $level)
  {
    global $verbosity;
    $verbosity = $level;
  }

  public static function Data($data)
  {
    echo "DATA: ";
    var_dump($data);
    echo "</br>";
  }

  public static function Fail($message)
  {
    global $verbosity;
    if ($verbosity >= 0)
      echo "FAIL: $message</br>";
  }

  public static function Warning($message)
  {
    global $verbosity;
    if ($verbosity >= 1)
      echo "WARN: $message</br>";
  }


  public static function Info($message)
  {
    global $verbosity;
    if ($verbosity >= 2)
      echo "INFO: $message</br>";
  }

  public static function Debug($message)
  {
    global $verbosity;
    if ($verbosity >= 3)
      echo "DBUG: $message</br>";
  }

  public static function Trace($message)
  {
    global $verbosity;
    if ($verbosity >= Verbosity::Trace)
      echo "TRACE: $message</br>";
  }

}