<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Role;

class ClubController extends Controller
{
    public function showStep1()
    {
        return view('admin.clubs.step1');
    }

    public function processStep1(Request $request)
    {
        $validated = $request->validate([
            'guard_name' => ['required', 'string', 'min:3'],
        ]);

        Session::put('step1', ['role_name' => $validated['guard_name']]);

        return redirect()->route('admin.clubs.step2');
    }

    public function showStep2()
    {
        if (!Session::has('step1')) {
            return redirect()->route('admin.clubs.step1')->with('error', 'Please complete Step 1 first.');
        }

        $step1 = Session::get('step1'); 
        $users = User::all();
        return view('admin.clubs.step2', compact('users', 'step1'));
    }

    public function processStep2(Request $request)
    {
        $validated = $request->validate([
            'club_name' => ['required', 'string', 'min:3'],
            'manager_id' => ['required', 'exists:users,id'],
        ]);

        Session::put('step2', $validated);
        return redirect()->route('admin.clubs.step3');
    }

    public function showStep3()
    {
        if (!Session::has('step1') || !Session::has('step2')) {
            return redirect()->route('admin.clubs.step1');
        }

        $step1 = Session::get('step1');
        $step2 = Session::get('step2');
        $manager = User::find($step2['manager_id']);

        return view('admin.clubs.step3', compact('step1', 'step2', 'manager'));
    }

    public function submitAllSteps()
    {
        $step1 = Session::get('step1');
        $step2 = Session::get('step2');

        $club = Club::create([
            'name' => $step2['club_name'],
            'manager_id' => $step2['manager_id'],
            'guard_name' => $step1['role_name']
        ]);

        $manager = User::find($step2['manager_id']);
        if ($manager) {
            $roleName = $step1['role_name'];
            Role::findOrCreate($roleName);
            $manager->assignRole($roleName);
        }

        Session::forget(['step1', 'step2']);

        return redirect()->route('admin.clubs.index')->with('message', 'Club created successfully!');
    }

    public function index()
    {
        $clubs = Club::with('manager')->get();
        return view('admin.clubs.index', compact('clubs'));
    }

    public function destroy(Club $club, Role $role)
    {
        $club->delete();
        return back()->with('message', 'Club deleted successfully');
    }

    public function edit(Club $club)
    {
        $managers = User::all();
        return view('admin.clubs.edit', compact('club', 'managers'));
    }

    public function update(Request $request, Club $club)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3'],
            'manager_id' => ['required', 'exists:users,id'],
        ]);

        $oldManager = $club->manager;

        $club->update([
            'name'=> $validated['name'],
            'manager_id'=> $validated['manager_id'],
        ]);

        $roleName = $club->guard_name;

        if($oldManager && $oldManager->id !== $validated['manager_id']) {
            $oldManager->removeRole($roleName);
        }
    
        $newManager = User::findOrFail($validated['manager_id']);
        $newManager->assignRole($roleName);

        return redirect()->route('admin.clubs.index')->with('message', 'Club updated successfully!');
    }
}
