<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DriverController extends Controller
{

    /** 
     * Show all drivers
     * 
     * @return JsonResponse
     */
    public function index(): JsonResponse 
    {
        $drivers = Driver::all();
        return response()->json($drivers);
    }

    /** 
    * Show one driver
    * 
    * @param String $id
    * @return JsonResponse
    */
    public function show(String $id): JsonResponse
    {
        $driver = Driver::find($id);
        return response()->json($driver);
    }

    /*
    |--------------------------------------------------------------------------
    | Streamed response for DriverController
    |--------------------------------------------------------------------------
    |
    | The streamIndex and streamOne methods are used to stream data from the
    | MongoDB change stream. The streamData method is used to create a
    | StreamedResponse object that streams data from the change stream.
    |
    */

    /**
     * Stream data from the change stream
     * 
     * @return StreamedResponse
     */
    public function streamIndex(): StreamedResponse
    {
        $changeStream = $this->getChangeStream();
        $callback = function() {
            return Driver::cursor();
        };
        return $this->streamData($changeStream, $callback);
    }

    /**
     * Stream data for one driver from the change stream
     * 
     * @param String $id
     * @return StreamedResponse
     */
    public function streamOne(String $id): StreamedResponse
    {
        $changeStream = $this->getChangeStream();
        $callback = function() use ($id) {
            return Driver::where('_id', object_id($id))->get()->first();
        };
        return $this->streamData($changeStream, $callback, $id);
    }

    /*
    |--------------------------------------------------------------------------
    | Helper methods for DriverController
    |--------------------------------------------------------------------------
    |
    | The getChangeStream method is used to get the MongoDB change stream.
    |
    */
    protected function getChangeStream()
    {
        return Driver::raw(function($collection) {
            return $collection->watch();
        });
    }
    
}
