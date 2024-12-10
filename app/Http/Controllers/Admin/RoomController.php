<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoomRequest;
use App\Models\Club;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoomController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Get the club associated with the logged-in manager
        $club = Club::whereHas('users', function ($query) use ($user) {
            $query->where('id', $user->id)
                ->whereHas('roles', function ($roleQuery) {
                    $roleQuery->where('name', 'manager');
                });
        })->first();

        // Retrieve rooms based on the associated club
        if ($club) {
            $rooms = $club->rooms;
        } else {
            $rooms = Room::all(); // For non-managers or unassigned clubs
        }

        // Check the status of each room based on active courses
        foreach ($rooms as $room) {
            $activeCourse = $room->courses()
                ->where('startTime', '<=', now())
                ->where('endTime', '>=', now())
                ->exists();
            
            $room->status = $activeCourse ? 'active' : 'inactive';
        }

        return view('admin.rooms.index', compact('rooms'));
    }

    public function create()
    {
        $user = Auth::user();

        // Get the club associated with the logged-in manager
        $club = Club::whereHas('users', function ($query) use ($user) {
            $query->where('id', $user->id)
                ->whereHas('roles', function ($roleQuery) {
                    $roleQuery->where('name', 'manager');
                });
        })->first();

        // If the user is a manager and has a club
        if ($club) {
            return view('admin.rooms.create', compact('club'));
        }

        // If the user is not a manager or no club is found, show all clubs
        $clubs = Club::all();
        return view('admin.rooms.create', compact('clubs'));
    }

    public function store(RoomRequest $request)
    {
        $validated = $request->validated();

        Room::create($validated);

        return redirect()->route('admin.rooms.index')->with('message', 'Room created successfully!');
    }

    public function edit(Room $room)
    {
        $user = Auth::user();

        // Get the club associated with the logged-in manager
        $club = Club::whereHas('users', function ($query) use ($user) {
            $query->where('id', $user->id)
                ->whereHas('roles', function ($roleQuery) {
                    $roleQuery->where('name', 'manager');
                });
        })->first();

        // If the user is a manager, check the room's club
        if ($club) {
            if ($room->club_id !== $club->id) {
                return redirect()->route('admin.rooms.index')->with('error', 'You can only edit rooms in your assigned club.');
            }

            return view('admin.rooms.edit', compact('room', 'club'));
        }

        // If the user is not a manager, allow selection of all clubs
        $clubs = Club::all();
        return view('admin.rooms.edit', compact('room', 'clubs'));
    }


    public function update(RoomRequest $request, Room $room)
    {
        $user = Auth::user();

        $validated = $request->validated();

        // Check if the user is a manager and validate the club assignment
        $club = Club::whereHas('users', function ($query) use ($user) {
            $query->where('id', $user->id)
                ->whereHas('roles', function ($roleQuery) {
                    $roleQuery->where('name', 'manager');
                });
        })->first();

        if ($club) {
            if ($validated['club_id'] != $club->id) {
                return redirect()->back()->with('error', 'You can only update rooms for your assigned club.');
            }
        }

        $room->update($validated);

        return redirect()->route('admin.rooms.index')->with('message', 'Room updated successfully!');
    }

    public function destroy(Room $room)
    {
        $room->delete();
        return back()->with('message', 'Room deleted successfully');
    }

}
