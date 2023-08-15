<?php

namespace App\Libraries\Planner;

require_once 'Report.php';

use App\Libraries\Planner\Report;
use mysqli;

class Database
{
  private $sql;
  private $fetchResult;

  protected function Connect($server, $username, $password, $database)
  {
    Report::Trace(__METHOD__);

    mysqli_report(MYSQLI_REPORT_ALL ^ MYSQLI_REPORT_STRICT);
    $this->sql = FALSE;
    $this->fetchResult = FALSE;

    try
    {
      $this->sql = new mysqli($server, $username, $password, $database);
    }
    catch (\Exception $e)
    {
      Report::Fail("SQL: Unable to connect to database '$database'.");
      die();
    }
    Report::Info("Succesfully connected to database");
  }

  protected function Disconnect()
  {
    Report::Trace(__METHOD__);

    if ($this->sql != FALSE)
    {
      $this->sql->close();
    }
    $this->sql = FALSE;
  }

  public function __destruct()
  {
    Report::Trace(__METHOD__);

    $this->Disconnect();
  }

  public function __construct()
  {
    Report::Trace(__METHOD__);

    $server = getenv('DB_HOST');
    $database = getenv('DB_DATABASE');
    $username = getenv('DB_USERNAME');
    $password = getenv('DB_PASSWORD');

    $this->Connect($server, $username, $password, $database);
  }

  function Query($sql)
  {
    Report::Trace(__METHOD__);
    Report::Debug("QUERY: $sql");

    try
    {
      $result = $this->sql->query($sql);
      $type = gettype($result);
      if ($type == "object")
      {
        Report::Debug("Query returned records.");
        return $result->fetch_all(MYSQLI_ASSOC);
      }

      if ($result == true)
      {
        Report::Debug("Query passed.");
        return true;
      }

      Report::Fail("$sql");
      return false;
    }
    catch (\Exception $e)
    {
      Report::Fail("SQL: Error executing query: $sql");
    }

    return false;
  }

  public function Insert($table, $record)
  {
    Report::Trace(__METHOD__);

    $sql = "INSERT INTO `" . strtolower($table) . "` (";

    $i = 0;
    $keys = array_keys($record);
    foreach ($keys as $key)
    {
      if ($i++ > 0) // Add comma in case of multiple keys
        $sql .= ", ";
      $sql .= "`" . $key . "`";
    }

    $sql .= ") VALUES (";

    $i = 0;
    $values = array_values($record);
    foreach ($values as $value)
    {
      if ($i++ > 0) // Add comma in case of multiple keys
        $sql .= ", ";

      settype($value, "string");
      $sql .= "'";
      $sql .= $this->sql->real_escape_string($value);
      $sql .= "'";
    }

    $sql .= ");";

    return $this->Query($sql);
  }

  function Delete($table, $filter = null)
  {
    Report::Trace(__METHOD__);

    $sql = "DELETE FROM `" . strtolower($table) . "` WHERE ";

    if ($filter == null)
    {
      $sql .= "1;";
    }
    else
    {
      $i = 0;
      foreach ($filter as $key => $value)
      {
        if ($i++ > 0)
          $sql .= " AND ";

        $sql .= "`" . $key . "`='" . $this->sql->real_escape_string($value) . "'";
      }
    }

    return $this->Query($sql);
  }

  function Select($table, $filter = null)
  {
    Report::Trace(__METHOD__);

    $sql = "SELECT * FROM `" . strtolower($table) . "` WHERE ";

    if ($filter == null)
    {
      $sql .= "1;";
    }
    else
    {
      $i = 0;
      foreach ($filter as $key => $value)
      {
        if ($i++ > 0)
          $sql .= " AND ";

        $sql .= "`" . $key . "`='" . $this->sql->real_escape_string($value) . "'";
      }
    }

    return $this->Query($sql);
  }

  function Update($table, $record, $filter = null)
  {
    Report::Trace(__METHOD__);

    if ($record == null)
      return true;

    $sql = "UPDATE `" . strtolower($table) . "` SET ";

    $i = 0;
    foreach ($record as $key => $value)
    {
      if ($i++ > 0)
        $sql .= ", ";
      $sql .= "`" . $key . "`=";
      if (isset($value))
        $sql .= "'" . $this->sql->real_escape_string($value) . "'";
      else
        $sql .= "NULL";
    }
    $sql .= " WHERE ";

    if ($filter == null)
    {
      $sql .= "1;";
    }
    else
    {
      $i = 0;
      foreach ($filter as $key => $value)
      {
        if ($i++ > 0)
          $sql .= " AND ";
        $sql .= "`" . $key . "`='" . $this->sql->real_escape_string($value) . "'";
      }
    }

    return $this->Query($sql);
  }
}