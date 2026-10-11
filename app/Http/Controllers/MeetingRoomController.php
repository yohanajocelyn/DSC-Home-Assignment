<?php

namespace App\Http\Controllers;

use App\Models\MeetingRoom;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MeetingRoomController extends Controller
{
    /**
     * Display a listing of the meeting rooms.
     */
    public function index(): View
    {
        $meetingRooms = MeetingRoom::latest()->get();

        return view('admin.dashboard', compact('meetingRooms'));
    }

    /**
     * Show the form for creating a new meeting room.
     */
    public function create(): View
    {
        return view('admin.meeting-rooms.create');
    }

    /**
     * Store a newly created meeting room in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1'],
            'description' => ['required', 'string'],
            'is_active' => ['required', 'boolean'],
        ]);

        MeetingRoom::create($validated);

        return redirect()
            ->route('admin.meeting-rooms.index')
            ->with('success', 'Meeting room created successfully!');
    }
}
