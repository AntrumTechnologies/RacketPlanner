<?php

namespace App\Libraries\Planner;

use Illuminate\Support\Facades\Log;

enum Verbosity: int
{
  case Fail = 0;
  case Warning = 1;
  case Info = 2;
  case Debug = 3;
  case Trace = 4;
}

$verbosity = Verbosity::Debug;

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
    if ($verbosity->value >= Verbosity::Fail->value)
      Log::error($message);
  }

  public static function Warning($message)
  {
    global $verbosity;
    if ($verbosity->value >= Verbosity::Warning->value)
      Log::warning($message);
  }


  public static function Info($message)
  {
    global $verbosity;
    if ($verbosity->value >= Verbosity::Info->value)
      Log::info($message);
  }

  public static function Debug($message)
  {
    global $verbosity;
    if ($verbosity->value >= Verbosity::Debug->value)
      Log::debug($message);
  }

  public static function Trace($message)
  {
    global $verbosity;
    if ($verbosity->value >= Verbosity::Trace->value)
      echo "TRACE: $message</br>";
  }

}