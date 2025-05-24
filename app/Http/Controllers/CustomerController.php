<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
       $customers = Customer::all();
       return response()->json($customers);
    }
    public function show($id)
    {
        $customer = Customer::findOrFail($id);
        return response()->json($customer);
    }
    public function store(Request $request)
    {
        
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'phone' => 'required|string|max:20',
        ]);

        $customer = Customer::create($validatedData);
        return response()->json($customer, 201);
    }
    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255',
            'phone' => 'sometimes|required|string|max:20',
        ]);

        $customer->fill($validatedData);
        $customer->save();

        return response()->json($customer);
    }
    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return response()->json(['message' => 'Customer deleted successfully']);
    }
}
