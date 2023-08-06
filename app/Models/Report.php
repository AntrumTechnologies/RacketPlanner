<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Report extends Model
{
    use HasFactory;

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

}
