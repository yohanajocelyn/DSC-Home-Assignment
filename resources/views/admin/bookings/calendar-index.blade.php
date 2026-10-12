@if ($meetingRooms->isEmpty())
    <!-- Empty State -->
    <div class="py-16 px-4 text-center bg-white rounded-3xl border border-primary-200/80 shadow-2xs">
        <div class="w-16 h-16 mx-auto mb-3 rounded-2xl bg-primary-100/70 border border-primary-200/60 flex items-center justify-center text-secondary shadow-2xs">
            <x-heroicon-o-building-office-2 class="w-8 h-8" />
        </div>
        <h3 class="text-lg font-bold text-primary-900 mb-1">No Meeting Rooms Available</h3>
        <p class="text-sm text-primary-600 max-w-sm mx-auto mb-6 leading-relaxed">
            You must create at least one meeting room before booking reservations.
        </p>
        <a href="{{ route('admin.meeting-rooms.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary text-white text-sm font-bold rounded-xl shadow-md hover:bg-secondary hover:shadow-lg transition-all duration-200">
            <x-heroicon-o-plus class="w-4 h-4 stroke-[2.5]" />
            <span>Create a Meeting Room</span>
        </a>
    </div>
@else
    <!-- Controls Section: Room Select and Date Picker -->
    <div class="flex flex-col sm:flex-row sm:items-end gap-4 mb-6">
        <!-- Select: Meeting Room -->
        <div class="w-full sm:w-72">
            <label for="roomSelect" class="block text-xs font-bold text-primary-800 uppercase tracking-wider mb-1.5">
                Meeting Room
            </label>
            <div class="relative">
                <select id="roomSelect"
                        class="w-full pl-3.5 pr-10 py-2.5 rounded-xl border border-primary-300/80 bg-white text-sm font-semibold text-primary-900 focus:outline-none focus:ring-2 focus:ring-secondary focus:border-secondary shadow-2xs cursor-pointer appearance-none">
                    @foreach ($meetingRooms as $room)
                        @if ($room->is_active)
                            <option value="{{ $room->id }}"
                                    data-name="{{ $room->name }}"
                                    {{ (string)$selectedRoomId === (string)$room->id ? 'selected' : '' }}>
                                {{ $room->name }}
                            </option>
                        @endif
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-primary-500">
                    <x-heroicon-o-chevron-down class="w-4 h-4" />
                </div>
            </div>
        </div>

        <!-- Date Picker -->
        <div class="w-full sm:w-60">
            <label for="datePicker" class="block text-xs font-bold text-primary-800 uppercase tracking-wider mb-1.5">
                Date
            </label>
            <div class="relative">
                <input type="date"
                       id="datePicker"
                       value="{{ $selectedDate }}"
                       class="w-full pl-3.5 pr-4 py-2.5 rounded-xl border border-primary-300/80 bg-white text-sm font-semibold text-primary-900 focus:outline-none focus:ring-2 focus:ring-secondary focus:border-secondary shadow-2xs cursor-pointer" />
            </div>
        </div>
    </div>

    <!-- Button: Book a Room -->
    <div class="mb-6">
        <button type="button"
                id="openBookingModalBtn"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-primary text-white text-sm font-bold rounded-xl shadow-md hover:bg-secondary hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5 cursor-pointer">
            <span>Book a Room</span>
        </button>
    </div>

    <!-- Reusable Calendar Grid View -->
    <x-calendar-view
        :days="$days"
        :bookings="$bookings"
        :selected-room-id="$selectedRoomId"
        :room-title="'Schedule for ' . ($meetingRooms->firstWhere('id', $selectedRoomId)?->name ?? 'Room')"
    />
@endif
