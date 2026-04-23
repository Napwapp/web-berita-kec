<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Agenda;

class AgendaController extends Controller
{
    public function index()
    {
        return view('agenda.index');
    }

    public function updateDate(Request $request)
    {
        $agenda = Agenda::find($request->id);

        if (!$agenda) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $agenda->update([
            'start_at' => $request->start_at,
            'end_at' => $request->end_at,
        ]);

        return response()->json(['success' => true]);
    }
}
