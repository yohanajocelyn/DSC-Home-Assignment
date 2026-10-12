<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\MeetingRoom;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BookingController extends Controller
{
    /**
     * Display a listing of bookings and calendar view.
     */
    public function index(Request $request): View
    {
        $meetingRooms = MeetingRoom::orderBy('name')->get();

        $selectedRoomId = $request->query('room_id');
        if (! $selectedRoomId || ! $meetingRooms->contains('id', $selectedRoomId)) {
            $selectedRoomId = $meetingRooms->where('is_active', true)->first()?->id ?? $meetingRooms->first()?->id;
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

        // All active bookings for the list view
        $allBookings = Booking::with(['user', 'meetingRoom'])
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'asc')
            ->get();

        // Calendar bookings for the selected meeting room
        $bookings = $allBookings->where('meeting_room_id', (int) $selectedRoomId);

        // All users for creator filter
        $creators = \App\Models\User::orderBy('name')->get();

        // Current view: 'calendar' (default) or 'list'
        $currentView = $request->query('view', 'calendar');
        if (! in_array($currentView, ['calendar', 'list'])) {
            $currentView = 'calendar';
        }

        return view('admin.bookings.index', compact(
            'meetingRooms',
            'selectedRoomId',
            'selectedDate',
            'days',
            'bookings',
            'allBookings',
            'creators',
            'currentView'
        ));
    }

    /**
     * Store a newly created booking in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'meeting_room_id' => ['required', 'exists:meeting_rooms,id'],
            'title' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ]);

        $room = MeetingRoom::find($validated['meeting_room_id']);
        if (! $room || ! $room->is_active) {
            return back()
                ->withErrors(['meeting_room_id' => 'Cannot book an inactive or unavailable meeting room.'])
                ->withInput();
        }

        // Prevent overlapping bookings on the same room and date
        $overlap = Booking::where('meeting_room_id', $validated['meeting_room_id'])
            ->where('date', $validated['date'])
            ->where(function ($q) use ($validated) {
                $q->where('start_time', '<', $validated['end_time'])
                    ->where('end_time', '>', $validated['start_time']);
            })
            ->exists();

        if ($overlap) {
            return back()
                ->withErrors(['start_time' => 'The selected room is already booked during this time interval.'])
                ->withInput();
        }

        Booking::create([
            'user_id' => Auth::id() ?? 1,
            'meeting_room_id' => $validated['meeting_room_id'],
            'title' => $validated['title'],
            'date' => $validated['date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
        ]);

        return redirect()->route('admin.bookings.index', [
            'room_id' => $validated['meeting_room_id'],
            'date' => $validated['date'],
        ])->with('success', 'Room booked successfully!');
    }

    /**
     * Update the specified booking in storage.
     */
    public function update(Request $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'meeting_room_id' => ['required', 'exists:meeting_rooms,id'],
            'title' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ]);

        $overlap = Booking::where('meeting_room_id', $validated['meeting_room_id'])
            ->where('date', $validated['date'])
            ->where('id', '!=', $booking->id)
            ->where(function ($q) use ($validated) {
                $q->where('start_time', '<', $validated['end_time'])
                    ->where('end_time', '>', $validated['start_time']);
            })
            ->exists();

        if ($overlap) {
            return back()
                ->withErrors(['start_time' => 'The selected room is already booked during this time interval.'])
                ->withInput();
        }

        $booking->update([
            'meeting_room_id' => $validated['meeting_room_id'],
            'title' => $validated['title'],
            'date' => $validated['date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
        ]);

        return redirect()->route('admin.bookings.index', [
            'room_id' => $validated['meeting_room_id'],
            'date' => $validated['date'],
        ])->with('success', 'Booking updated successfully!');
    }

    /**
     * Remove the specified booking from storage.
     */
    public function destroy(Booking $booking): RedirectResponse
    {
        $booking->delete();

        return back()->with('success', 'Booking cancelled successfully!');
    }
}
