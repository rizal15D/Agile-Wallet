<?php

namespace App\Http\Controllers\API;

use App\Models\ForecastTransaction;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\APIController;
use App\Models\ForecastTransactionInterval;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Spatie\QueryBuilder\QueryBuilder;

class ForecastTransactionController extends APIController
{
    public function index(Request $request): JsonResponse
    {
        $forecastTransactions = ForecastTransaction::where('forecast_simulation_id', $request->id)->get();

            return $this->response->resource($forecastTransactions);
    }

    public function show(ForecastTransaction $transaction): JsonResponse
    {
        Gate::authorize('view', $transaction);

        return $this->response->resource($transaction);
    }

    public function detail ($id) {
        try {
            $forecastTransaction = ForecastTransaction::where('id', $id)->first();

            if (!$forecastTransaction) return response()->json([
                'message' => 'Data Simulation Not Found!'
            ], 404);

            return response()->json([
                'message' => 'Success Gets Simulation',
                'simulation' => $forecastTransaction
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store (Request $req) {
        $validator = Validator::make($req->all(), [
            'simulation_id' => 'required|integer',
            'amount' => 'required|integer',
            'name' => 'required|string',
            'type_id' => 'required|string',
            'interval_id' => 'required|string',
        ]);
        if ($validator->fails()) return response()->json([
            'message' => $validator->errors()
        ], 422);
        if($req->amount > 999999999999999){
            return response()->json([
                'message' => 'Data Transaction Amount Too Many',
            ]);
        }else{
            DB::beginTransaction();
            try {
                $forecastTransaction = ForecastTransaction::create([
                    'forecast_simulation_id' => $req->simulation_id,
                    'amount' => $req->amount,
                    'name' => $req->name,
                    'forecast_transaction_type_id' => $req->type_id,
                    'forecast_transaction_interval_id' => $req->interval_id,
                ]);
                DB::commit();
                return response()->json([
                    'message' => 'Data Transaction has been Created!',
                    'data' => $forecastTransaction
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'error' => $e->getMessage()
                ], 500);
            }
        }
    }

    public function update (Request $req, $user_id) {
        $validator = Validator::make($req->all(), [
            'amount' => 'required|integer',
            'name' => 'required|string',
            'interval_id' => 'required|string'
        ]);
        if ($validator->fails()) return response()->json([
            'message' => $validator->errors()
        ], 422);
        if($req->amount > 1000000000000000){
            return response()->json([
                'message' => 'Data Transaction Amount Too Many',
            ]);
        }else{
            DB::beginTransaction();
            try {
                $forecastTransaction = ForecastTransaction::where('id', $user_id)->first();
                if (!$forecastTransaction) return response()->json([
                    'message' => 'Data Simulation Not Found!'
                ], 404);
                $forecastTransaction->update([
                    'name' => $req->name,
                    'amount' => $req->amount,
                    'forecast_transaction_interval_id' => $req->interval_id
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
    }

    public function destroy ($id) {
        DB::beginTransaction();
        try {
            ForecastTransaction::where('id', $id)->delete();
            DB::commit();
            return response()->json(['message' => 'Data Transaction has been Deleted!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
