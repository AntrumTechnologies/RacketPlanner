<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    public function Trace($message)
    {
        Log::debug($trace);
    }
  
    public function Debug($message)
    {
        Log::debug($message);
    }
  
    public function Info($message)
    {
        Log::info($message);
    }
  
    public function Fail($message)
    {
        Log::error($message);
    }
  
    public function Data($data)
    {
        Log::notice($data);
    }
}
