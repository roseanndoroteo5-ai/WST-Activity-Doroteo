<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index()
    {
        return response()->json(
            Address::with('user')->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'street_address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zip' => 'required|string|max:10',
        ]);

        $address = Address::create($validated);

        return response()->json(
            $address->load('user'),
            201
        );
    }

    public function show(Address $address)
    {
        return response()->json(
            $address->load('user')
        );
    }

    public function update(Request $request, Address $address)
    {
        $validated = $request->validate([
            'user_id' => 'sometimes|required|exists:users,id',
            'street_address' => 'sometimes|required|string|max:255',
            'city' => 'sometimes|required|string|max:255',
            'state' => 'sometimes|required|string|max:255',
            'zip' => 'sometimes|required|string|max:10',
        ]);

        $address->update($validated);

        return response()->json(
            $address->load('user')
        );
    }

    public function destroy(Address $address)
    {
        $address->delete();

        return response()->json([
            'message' => 'Address deleted successfully'
        ]);
    }
}