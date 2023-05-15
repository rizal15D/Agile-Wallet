<?php

namespace App\Http\Controllers\API;

use App\Models\ForecastSimulation;
use App\Http\Controllers\API\APIController;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Spatie\QueryBuilder\QueryBuilder;

class ForecastSimulationController extends APIController
{
    public function index(Request $request): JsonResponse
    {
        $simulation = ForecastSimulation::where('user_id', $request->user_id)->get();

        return $this->response->resource($simulation);
    }

    public function show(ForecastSimulation $simulation): JsonResponse
    {
        Gate::authorize('view', $simulation);

        return $this->response->resource($simulation);
    }

    public function detail ($id) {
        try {
            $simulation = ForecastSimulation::where('id', $id)->first();

            if (!$simulation) return response()->json([
                'message' => 'Data Simulation Not Found!'
            ], 404);

            return response()->json([
                'message' => 'Success Gets Simulation',
                'simulation' => $simulation
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $req): JsonResponse
    {
        $validatedRequestData = $req->validate([
            'user_id' => 'required|integer',
            'name' => 'required|string'
        ]);

        $simulation = ForecastSimulation::create($validatedRequestData);

        return $this->response->resource($simulation);
    }

    public function update (Request $req, $user_id) {
        $validator = Validator::make($req->all(), [
            'name' => 'required|string'
        ]);
        if ($validator->fails()) return response()->json([
            'message' => $validator->errors()
        ], 422);
        DB::beginTransaction();
        try {
            $simulation = ForecastSimulation::where('id', $user_id)->first();
            if (!$simulation) return response()->json([
                'message' => 'Data Simulation Not Found!'
            ], 404);
            $simulation->update([
                'name' => $req->name
            ]);
            DB::commit();
            return response()->json([
                'message' => 'Data Simulation has been Updated!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy ($id) {
        DB::beginTransaction();
        try {
            ForecastSimulation::where('id', $id)->delete();
            DB::commit();
            return response()->json(['message' => 'Data Simulation has been Deleted!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
