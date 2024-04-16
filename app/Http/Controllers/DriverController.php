<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DriverController extends Controller
{

    public function index(): JsonResponse 
    {
        $drivers = Driver::all();
        return response()->json($drivers);
    }

    public function show(String $id): JsonResponse
    {
        $driver = Driver::find($id);
        return response()->json($driver);
    }

    public function streamIndex(): StreamedResponse
    {
        $changeStream = $this->getChangeStream();
        $callback = function() {
            return Driver::cursor();
        };
        return $this->streamData($changeStream, $callback);
    }

    public function streamOne(String $id): StreamedResponse
    {
        $changeStream = $this->getChangeStream();
        $callback = function() use ($id) {
            return Driver::where('_id', object_id($id))->get()->first();
        };
        return $this->streamData($changeStream, $callback, $id);
    }

    protected function getChangeStream()
    {
        return Driver::raw(function($collection) {
            return $collection->watch();
        });
    }
    
}
