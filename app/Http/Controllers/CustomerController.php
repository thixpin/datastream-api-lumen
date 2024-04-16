<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\JsonResponse;

class CustomerController extends Controller
{

    /*
    * Show all customers
    * @return JsonResponse
    */
    public function index(): JsonResponse 
    {
        $customers = Customer::all();
        return response()->json($customers);
    }

    /*
    * Show one customer
    * @param String $id
    * @return JsonResponse
    */
    public function show(String $id): JsonResponse
    {
        $customer = Customer::find($id);
        return response()->json($customer);
    }
    
}
