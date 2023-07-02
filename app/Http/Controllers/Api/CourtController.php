<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Court;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class CourtController extends Controller
{
    public function index() {
        $courts = Court::all();
        return Response::json($courts, 200);
    }

    public function show($id) {
        $court = Court::findOrFail($id);
        if ($court) {
            return Response::json($court, 200);
        } else {
            return Response::json("The given court could not be found.", 400);
        }
    }
}
