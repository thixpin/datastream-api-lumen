<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

use App\Models\Customer;
use App\Models\Driver;
use App\Models\Order;

class SeedController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return JsonResponse
     */
    public function seed(): JsonResponse
    {
        $drivers = Driver::factory()->count(5)->create();
        $customers = Customer::factory()->count(3)->create();
        return response()->json(['message' => 'Data seeded successfully', 'drivers' => $drivers, 'customers' => $customers], 201);
    }

    /**
     * Clear all data
     *
     * @return JsonResponse
     */
    public function clear(): JsonResponse
    {
        Driver::truncate();
        Customer::truncate();
        Order::truncate();
        return response()->json(['message' => 'Data truncated successfully'], 204);
    }
    
}
