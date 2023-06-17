<?php

namespace App\Http\Controllers;

use App\Models\Court;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class CourtController extends Controller
{
    public $successStatus = 200;
    public $errorStatus = 400;

    public function index() {
        $courts = Court::all();
        return Response::json($courts, 200);
    }
}
