<?php

use App\Http\Controllers\cricket\MatchInformationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\YarnApiController;
use App\Http\Controllers\YarnController;
use App\Http\Controllers\cricket\TournamentController;
use App\Http\Controllers\cricket\TournamentTypeController;
use App\Http\Controllers\cricket\TeamController;
use App\Http\Controllers\cricket\PlayerController;
use App\Http\Controllers\cricket\TeamPlayerController;
use App\Models\MatchInformation;
use App\Models\Package;
use App\Models\Tournament;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Yarn Api Route 
Route::get('yarnIndex', [YarnApiController::class, 'yarnIndex'])->name('yarnIndex');
Route::post('yarnCreate', [YarnApiController::class, 'yarnCreate'])->name('yarnCreate');
Route::put('yarnUpdate/{id}', [YarnApiController::class,'yarnUpdate'])->name('yarnUpdate');
Route::delete("yarnDestroy/{id}", [YarnApiController::class, 'yarnDestroy']);

// not use
Route::post('testingjoins', [YarnApiController::class, 'testingjoins']);

// Yarn Category Api Route 
Route::get('yarnCategory', [YarnApiController::class, 'yarnCategory'])->name('yarnCategory');
Route::post('yarnCreateCategory', [YarnApiController::class, 'yarnCreateCategory'])->name('yarnCreateCategory');
Route::put('yarnUpdateCategory/{id}', [YarnApiController::class,'yarnUpdateCategory'])->name('yarnUpdateCategory');
Route::delete("yarnDestroyCategory/{id}", [YarnApiController::class, 'yarnDestroyCategory']);

// Fabric Category Api Route 
Route::get('fabricCategory', [YarnApiController::class, 'fabricCategory'])->name('fabricCategory');
Route::post('fabricCreateCategory', [YarnApiController::class, 'fabricCreateCategory'])->name('fabricCreateCategory');
Route::put('fabricUpdateCategory/{id}', [YarnApiController::class,'fabricUpdateCategory'])->name('fabricUpdateCategory');
Route::delete("fabricDestroyCategory/{id}", [YarnApiController::class, 'fabricDestroyCategory']);

// Login 
Route::post('login', [YarnApiController::class, 'login']);
Route::post('userlogin', [YarnApiController::class, 'userlogin']);
Route::post('userRegistration', [YarnApiController::class, 'userRegistration']);

// Search Apis
Route::get('yarmsrc', [YarnApiController::class, 'yarmsrc']);
Route::get('categorysrc', [YarnApiController::class, 'categorysrc']);

// Fabric Cost 

Route::post('AddFabricDetails', [YarnApiController::class, 'AddFabricDetails']);
// Route::post('AddFabricDetailsx', [YarnApiController::class, 'AddFabricDetailsx']);
Route::post('validationFabricDetails', [YarnApiController::class, 'validationFabricDetails']);
Route::delete('fabricCostDelete/{id}', [YarnApiController::class, 'fabricCostDelete']);



Route::get('getFabricCost', [YarnApiController::class, 'getFabricCost']);
Route::get('getFabricCostx', [YarnApiController::class, 'getFabricCostx']);


Route::get('creatWarp', [YarnApiController::class, 'creatWarp']);
Route::post('storeWarp', [YarnApiController::class, 'storeWarp']);
Route::post('updateWarp', [YarnApiController::class, 'updateWarp']);
Route::post('updateWeft', [YarnApiController::class, 'updateWeft']);
Route::get('getresult/{id}', [YarnApiController::class, 'getresult']);

// Package
Route::get('userlist', [YarnApiController::class, 'userlist']);
Route::get('userpackage', [YarnApiController::class, 'userpackage']);
Route::post('AddPackage', [YarnApiController::class, 'AddPackage']);
Route::get('packagelist', [YarnApiController::class, 'packagelist']);
Route::post('uploadPhoto', [YarnApiController::class, 'uploadPhoto']);
Route::post('deletePhoto', [YarnApiController::class, 'deletePhoto']);
Route::post('userUpadate', [YarnApiController::class, 'userUpadate']);
Route::post('updateNumber', [YarnApiController::class, 'updateNumber']);
Route::post('deleteUser', [YarnApiController::class, 'deleteUser']);        
Route::get('getDetails', [YarnApiController::class, 'getDetails']);
Route::delete('deleteUserData', [YarnApiController::class, 'deleteUserData']);

// Tournament Type 
Route::get('index_tournament_type', [TournamentTypeController::class, 'index_tournament_type'])->name('index_tournament_type');
Route::post('create_tournament_type', [TournamentTypeController::class, 'create_tournament_type'])->name('create_tournament_type');
Route::post('update_tournament_type/{id}', [TournamentTypeController::class, 'update_tournament_type'])->name('update_tournament_type');

// Tournament
Route::get('index_tournament', [TournamentController::class, 'index_tournament'])->name('index_tournament');
Route::get('index_all_tournament', [TournamentController::class, 'index_all_tournament'])->name('index_all_tournament');
Route::get('tournament_details/{id}', [TournamentController::class, 'tournament_details'])->name('tournament_details');
Route::post('create_tournament', [TournamentController::class, 'create_tournament'])->name('create_tournament');
Route::post('update_tournament/{id}', [TournamentController::class, 'update_tournament'])->name('update_tournament');
Route::get('tournamentsrc', [TournamentController::class, 'tournamentsrc'])->name('tournamentsrc');
Route::post('tournament_delete', [TournamentController::class, 'tournament_delete'])->name('tournament_delete');

// Team
Route::get('index_team/{id}', [TeamController::class, 'index_team'])->name('index_team');
Route::get('groupbyteam/{id}', [TeamController::class, 'groupbyteam'])->name('groupbyteam');
Route::post('create_team', [TeamController::class, 'create_team'])->name('create_team');
Route::post('update_team/{id}', [TeamController::class, 'update_team'])->name('update_team');
Route::post('team_delete', [TeamController::class, 'team_delete'])->name('team_delete');
Route::post('sequence', [TeamController::class, 'sequence'])->name('sequence');


// players
Route::post('index_players', [PlayerController::class, 'index_players'])->name('index_players');
Route::post('create_players', [PlayerController::class, 'create_players'])->name('create_players');
Route::post('update_players/{id}', [PlayerController::class, 'update_players'])->name('update_players');
Route::get('playersrc', [PlayerController::class, 'playersrc'])->name('playersrc');
Route::get('player', [PlayerController::class, 'player'])->name('player');
Route::get('playerdropdowndetails', [PlayerController::class, 'playerdropdowndetails'])->name('playerdropdowndetails');
Route::post('player_delete',[PlayerController::class, 'player_delete'])->name('player_delete');

// Player Check 
Route::post('user_check', [PlayerController::class, 'user_check'])->name('user_check');

// Team Player
Route::get('index_team_player/{id}', [TeamPlayerController::class, 'index_team_player'])->name('index_team_player');
Route::post('create_team_player', [TeamPlayerController::class, 'create_team_player'])->name('create_team_player');
Route::post('update_team_player/{id}', [TeamPlayerController::class, 'update_team_player'])->name('update_team_player');
Route::get('remove_team_player/{id}', [TeamPlayerController::class, 'remove_team_player'])->name('remove_team_player');
Route::post('team_wise_player',[TeamPlayerController::class,'team_wise_player'])->name('team_wise_player');
Route::post('add_player', [TeamPlayerController::class, 'add_player'])->name('add_player');
Route::get('playerRole', [TeamPlayerController::class, 'playerRole'])->name('playerRole');
Route::get('editRole/{id}', [TeamPlayerController::class, 'editRole'])->name('editRole');
Route::post('order_sequence', [TeamPlayerController::class, 'order_sequence'])->name('order_sequence');



// Match Information
Route::get('index_match_info/{id}', [MatchInformationController::class, 'index_match_info'])->name('index_match_info');
Route::post('create_match_info', [MatchInformationController::class, 'create_match_info'])->name('create_match_info');
Route::post('update_match_info/{id}', [MatchInformationController::class, 'update_match_info'])->name('update_match_info');
Route::get('match_status', [MatchInformationController::class, 'match_status'])->name('match_status');
Route::post('match_delete', [MatchInformationController::class, 'match_delete'])->name('match_delete');

Route::post('add_match_player', [MatchInformationController::class, 'add_match_player'])->name('add_match_player');
Route::post('add_match_over', [MatchInformationController::class, 'add_match_over'])->name('add_match_over');

Route::post('inning_toss', [MatchInformationController::class, 'inningToss'])->name('inning_toss');
Route::post('finish_first_inning', [MatchInformationController::class, 'finishFirstInning'])->name('finish_first_inning');
Route::post('finish_second_inning', [MatchInformationController::class, 'finishSecondInning'])->name('finish_second_inning');
// won 
Route::post('declare_result',[MatchInformationController::class, 'declareResult'])->name('declare_result');

Route::post('matchinfo/{id}', [MatchInformationController::class, 'matchinfo'])->name('matchinfo');
Route::post('breakreason', [MatchInformationController::class, 'breakreason'])->name('breakreason');
Route::post('match_status_up', [MatchInformationController::class, 'match_status_up'])->name('match_status_up');

// ADD & EDIT NEW BATSMAN 
Route::post('newbatsman',[MatchInformationController::class, 'newbatsman'])->name('newbatsman');
Route::post('edit_new_bats_man',[MatchInformationController::class, 'editnewbatsman'])->name('editnewbatsman');

// ADD & EDIT NEW BOWLER
Route::post('newbowler',[MatchInformationController::class, 'newbowler'])->name('newbowler');
Route::post('edit_new_bowler',[MatchInformationController::class, 'editnewbowler'])->name('editnewbowler');

Route::post('cricket_status_update',[MatchInformationController::class, 'cricket_status_update'])->name('cricket_status_update');
Route::post('scorecard/{id}', [MatchInformationController::class, 'scorecard'])->name('scorecard');
Route::post('ballbyball/{id}', [MatchInformationController::class, 'ballbyball'])->name('scorecard');

Route::post('batsmanList', [MatchInformationController::class, 'batsmanList'])->name('batsmanList');
Route::post('bowlerList', [MatchInformationController::class, 'bowlerList'])->name('bowlerList');


Route::get('group_list', [MatchInformationController::class, 'group_list']);

Route::post('matchResult', [MatchInformationController::class, 'matchResult']);


Route::post('undobutton',[MatchInformationController::class, 'undobutton']);

Route::post('strick_change',[MatchInformationController::class, 'strickchange']);
Route::post('both_team_player', [MatchInformationController::class, 'bothTeamPlayer']);
Route::post('declare_player_of_the_match', [MatchInformationController::class, 'declarePlayerOfTheMatch']);
