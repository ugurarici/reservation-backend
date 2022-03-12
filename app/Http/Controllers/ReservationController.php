<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservationRequest;
use App\Http\Requests\UpdateReservationRequest;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except('create', 'store');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $reservations = $request->user()
            ->accessibleReservations()
            ->orderBy('reservation_at', 'desc')
            ->paginate(100);

        return response()->json($reservations);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $startHour = 10;
        $date = now();
        if ($request->query('date')) {
            try {
                $date = new Carbon($request->query('date'));
            } catch (\Throwable $th) {
                $date = now();
            }
        }

        if ($date->isToday()) {
            $startHour = now()->addHour()->hour;
        }

        if ($startHour <= 17) {
            $allHours = collect(range($startHour, 17));
        } else {
            $allHours = collect([]);
        }

        $reservedHours = Reservation::query()
            ->where('reservation_at', '>=', $date->copy()->startOfDay())
            ->where('reservation_at', '<=', $date->copy()->endOfDay())
            ->get()
            ->map(function ($reservation) {
                return $reservation->reservation_at->hour;
            })
            ->flatten();

        return $allHours->diff($reservedHours)->flatten();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreReservationRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreReservationRequest $request)
    {
        $validatedData = $request->validated();

        if (Auth::check()) {
            $validatedData['user_id'] = Auth::id();
        }

        $reservation = Reservation::create($validatedData);

        return response()->json($reservation, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Reservation  $reservation
     * @return \Illuminate\Http\Response
     */
    public function show(Reservation $reservation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Reservation  $reservation
     * @return \Illuminate\Http\Response
     */
    public function edit(Reservation $reservation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateReservationRequest  $request
     * @param  \App\Models\Reservation  $reservation
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateReservationRequest $request, Reservation $reservation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Reservation  $reservation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Reservation $reservation)
    {
        if ($reservation->user && $reservation->user->id === Auth::id()) {
            $reservation->delete();
            return response()->json("Reservation deleted");
        } else {
            return abort(403, "You can't delete this reservation");
        }
    }
}
