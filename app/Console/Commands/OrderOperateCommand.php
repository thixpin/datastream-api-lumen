<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

use App\Enums\OrderStatus;
use App\Models\Driver;
use App\Models\Order;

class OrderOperateCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'order:operate';

    /**
     * The console command to operate orders.
     *
     * @var string
     */
    protected $description = "Assign orders to drivers and update their status";

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function __invoke()
    {
        $this->processPendingOrders();
        $this->processProcessingOrders();
    }

    /**
     * Assign orders to available drivers
     *
     */
    public function processPendingOrders()
    {
        $this->info('Start processing pending orders');
        // Get all orders that are pending
        $pendingOrders = Order::where('status', OrderStatus::CREATED->value)->get();
        $i = 0;

        foreach ($pendingOrders as $order) {
            // Get the driver with the least number of orders
            $driver = Driver::where('current_order_id', null);
            $skip = rand(0, $driver->count() - 1);
            $driver = $driver->skip($skip)->first();
            
            // If no driver is available, skip all the process
            if(!$driver) {
                $this->info('No drivers available');
                break;
            }


            try {

                // Assign the order to the driver
                $driver->current_order_id = object_id($order->_id);
                $driver->current_order = json_decode(json_encode($order));
                $driver->save();

                // Update the order status
                $order->status = OrderStatus::PROCESSING;
                $order->driver_id = object_id($driver->_id);
                $order->save();


                $i++;

            } catch (\Throwable $th) {


                Log::error($th->getMessage());

            }
            
        }
        $this->info("End processing pending orders. $i orders assigned to drivers");
    }

    /**
     * Update the status of orders that are being processed
     *
     */
    public function processProcessingOrders()
    {
        $this->info('Start processing processing orders');
        // Get all orders that are processing
        $processingOrders = Order::where('status', OrderStatus::PROCESSING->value)->get();

        $i = 0;
        foreach ($processingOrders as $order) {

            
            $todtalSteps = $order->steps;
            $setupInSeconds = $todtalSteps / $order->duration;
            $order->completed_steps += $setupInSeconds;

            // Get the driver who is processing this order
            $driver = Driver::where('current_order_id', object_id($order->_id))->first();

            try {

                // If the order is completed, update the status of the order and the driver
                if($order->completed_steps >= $todtalSteps) {
                    $order->completed_steps = $todtalSteps;
                    $order->status = OrderStatus::COMPLETED;
                    unset($driver->current_order_id);
                    unset($driver->current_order);
                } 
                // If the order is not completed, update the order status of the driver
                else {
                    $driver->current_order = json_decode(json_encode($order));
                }

                $driver->save();
                $order->save();
                $i++;
                
            } catch (\Throwable $th) {
                $this->info($th->getMessage());
                Log::error($th->getMessage());
            }
            
        }
        $this->info("End processing processing orders. $i orders updated");
    }

}