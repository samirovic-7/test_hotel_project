<?php

namespace App\Http\Controllers;

use App\Models\Maintenance;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MaintenanceController extends Controller
{

    public function index()
    {
        $maintenances = Maintenance::all();
        return response()->json(['data' => $maintenances]);
    }
    public function mainTenancePagination()
    {
        $maintenances = Maintenance::paginate(request()->segment(count(request()->segments())));
        return response()->json(['data' => $maintenances]);
    }
    public function maintenances_date_with_filter(Request $request)
    {

        $start = Carbon::parse($request->start_date);
        $end = Carbon::parse($request->end_date);

        $maintenances = Maintenance::when($request->has('main_status'), function (Builder $query) use ($request) {
            return $query->where('main_status', $request->input('main_status'));
        })->whereBetween('created_at', [$start, $end])
            ->get();

        

        return response()->json(['data' => $maintenances]);
    }
    public function get_room_maintenances($id)
    {
        $room_maintenances = Room::with('maintenances')->where('id', $id)->get();
        return response()->json(['data' => $room_maintenances]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'maintenance_type_id' => 'required|exists:maintenance_types,id',
            'description' => 'required',
            'main_status' => ['required', Rule::in(['pending', 'finished', 'closed'])],
            'exp_ready_date' => 'required|date',
        ]);
        $maintenance = new Maintenance;
        $maintenance->room_id = $validatedData['room_id'];
        $maintenance->maintenance_type_id = $validatedData['maintenance_type_id'];
        $maintenance->description = $validatedData['description'];
        $maintenance->main_status = $validatedData['main_status'];
        $maintenance->exp_ready_date = $validatedData['exp_ready_date'];
        $maintenance->save();

        return response()->json(['data' => $maintenance]);
    }

    public function show(Maintenance $maintenance)
    {
        return response()->json(['data' => $maintenance]);
    }

    public function update(Request $request, Maintenance $maintenance)
    {
        $validatedData = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'maintenance_type_id' => 'required|exists:maintenance_types,id',
            'description' => 'required',
            'main_status' => ['required', Rule::in(['pending', 'finished', 'closed'])],
            'exp_ready_date' => 'required|date',
        ]);
        if ($maintenance->main_status == 'pending') {
            $maintenance->room_id = $validatedData['room_id'];
            $maintenance->maintenance_type_id = $validatedData['maintenance_type_id'];
            $maintenance->description = $validatedData['description'];
            $maintenance->main_status = $validatedData['main_status'];
            $maintenance->exp_ready_date = $validatedData['exp_ready_date'];
            $maintenance->save();
        } else {
            return response()->json(['error' => 'Maintenance status is already ' . $maintenance->main_status]);
        }

        return response()->json(['data' => $maintenance]);
    }

    public function destroy(Maintenance $maintenance)
    {
        $maintenance->delete();

        return response()->json(['message' => 'Maintenance deleted']);
    }
}
