<!-- Action & Search / Filter Bar Row (Styled like meeting-rooms/index.blade.php) -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <!-- "Book a Room" Button on Left (Reuses shared create modal) -->
    <div class="shrink-0">
        <button type="button"
                id="openBookingModalBtnList"
                class="open-booking-modal-trigger inline-flex items-center justify-center px-5 py-2.5 bg-primary text-white text-sm font-bold rounded-xl shadow-md hover:bg-secondary hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5 cursor-pointer whitespace-nowrap">
            <span>Book a Room</span>
        </button>
    </div>

    <!-- Filters on Right: Search, Filter by Room, Filter by Creator -->
    <div class="flex flex-col sm:flex-row sm:items-center gap-2.5 sm:gap-3 w-full sm:w-auto">
        <!-- Search Input -->
        <div class="relative w-full sm:w-48 md:w-56">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-primary-500 z-10">
                <x-heroicon-o-magnifying-glass class="w-4 h-4 text-primary-500" />
            </span>
            <input type="text"
                   id="bookingSearchInput"
                   placeholder="Search title, room, staff..."
                   class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-primary-300/80 bg-white/70 backdrop-blur-xs text-primary-900 text-sm placeholder-primary-400 focus:outline-none focus:ring-2 focus:ring-secondary focus:bg-white shadow-2xs transition-all duration-200" />
        </div>

        <!-- Filter by Room -->
        <div class="relative w-full sm:w-36">
            <select id="bookingFilterRoom"
                    class="w-full pl-3 pr-7 py-2.5 rounded-xl border border-primary-300/80 bg-white/80 text-primary-900 text-xs sm:text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-secondary shadow-2xs cursor-pointer appearance-none transition-all duration-200">
                <option value="">All Rooms</option>
                @foreach ($meetingRooms as $room)
                    <option value="{{ $room->id }}">{{ $room->name }}</option>
                @endforeach
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-primary-500">
                <x-heroicon-o-chevron-down class="w-4 h-4" />
            </div>
        </div>

        <!-- Filter by Creator / User -->
        <div class="relative w-full sm:w-36">
            <select id="bookingFilterUser"
                    class="w-full pl-3 pr-7 py-2.5 rounded-xl border border-primary-300/80 bg-white/80 text-primary-900 text-xs sm:text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-secondary shadow-2xs cursor-pointer appearance-none transition-all duration-200">
                <option value="">All Creators</option>
                @foreach ($creators as $creator)
                    <option value="{{ $creator->id }}">{{ $creator->name }}</option>
                @endforeach
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-primary-500">
                <x-heroicon-o-chevron-down class="w-4 h-4" />
            </div>
        </div>

        <!-- Filter by Timing: Upcoming/Present vs Past -->
        <div class="relative w-full sm:w-44">
            <select id="bookingFilterTiming"
                    class="w-full pl-3 pr-7 py-2.5 rounded-xl border border-primary-300/80 bg-white/80 text-primary-900 text-xs sm:text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-secondary shadow-2xs cursor-pointer appearance-none transition-all duration-200">
                <option value="">All Bookings</option>
                <option value="upcoming">Upcoming</option>
                <option value="past">Past Bookings</option>
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-primary-500">
                <x-heroicon-o-chevron-down class="w-4 h-4" />
            </div>
        </div>
    </div>
</div>

<!-- List of Bookings (Matching meeting-rooms/index table style) -->
<div class="divide-y divide-primary-200/60 border-t border-b border-primary-200/60" id="bookingsTableList">
    @forelse ($allBookings as $booking)
        <div class="booking-list-row py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition-colors hover:bg-primary-100/50 rounded-xl px-3 sm:px-4 cursor-pointer"
             data-booking-id="{{ $booking->id }}"
             data-title="{{ $booking->title }}"
             data-room-id="{{ $booking->meeting_room_id }}"
             data-room-name="{{ $booking->meetingRoom?->name ?? 'Meeting Room' }}"
             data-user-id="{{ $booking->user_id }}"
             data-user-name="{{ $booking->user?->name ?? 'Staff' }}"
             data-user-email="{{ $booking->user?->email ?? '' }}"
             data-date="{{ is_string($booking->date) ? $booking->date : $booking->date->format('Y-m-d') }}"
             data-date-formatted="{{ is_string($booking->date) ? \Carbon\Carbon::parse($booking->date)->format('D, M d, Y') : $booking->date->format('D, M d, Y') }}"
             data-start-time="{{ substr($booking->start_time, 0, 5) }}"
             data-end-time="{{ substr($booking->end_time, 0, 5) }}"
             data-search="{{ strtolower($booking->title . ' ' . ($booking->meetingRoom?->name ?? '') . ' ' . ($booking->user?->name ?? '')) }}">
            
            <!-- Left: Dot + Title & Booker (sm:w-1/3 matching meeting-rooms) -->
            <div class="flex items-center gap-3 min-w-0 sm:w-1/3">
                <div class="min-w-0">
                    <span class="text-base font-bold text-primary-900 truncate block" title="{{ $booking->title }}">
                        {{ $booking->title }}
                    </span>
                    <div class="flex items-center gap-1.5 text-xs text-primary-600 font-medium mt-0.5">
                        <x-heroicon-o-user class="w-3.5 h-3.5 text-primary-400 shrink-0" />
                        <span class="truncate">By {{ $booking->user?->name ?? 'Staff' }}</span>
                    </div>
                </div>
            </div>

            <!-- Middle: Room & Date/Time Badge (sm:w-1/2 matching meeting-rooms) -->
            <div class="flex items-center justify-between sm:justify-start gap-4 sm:w-1/2 min-w-0">
                <!-- Room -->
                <div class="flex items-center gap-1.5 text-xs sm:text-sm text-primary-600 truncate min-w-0 flex-1"
                     title="{{ $booking->meetingRoom?->name }}">
                    <x-heroicon-o-building-office-2 class="w-4 h-4 shrink-0 text-primary-400" />
                    <span class="truncate font-medium text-primary-800">{{ $booking->meetingRoom?->name ?? 'Meeting Room' }}</span>
                </div>

                <!-- Date & Time Interval Badge (Grouped badge matching People badge in meeting rooms) -->
                <div class="flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-primary-800 bg-primary-100/80 border border-primary-200/60 px-2.5 py-1 rounded-lg shrink-0">
                    <x-heroicon-o-clock class="w-4 h-4 text-secondary shrink-0" />
                    <span>{{ is_string($booking->date) ? \Carbon\Carbon::parse($booking->date)->format('M d') : $booking->date->format('M d') }}, {{ substr($booking->start_time, 0, 5) }}-{{ substr($booking->end_time, 0, 5) }}</span>
                </div>
            </div>

            <!-- Right: Action Buttons (Details, Edit, Cancel) -->
            <div class="flex items-center justify-end gap-1.5 sm:gap-2 shrink-0">
                <!-- Details Button (Opens shared details modal) -->
                <button type="button"
                        class="row-details-btn inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-primary-900 bg-white hover:bg-primary-100 border border-primary-200 rounded-lg shadow-2xs hover:shadow-xs transition-all duration-200 cursor-pointer">
                    <x-heroicon-o-information-circle class="w-3.5 h-3.5 text-secondary" />
                    <span>Details</span>
                </button>

                <!-- Edit Button (Opens shared edit modal) -->
                <button type="button"
                        class="row-edit-btn inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-primary-900 bg-white hover:bg-primary-100 border border-primary-200 rounded-lg shadow-2xs hover:shadow-xs transition-all duration-200 cursor-pointer">
                    <x-heroicon-o-pencil-square class="w-3.5 h-3.5 text-secondary" />
                    <span>Edit</span>
                </button>

                <!-- Cancel Button (Opens generic confirmation modal) -->
                <button type="button"
                        class="open-cancel-modal-btn inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-rose-700 bg-white hover:bg-rose-50 border border-rose-200 rounded-lg shadow-2xs hover:shadow-xs transition-all duration-200 cursor-pointer"
                        title="Cancel this booking"
                        data-booking-id="{{ $booking->id }}"
                        data-booking-title="{{ $booking->title }}"
                        data-action="{{ route('admin.bookings.destroy', $booking->id) }}">
                    <x-heroicon-o-x-circle class="w-3.5 h-3.5 text-rose-600" />
                    <span>Cancel</span>
                </button>
            </div>
        </div>
    @empty
        <!-- Placeholder for when there are no bookings registered yet -->
        <div class="py-16 px-4 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-primary-100/70 border border-primary-200/60 flex items-center justify-center text-primary-400 shadow-2xs">
                <x-heroicon-o-calendar-days class="w-8 h-8 text-secondary" />
            </div>
            <h3 class="text-lg font-bold text-primary-900 mb-1">No bookings yet</h3>
            <p class="text-sm text-primary-600 max-w-sm mx-auto mb-6 leading-relaxed">
                There are currently no bookings registered. Click below to book a room.
            </p>
            <button type="button"
                    class="open-booking-modal-trigger inline-flex items-center gap-2 px-5 py-2.5 bg-primary text-white text-sm font-bold rounded-xl shadow-md hover:bg-secondary hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5 cursor-pointer whitespace-nowrap">
                <span>Book a Room</span>
            </button>
        </div>
    @endforelse
</div>

<!-- No Filter Results Indicator -->
<div id="noBookingsFilterResults" class="hidden py-16 px-4 text-center">
    <div class="w-12 h-12 mx-auto mb-3 rounded-2xl bg-primary-100/70 border border-primary-200/60 flex items-center justify-center text-primary-400 shadow-2xs">
        <x-heroicon-o-magnifying-glass class="w-6 h-6 text-secondary" />
    </div>
    <p class="font-bold text-base text-primary-900 mb-1">No matching bookings</p>
    <p class="text-sm text-primary-600">No reservations match your search query, room, or creator filter.</p>
</div>
