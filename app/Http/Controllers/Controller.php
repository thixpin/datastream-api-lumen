<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Controller extends BaseController
{
    public function streamData($changeStream, $callback, $id = null): StreamedResponse
    {
        @ob_end_clean();
        ini_set('output_buffering', 0);

        $response = new StreamedResponse();        
        $response = $this->setStreamResponseHeaders($response);
        $response->sendHeaders();


        $response->setCallback(function () use ($changeStream, $callback, $id){

            $data = $id ? $callback($id) : $callback();
            $data = json_decode(json_encode($data), true);

            $isFirstTime = true;

            for ($changeStream->rewind(); true; $changeStream->next()) {
                if ( ! $changeStream->valid()) {
                    echo $isFirstTime 
                                ? json_encode([ "update" => true , "data" => $data ])
                                : json_encode([ "update" => false , "data" => [] ]);
                    $isFirstTime = false;
                    flush();
                    sleep(1);
                    continue;
                }

                $event = $changeStream->current();
                if ($event['operationType'] === 'invalidate') {
                    echo $isFirstTime 
                                ? json_encode([ "update" => true , "data" => $data ])
                                : json_encode([ "update" => false , "data" => [] ]);
                    $isFirstTime = false;
                    break;
                }
    
                if ($id ) {
                    if ( isset($event?->documentKey?->_id) && (string) $event->documentKey->_id === $id ) {
                        $data = $callback($id);
                        echo json_encode([ "update" => true , "data" => $data ]);
                    } else {
                        echo json_encode([ "update" => false , "data" => $data ]);
                    }
                } else {
                    $data = $callback();
                    echo json_encode([ "update" => true , "data" => $data ]);
                }
                $isFirstTime = false;
                flush();
                sleep(1);
                
            }
        });

        return $response;
    }

    protected function setStreamResponseHeaders(StreamedResponse $response): StreamedResponse
    {
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('Connection', 'keep-alive');
        $response->headers->set('X-Accel-Buffering', 'no');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        // $response->headers->set('Transfer-Encoding', 'chunked');

        return $response;
    }
}
