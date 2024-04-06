<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use Illuminate\Http\Request;

class TournamentController extends Controller
{
    public function create_tournament(Request $request)
    {
        $tournament_id = $request->tournament_delete;
        $tournament = Tournament::find($tournament_id);
        $tournament->is_delete = 0;
        $tournament->save();

        return json_encode([
            'success' => true,
            'message' => 'Tournament Deleted Successfully'
        ]);
    }
}
