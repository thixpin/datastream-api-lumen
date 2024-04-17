<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Driver;
use App\Enums\OrderStatus;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
   
    /** 
     * Show all orders
     * 
     * @return JsonResponse
     */
    public function index(): JsonResponse 
    {
        $orders = Order::all();
        return response()->json($orders);
    }

    /** 
     * Show one order
     * 
     * @param String $id
     * @return JsonResponse
     */
    public function show(String $id): JsonResponse
    {
        $order = Order::find($id);
        return response()->json($order);
    }

    /**
     * Create a new order
     * 
     * @return JsonResponse
     */
    public function store(): JsonResponse
    {
        // Get the first customer to create an order
        $customer = Customer::select('_id')->first();

        // If no customer is found, return an error message
        if (!$customer) {
            return response()->json(['message' => 'Please seed data first'], 406);
        }

        // Create a new order
        $order = Order::create([
            'order_location_point' => generateRandomCoordinates(),
            'customer_id' => $customer->_id,
            'status' => OrderStatus::CREATED,
            'steps' => rand(3, 20),
            'duration' => rand(10, 60),
            'completed_steps' => 0,
        ]);
        
        /**
         * If the order is created, return the order ID
         * If the order is not created, return an error message
         */
        $orderId = $order->_id;
        return $orderId 
            ? response()->json(['_id' => $orderId], 201)
            : response()->json(['error' => 'Order not created'], 500);

    }

    /**
     * Cancel an order
     * 
     * @param String $id
     * @return JsonResponse
     */
    public function cancel(String $id): JsonResponse
    {
        // Find the order by ID
        $order = Order::find($id);

        // If the order is not found, return an error message
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        // If the order status is not CREATED or PROCESSING, return an error message
        if ( !in_array($order->status, [OrderStatus::CREATED->value, OrderStatus::PROCESSING->value]) ) {
            return response()->json(['error' => 'Order cannot be canceled'], 400);
        }

        // Find the driver assigned to the order
        try {
            $driver = Driver::where('current_order_id', object_id($id))->first();
            if ($order->status === OrderStatus::PROCESSING->value) {
                $driver = Driver::where('current_order_id', object_id($id))->first();
                
                // If the driver is found, remove the current order ID and current order from the driver
                if($driver) {
                    unset($driver->current_order_id);
                    unset($driver->current_order);
                    $driver->save();
                }
            }
            $order->status = OrderStatus::CANCELED;
            $order->save();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['error' => 'Order not canceled'], 500);
        }
        return response()->json(['message' => 'Order canceled', 'order' => $order, 'driver' => $driver]);
    }

    /**
     * Streamed response for OrderController
     * 
     * The streamIndex and streamOne methods are used to stream data from the
     * MongoDB change stream. The streamData method is used to create a
     * StreamedResponse object that streams data from the change stream.
     * 
     */
    public function streamIndex(): StreamedResponse
    {
        $changeStream = $this->getChangeStream();
        $callback = function() {
            return Order::cursor();
        };
        return $this->streamData($changeStream, $callback);
    }

    /**
     * Stream data for one order from the change stream
     * 
     * @param String $id
     * @return StreamedResponse
     */
    public function streamOne(String $id): StreamedResponse
    {
        $changeStream = $this->getChangeStream();
        $callback = function() use ($id) {
            return Order::where('_id', object_id($id))->get()->first();
        };
        return $this->streamData($changeStream, $callback, $id);
    }

    /**
     * Get the MongoDB change stream
     * 
     * @return ChangeStream
     */
    protected function getChangeStream()
    {
        return Order::raw(function($collection) {
            return $collection->watch();
        });
    }
    
}
