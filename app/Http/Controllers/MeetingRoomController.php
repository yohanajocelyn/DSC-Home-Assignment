<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\MeetingRoom;
use Carbon\Carbon;
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

    /**
     * Show the form for editing the specified meeting room.
     */
    public function edit(MeetingRoom $meetingRoom): View
    {
        return view('admin.meeting-rooms.edit', compact('meetingRoom'));
    }

    /**
     * Update the specified meeting room in storage.
     */
    public function update(Request $request, MeetingRoom $meetingRoom): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1'],
            'description' => ['required', 'string'],
            'is_active' => ['required', 'boolean'],
        ]);

        $meetingRoom->update($validated);

        return redirect()
            ->route('admin.meeting-rooms.index')
            ->with('success', 'Meeting room updated successfully!');
    }
    /**
     * Display a listing of active meeting rooms for staff.
     */
    public function staffIndex(Request $request): View
    {
        $meetingRooms = MeetingRoom::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('staff.meeting-rooms.index', compact('meetingRooms'));
    }

    /**
     * Display the specified meeting room details and calendar schedule for staff.
     */
    public function staffShow(Request $request, MeetingRoom $meetingRoom): View|RedirectResponse
    {
        if (! $meetingRoom->is_active) {
            return redirect()
                ->route('staff.meeting-rooms.index')
                ->withErrors(['room' => 'This meeting room is currently inactive.']);
        }

        $selectedDate = $request->query('date', now()->format('Y-m-d'));
        try {
            $startDate = Carbon::parse($selectedDate)->startOfDay();
        } catch (\Exception $e) {
            $startDate = now()->startOfDay();
            $selectedDate = $startDate->format('Y-m-d');
        }

        // Build 7 consecutive days starting from selected date
        $days = [];
        for ($i = 0; $i < 7; $i++) {
            $currentDay = $startDate->copy()->addDays($i);
            $days[] = [
                'date_string' => $currentDay->format('Y-m-d'),
                'day_name' => $currentDay->format('D'),
                'day_full' => $currentDay->format('l'),
                'date_formatted' => $currentDay->format('M d'),
                'day_number' => $currentDay->format('d'),
                'month_name' => $currentDay->format('M'),
                'is_today' => $currentDay->isToday(),
                'is_selected' => $i === 0,
            ];
        }

        // Fetch bookings for this room to display on the calendar
        $bookings = Booking::with(['user', 'meetingRoom'])
            ->where('meeting_room_id', $meetingRoom->id)
            ->get();

        return view('staff.meeting-rooms.show', compact('meetingRoom', 'selectedDate', 'days', 'bookings'));
    }
}
