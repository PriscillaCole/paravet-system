<?php

namespace App\Http\Controllers;

use App\Models\FarmAnimal;
use App\Models\Farmer;
use App\Models\Farm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class FarmAnimalController extends Controller
{
    public function index()
    {
        $farmAnimals = FarmAnimal::all();
        return response()->json($farmAnimals);
    }

    public function show($id)
    {
        $farmAnimal = FarmAnimal::find($id);
        if ($farmAnimal) {
            return response()->json($farmAnimal);
        } else {
            return response()->json(['message' => 'FarmAnimal not found'], 404);
        }
    }

    public function store(Request $request)
    {
        $rules = [
            
            'farm_id' => 'required|exists:farms,id',
            'type' => 'nullable|string|max:255',
            'tag_number' => 'nullable|string|max:255|unique:farm_animals,tag_number',
            'species' => 'nullable|string|max:255',
            'gender' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'color' => 'nullable|string|max:255',
            'current_location' => 'nullable|string|max:255',
            'weight' => 'nullable|numeric',
            'body_condition_score' => 'nullable|integer',
            'health_history' => 'nullable|string',
            'medications' => 'nullable|string',
            'vaccinations' => 'nullable|string',
            'dietary_requirements' => 'nullable|string',
            'parentage' => 'nullable|string',
            'behavioral_notes' => 'nullable|string',
            'handling_requirements' => 'nullable|string',
            'management_notes' => 'nullable|string',
            'feeding_schedule' => 'nullable|string',
        ];

        try {
            $validatedData = Validator::make($request->all(), $rules)->validate();
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        $farmAnimal = FarmAnimal::create($validatedData);

        return response()->json([
            'message' => 'FarmAnimal added successfully',
            'farmAnimal' => $farmAnimal
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'farm_id' => 'required|exists:farms,id',
            'type' => 'nullable|string|max:255',
            'tag_number' => 'nullable|string|max:255|unique:farm_animals,tag_number,' . $id,
            'species' => 'nullable|string|max:255',
            'gender' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'color' => 'nullable|string|max:255',
            'current_location' => 'nullable|string|max:255',
            'weight' => 'nullable|numeric',
            'body_condition_score' => 'nullable|integer',
            'health_history' => 'nullable|string',
            'medications' => 'nullable|string',
            'vaccinations' => 'nullable|string',
            'dietary_requirements' => 'nullable|string',
            'parentage' => 'nullable|string',
            'behavioral_notes' => 'nullable|string',
            'handling_requirements' => 'nullable|string',
            'management_notes' => 'nullable|string',
            'feeding_schedule' => 'nullable|string',
        ];

        try {
            $validatedData = Validator::make($request->all(), $rules)->validate();
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        $farmAnimal = FarmAnimal::find($id);
        if ($farmAnimal) {
            $farmAnimal->update($validatedData);
            return response()->json([
                'message' => 'FarmAnimal updated successfully',
                'farmAnimal' => $farmAnimal
            ], 200);
        } else {
            return response()->json(['message' => 'FarmAnimal not found'], 404);
        }
    }

    public function destroy($id)
    {
        $farmAnimal = FarmAnimal::find($id);
        if ($farmAnimal) {
            $farmAnimal->delete();
            return response()->json(['message' => 'FarmAnimal deleted successfully'], 200);
        } else {
            return response()->json(['message' => 'FarmAnimal not found'], 404);
        }
    }
    
    public function getFarmAnimalsByFarm($farmId)
    {
        $farms = FarmAnimal::where('farm_id', $farmId)->get();
        return response()->json($farms);
    }
}


