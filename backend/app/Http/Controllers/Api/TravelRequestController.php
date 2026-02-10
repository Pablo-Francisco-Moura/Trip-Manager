<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\StoreTravelRequest;
use App\Http\Requests\UpdateTravelRequestStatus;
use App\Models\TravelRequest;
use App\Notifications\TravelRequestStatusChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TravelRequestController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request)
    {
        $user = $request->user();

        // Admins may see all travel requests; regular users only their own
        $query = TravelRequest::with('user');
        if (! ($user->is_admin ?? false)) {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('destination')) {
            $query->where('destination', 'like', '%'.$request->input('destination').'%');
        }

        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('departure_date', [$request->input('from'), $request->input('to')]);
        }

        return response()->json($query->paginate(15));
    }

    public function show(Request $request, TravelRequest $travelRequest)
    {
        $this->authorize('view', $travelRequest);

        return response()->json($travelRequest);
    }

    public function store(StoreTravelRequest $request)
    {
        $data = $request->validated();
        // associate with authenticated user and record requester name server-side
        $data['user_id'] = $request->user()->id;
        $data['requester_name'] = $request->user()->name;

        $travel = TravelRequest::create($data);

        return response()->json($travel, 201);
    }

    public function updateStatus(UpdateTravelRequestStatus $request, TravelRequest $travelRequest)
    {
        $this->authorize('updateStatus', $travelRequest);

        $newStatus = $request->input('status');

        if ($newStatus === 'canceled' && $travelRequest->status === 'approved') {
            return response()->json(['message' => 'Não é possível cancelar um pedido já aprovado.'], 422);
        }

        $travelRequest->status = $newStatus;
        $travelRequest->save();

        $travelRequest->user->notify(new TravelRequestStatusChanged($travelRequest));

        return response()->json($travelRequest);
    }
}
