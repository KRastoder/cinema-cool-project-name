<?php
namespace App\Http\Controllers;

use App\Models\Hall;
use App\Models\Seat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class HallController extends Controller
{
    public function layoutBuilder()
    {
        return Inertia::render('admin/LayoutBuilder');
    }

    //TODO MAKE A REPO AND MOVE THE LOGIC WHEN YOU WANNA ADD THE CRUD TO ADMIN
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string',
            'rows'     => 'required|integer|min:1',
            'columns'  => 'required|integer|min:1',
            'row_gap'  => 'nullable|array',
            'col_gap'  => 'nullable|array',
            'vip_list' => 'nullable|array',
        ]);

        DB::transaction(function () use ($request) {
            $hall = Hall::create([
                'name'    => $request->name,
                'rows'    => $request->rows,
                'columns' => $request->columns,
                'row_gap' => $request->row_gap,
                'col_gap' => $request->col_gap,
            ]);

            $totalSeats    = $request->rows * $request->columns;
            $seatsToInsert = [];
            $vipList       = $request->vip_list ?? [];

            for ($i = 1; $i <= $totalSeats; $i++) {
                $seatsToInsert[] = [
                    'hall_id'     => $hall->id,
                    'seat_number' => $i,
                    'type'        => in_array($i, $vipList) ? 'vip' : 'normal',
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ];

                if (count($seatsToInsert) >= 200) {
                    Seat::insert($seatsToInsert);
                    $seatsToInsert = [];
                }
            }

            if (! empty($seatsToInsert)) {
                Seat::insert($seatsToInsert);
            }
        });

        return redirect()->back()->with('success', 'Hall and seats generated!');
    }

}
