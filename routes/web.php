<?php

use App\Http\Livewire\Projects;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('login');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    // Route::get('/', function () {
    //     return view('livewire.projects');
    // })->name('projects');
    Route::get('/', function() {
        return view('home');
    })->name('projects');
    Route::get('/project', function() {
        return redirect('/');
    });
    Route::get('/project-{project_id}', function() {
        $URLpieces = explode('-', Request::url());
        $selectedProject = $URLpieces[1];

        return view('project-view', ['selectedProject' => $selectedProject, 'selectedList' => null]);
    });
    Route::get('/project-{project_id}/list-{list_id}', function() {
        // Grabs the URL and uses it to detirmine what project and list are currently selected
        $URLpieces = explode('/', Request::url());
        $projectAndList = array_slice($URLpieces, -2, 2, true);
        foreach($projectAndList as $val) {
            $pieces = explode('-', $val);
    
            if ($pieces[0] == 'project') {
                $selectedProject = $pieces[1];
            } 
            
            if ($pieces[0] == 'list') {
                $selectedList = $pieces[1];
            }
        }
        return view('project-view', ['selectedProject' => $selectedProject, 'selectedList' => $selectedList]);
    })->name('project-view');
});

// Route: '/' - Home, login page if not auth, projects list if auth
// // View: 'login' & 'projects'
// Route: '/project/{project_name} - looking into a project
// Route: '/project/{project_name}/{list_name} - looking at a list