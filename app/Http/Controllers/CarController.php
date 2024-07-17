<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

class CarController extends Controller
{
    public function index()
    {
        $cars = Car::where('available', true)->get();
        return response()->json($cars);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'brand' => 'sometimes|string|max:255',
            'year' => 'sometimes|integer|min:1900|max:' . (date('Y') + 1),
            'license_plate' => 'sometimes|string|max:20',
            'description' => 'sometimes|string',
            'available' => 'sometimes|boolean',
            'base_price_per_day' => 'sometimes|numeric|min:0',
        ]);

        $car = Car::findOrFail($id);

        $validatedData = $request->only([
            'name', 'brand', 'year', 'license_plate', 'description',
            'available', 'base_price_per_day'
        ]);

        $car->update($validatedData);

        return response()->json([
            'message' => 'Car updated successfully',
            'car' => $car
        ]);
    }

    public function destroy($id)
    {
        $car = Car::findOrFail($id);
        $car->delete();
        return response()->json(['message' => 'Car hire deleted successfully']);
    }

    public function hire(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'rental_days' => 'required|integer|min:1',
        ]);

        $car = Car::findOrFail($id);
        $user = User::findOrFail($request->input('user_id'));

        if (!$car->available) {
            return response()->json(['message' => 'Car is not available'], 400);
        }

        $hireCost = $this->calculateCost($car, $user, $request->input('rental_days'));

        $car->update(['available' => false]);

        return response()->json([
            'message' => 'Car hired successfully',
            'hire_cost' => $hireCost,
        ]);
    }

    private function calculateCost(Car $car, User $user, $rentalDays)
    {
        $cost = $car->base_price_per_day * $rentalDays;
        $userAge = Carbon::parse($user->dob)->age;

        $agePolicyYoungPercentage = Config::get('car.age_policy.young_percentage');
        $agePolicyYoungAge = Config::get('car.age_policy.young_age');
        $agePolicyOldPercentage = Config::get('car.age_policy.senior_percentage');
        $agePolicyOldAge = Config::get('car.age_policy.old_age');


        if ($userAge < $agePolicyYoungAge) {
            $cost *= $agePolicyYoungPercentage;
        } elseif ($userAge >= $agePolicyOldAge) {
            $cost *= $agePolicyOldPercentage;
        }

        return $cost;
    }
}

