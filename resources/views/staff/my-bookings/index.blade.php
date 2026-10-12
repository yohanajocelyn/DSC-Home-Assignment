@php
    $rooms = $meetingRooms ?? \App\Models\MeetingRoom::where('is_active', true)->orderBy('name')->get();
@endphp

@if (!isset($isIncluded))
<x-layout title="My Bookings - DSC Meeting Room Reservation">
    <div class="flex flex-col md:flex-row min-h-screen">
        <x-navbar />
        <main class="flex-1 p-6 sm:p-8 md:p-10 lg:p-12 overflow-x-hidden">
@endif

<div class="max-w-6xl">
    <!-- Flash Notifications -->
    <x-flash-success class="mb-6" />
    <x-flash-error class="mb-6" />

    <!-- Title: My Bookings -->
    <h1 class="text-2xl sm:text-3xl font-black text-primary-900 tracking-tight mb-6">
        My Bookings
    </h1>

    <!-- Search Bar aligned full-width on mobile, neat on tablet/desktop -->
    <div class="mb-6 max-w-md">
        <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <x-heroicon-o-magnifying-glass class="w-4 h-4 text-primary-500" />
            </span>
            <input type="text"
                   id="staffBookingSearch"
                   value="{{ request('search', '') }}"
                   placeholder="Search by title or room name..."
                   class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-primary-300/80 bg-white/70 backdrop-blur-xs text-primary-900 text-sm placeholder:text-primary-400 focus:outline-none focus:ring-2 focus:ring-secondary focus:bg-white shadow-2xs transition-all duration-200">
        </div>
    </div>

    <!-- List of Bookings (Matching meeting-rooms/index style, sorted ascending by date and time) -->
    <div class="divide-y divide-primary-200/60 border-t border-b border-primary-200/60" id="staffBookingsList">
        @forelse ($bookings as $booking)
            <div class="staff-booking-row py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition-colors hover:bg-primary-100/50 rounded-xl px-3 sm:px-4 cursor-pointer"
                 onclick="window.openStaffDetails(this)"
                 data-booking-id="{{ $booking->id }}"
                 data-room-id="{{ $booking->meeting_room_id }}"
                 data-title="{{ $booking->title }}"
                 data-room-name="{{ $booking->meetingRoom?->name ?? 'Meeting Room' }}"
                 data-room-location="{{ $booking->meetingRoom?->location ?? 'N/A' }}"
                 data-room-capacity="{{ $booking->meetingRoom?->capacity ?? 'N/A' }}"
                 data-date="{{ is_string($booking->date) ? $booking->date : $booking->date->format('Y-m-d') }}"
                 data-date-formatted="{{ is_string($booking->date) ? \Carbon\Carbon::parse($booking->date)->format('D, M d, Y') : $booking->date->format('D, M d, Y') }}"
                 data-start-time="{{ substr($booking->start_time, 0, 5) }}"
                 data-end-time="{{ substr($booking->end_time, 0, 5) }}"
                 data-search="{{ strtolower($booking->title . ' ' . ($booking->meetingRoom?->name ?? '') . ' ' . ($booking->meetingRoom?->location ?? '')) }}">

                <!-- Left: Dot Indicator + Title & Location -->
                <div class="flex items-center gap-3 min-w-0 sm:w-1/3">
                    <span class="w-2.5 h-2.5 rounded-full bg-secondary shrink-0 shadow-2xs" title="Active Booking"></span>
                    <div class="min-w-0">
                        <span class="text-base font-bold text-primary-900 truncate block" title="{{ $booking->title }}">
                            {{ $booking->title }}
                        </span>
                        <div class="flex items-center gap-1.5 text-xs text-primary-600 font-medium mt-0.5">
                            <x-heroicon-o-building-office-2 class="w-3.5 h-3.5 text-primary-400 shrink-0" />
                            <span class="truncate">{{ $booking->meetingRoom?->name ?? 'Meeting Room' }}</span>
                            @if ($booking->meetingRoom?->location)
                                <span class="text-primary-400">•</span>
                                <span class="text-primary-500 truncate">{{ $booking->meetingRoom->location }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Middle: Date & Time Interval Badge -->
                <div class="flex items-center justify-between sm:justify-start gap-3 sm:w-1/2 min-w-0">
                    <div class="flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-primary-800 bg-primary-100/80 border border-primary-200/60 px-3 py-1.5 rounded-lg shrink-0">
                        <x-heroicon-o-clock class="w-4 h-4 text-secondary shrink-0" />
                        <span>{{ is_string($booking->date) ? \Carbon\Carbon::parse($booking->date)->format('D, M d') : $booking->date->format('D, M d') }}, {{ substr($booking->start_time, 0, 5) }}-{{ substr($booking->end_time, 0, 5) }}</span>
                    </div>
                </div>

                <!-- Right: Action Buttons (Details, Edit, Cancel) -->
                <div class="flex items-center justify-end gap-1.5 sm:gap-2 shrink-0">
                    <!-- Details Button -->
                    <button type="button"
                            onclick="event.stopPropagation(); window.openStaffDetails(this.closest('.staff-booking-row'))"
                            class="staff-details-btn inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-primary-900 bg-white hover:bg-primary-100 border border-primary-200 rounded-lg shadow-2xs hover:shadow-xs transition-all duration-200 cursor-pointer">
                        <x-heroicon-o-information-circle class="w-3.5 h-3.5 text-secondary" />
                        <span>Details</span>
                    </button>

                    <!-- Edit Button -->
                    <button type="button"
                            onclick="event.stopPropagation(); window.openStaffEditModal(this.closest('.staff-booking-row'))"
                            class="staff-edit-btn inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-primary-900 bg-white hover:bg-primary-100 border border-primary-200 rounded-lg shadow-2xs hover:shadow-xs transition-all duration-200 cursor-pointer">
                        <x-heroicon-o-pencil-square class="w-3.5 h-3.5 text-secondary" />
                        <span>Edit</span>
                    </button>

                    <!-- Cancel Button -->
                    <button type="button"
                            onclick="event.stopPropagation(); window.openStaffCancelModal(this.dataset.bookingId, this.dataset.bookingTitle, this.dataset.action)"
                            class="staff-cancel-btn inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-rose-700 bg-white hover:bg-rose-50 border border-rose-200 rounded-lg shadow-2xs hover:shadow-xs transition-all duration-200 cursor-pointer"
                            title="Cancel this booking"
                            data-booking-id="{{ $booking->id }}"
                            data-booking-title="{{ $booking->title }}"
                            data-action="{{ route('staff.my-bookings.destroy', $booking->id) }}">
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
                <p class="text-sm text-primary-600 max-w-sm mx-auto leading-relaxed">
                    You have not made any room reservations yet.
                </p>
            </div>
        @endforelse
    </div>

    <!-- No Filter Results Indicator -->
    <div id="noStaffBookingsFilterResults" class="hidden py-16 px-4 text-center">
        <div class="w-12 h-12 mx-auto mb-3 rounded-2xl bg-primary-100/70 border border-primary-200/60 flex items-center justify-center text-primary-400 shadow-2xs">
            <x-heroicon-o-magnifying-glass class="w-6 h-6 text-secondary" />
        </div>
        <p class="font-bold text-base text-primary-900 mb-1">No matching bookings</p>
        <p class="text-sm text-primary-600">No reservations match your search query.</p>
    </div>
</div>

<!-- MODAL: Reusable Booking Details Modal (Read-Only for Staff with Edit Action Option) -->
<x-booking-details-modal id="staffDetailsModal" :show-creator="false">
    <x-slot:actions>
        <button type="button"
                id="switchToStaffEditModalBtn"
                onclick="window.switchFromDetailsToEdit()"
                class="inline-flex items-center gap-1.5 px-5 py-2 text-xs sm:text-sm font-bold bg-primary text-white rounded-xl shadow-md hover:bg-secondary hover:shadow-lg transition-all transform hover:-translate-y-0.5 cursor-pointer">
            <x-heroicon-o-pencil-square class="w-4 h-4" />
            <span>Edit</span>
        </button>
    </x-slot:actions>
</x-booking-details-modal>

<!-- MODAL: Edit Booking Modal for Staff -->
<div id="staffEditBookingModal" class="fixed inset-0 z-50 hidden bg-black/45 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl border border-primary-200 max-w-lg w-full overflow-hidden transform transition-all duration-200">
        <!-- Modal Header -->
        <div class="p-6 pb-4 border-b border-primary-200/60 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-secondary/15 flex items-center justify-center text-secondary">
                    <x-heroicon-o-pencil-square class="w-5 h-5" />
                </div>
                <div>
                    <h3 class="text-lg font-black text-primary-900 tracking-tight">Edit Booking</h3>
                    <p class="text-xs text-primary-600 font-medium">Update reservation details</p>
                </div>
            </div>
            <button type="button"
                    id="closeStaffEditModalBtn"
                    onclick="window.closeStaffEditModal()"
                    class="modal-close-trigger p-1.5 rounded-lg text-primary-400 hover:text-primary-800 hover:bg-primary-100/60 transition-colors cursor-pointer"
                    aria-label="Close modal">
                <x-heroicon-o-x-mark class="w-5 h-5" />
            </button>
        </div>

        <!-- Form: Booking Update -->
        <form method="POST" action="" id="staffEditBookingForm">
            @csrf
            @method('PUT')
            <div class="p-6 space-y-4">
                <!-- Meeting Room -->
                <div>
                    <x-input-label for="staff_edit_room_id">
                        <span>Meeting Room</span>
                        <span class="text-rose-500 ml-0.5">*</span>
                    </x-input-label>
                    <select name="meeting_room_id"
                            id="staff_edit_room_id"
                            required
                            class="w-full mt-1.5 px-3.5 py-2.5 rounded-xl border border-primary-300 bg-white text-primary-900 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-secondary cursor-pointer">
                        @foreach ($rooms as $room)
                            <option value="{{ $room->id }}">
                                {{ $room->name }} {{ $room->location ? '('.$room->location.')' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Title Field -->
                <div>
                    <x-input-label for="staff_edit_title">
                        <span>Title</span>
                        <span class="text-rose-500 ml-0.5">*</span>
                    </x-input-label>
                    <x-input id="staff_edit_title"
                             type="text"
                             name="title"
                             required
                             class="w-full mt-1.5" />
                </div>

                <!-- Date Field -->
                <div>
                    <x-input-label for="staff_edit_date">
                        <span>Date</span>
                        <span class="text-rose-500 ml-0.5">*</span>
                    </x-input-label>
                    <x-input id="staff_edit_date"
                             type="date"
                             name="date"
                             required
                             class="w-full mt-1.5 cursor-pointer" />
                </div>

                <!-- Start Time & End Time -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="staff_edit_start_time">
                            <span>Start Time</span>
                            <span class="text-rose-500 ml-0.5">*</span>
                        </x-input-label>
                        <x-input id="staff_edit_start_time"
                                 type="time"
                                 name="start_time"
                                 required
                                 class="w-full mt-1.5 cursor-pointer" />
                    </div>

                    <div>
                        <x-input-label for="staff_edit_end_time">
                            <span>End Time</span>
                            <span class="text-rose-500 ml-0.5">*</span>
                        </x-input-label>
                        <x-input id="staff_edit_end_time"
                                 type="time"
                                 name="end_time"
                                 required
                                 class="w-full mt-1.5 cursor-pointer" />
                    </div>
                </div>
            </div>

            <!-- Modal Footer: Cancel and Save changes button on bottom right -->
            <div class="p-4 sm:p-5 border-t border-primary-200/60 bg-primary-50/30 flex items-center justify-end gap-3">
                <button type="button"
                        id="cancelStaffEditModalBtn"
                        onclick="window.closeStaffEditModal()"
                        class="px-5 py-2.5 text-xs sm:text-sm font-semibold text-primary-700 bg-white hover:bg-primary-100/80 border border-primary-300 rounded-xl shadow-2xs transition-all cursor-pointer">
                    Cancel
                </button>
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-primary text-white text-xs sm:text-sm font-bold rounded-xl shadow-md hover:bg-secondary hover:shadow-lg transition-all transform hover:-translate-y-0.5 cursor-pointer">
                    <span>Save</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: Cancel Confirmation Modal (Reusing generic x-confirm-modal) -->
<x-confirm-modal
    id="cancelStaffBookingModal"
    title="Cancel Booking"
    message="Are you sure you want to cancel this booking? This will remove the reservation from the schedule."
    confirm-text="Yes, Cancel"
    cancel-text="Keep Booking"
    variant="danger"
    method="DELETE"
/>

<script>
    let currentStaffRow = null;

    // Global modal functions
    window.openStaffDetails = function (row) {
        if (!row) return;
        currentStaffRow = row;

        const titleEl = document.getElementById('staffDetailsTitle');
        const roomEl = document.getElementById('staffDetailsRoom');
        const locEl = document.getElementById('staffDetailsLocation');
        const capEl = document.getElementById('staffDetailsCapacity');
        const dateEl = document.getElementById('staffDetailsDate');
        const timeEl = document.getElementById('staffDetailsTime');

        if (titleEl) titleEl.textContent = row.getAttribute('data-title') || '-';
        if (roomEl) roomEl.textContent = row.getAttribute('data-room-name') || '-';

        const location = row.getAttribute('data-room-location');
        if (locEl) {
            if (location && location !== 'N/A') {
                locEl.textContent = location;
                locEl.classList.remove('hidden');
            } else {
                locEl.classList.add('hidden');
            }
        }

        const capacity = row.getAttribute('data-room-capacity');
        if (capEl) capEl.textContent = (capacity && capacity !== 'N/A') ? (capacity + ' people') : '-';
        if (dateEl) dateEl.textContent = row.getAttribute('data-date-formatted') || '-';
        if (timeEl) timeEl.textContent = (row.getAttribute('data-start-time') || '') + ' - ' + (row.getAttribute('data-end-time') || '');

        const modal = document.getElementById('staffDetailsModal');
        if (modal) modal.classList.remove('hidden');
    };

    window.closeStaffDetails = function () {
        const modal = document.getElementById('staffDetailsModal');
        if (modal) modal.classList.add('hidden');
    };

    window.openStaffEditModal = function (row) {
        if (!row) return;
        window.closeStaffDetails();
        currentStaffRow = row;

        const bookingId = row.getAttribute('data-booking-id');
        const roomId = row.getAttribute('data-room-id');
        const title = row.getAttribute('data-title');
        const date = row.getAttribute('data-date');
        const startTime = row.getAttribute('data-start-time');
        const endTime = row.getAttribute('data-end-time');

        const form = document.getElementById('staffEditBookingForm');
        const roomSelect = document.getElementById('staff_edit_room_id');
        const titleInput = document.getElementById('staff_edit_title');
        const dateInput = document.getElementById('staff_edit_date');
        const startInput = document.getElementById('staff_edit_start_time');
        const endInput = document.getElementById('staff_edit_end_time');

        if (form && bookingId) {
            form.action = '/staff/my-bookings/' + bookingId;
        }
        if (roomSelect && roomId) roomSelect.value = roomId;
        if (titleInput && title) titleInput.value = title;
        if (dateInput && date) dateInput.value = date;
        if (startInput && startTime) startInput.value = startTime;
        if (endInput && endTime) endInput.value = endTime;

        const editModal = document.getElementById('staffEditBookingModal');
        if (editModal) editModal.classList.remove('hidden');
    };

    window.closeStaffEditModal = function () {
        const editModal = document.getElementById('staffEditBookingModal');
        if (editModal) editModal.classList.add('hidden');
    };

    window.switchFromDetailsToEdit = function () {
        if (currentStaffRow) {
            window.openStaffEditModal(currentStaffRow);
        }
    };

    window.openStaffCancelModal = function (bookingId, bookingTitle, actionUrl) {
        const cancelForm = document.getElementById('cancelStaffBookingModal_form');
        const cancelMessage = document.getElementById('cancelStaffBookingModal_message');
        const cancelModal = document.getElementById('cancelStaffBookingModal');

        if (cancelForm && actionUrl) {
            cancelForm.action = actionUrl;
        }
        if (cancelMessage) {
            cancelMessage.textContent = bookingTitle
                ? ('Are you sure you want to cancel "' + bookingTitle + '"? This will remove the reservation from the schedule.')
                : 'Are you sure you want to cancel this booking? This will remove the reservation from the schedule.';
        }
        if (cancelModal) {
            cancelModal.classList.remove('hidden');
        }
    };

    window.closeStaffCancelModal = function () {
        const cancelModal = document.getElementById('cancelStaffBookingModal');
        if (cancelModal) {
            cancelModal.classList.add('hidden');
        }
    };

    document.addEventListener('DOMContentLoaded', function () {
        // 1. Instant Client-Side Search
        const searchInput = document.getElementById('staffBookingSearch');
        const bookingRows = document.querySelectorAll('.staff-booking-row');
        const noResults = document.getElementById('noStaffBookingsFilterResults');

        function filterRows() {
            if (!searchInput) return;
            const query = searchInput.value.toLowerCase().trim();
            let matchCount = 0;

            bookingRows.forEach(row => {
                const searchData = row.getAttribute('data-search') || '';
                if (!query || searchData.includes(query)) {
                    row.style.display = '';
                    matchCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            if (noResults) {
                noResults.classList.toggle('hidden', matchCount > 0 || bookingRows.length === 0);
            }
        }

        if (searchInput) {
            searchInput.addEventListener('input', filterRows);
            if (searchInput.value.trim() !== '') {
                filterRows();
            }
        }

        // Details modal buttons wiring
        const detailsModal = document.getElementById('staffDetailsModal');
        if (detailsModal) {
            detailsModal.querySelectorAll('.modal-close-trigger, .modal-cancel-trigger').forEach(trigger => {
                trigger.addEventListener('click', window.closeStaffDetails);
            });
        }

        // Cancel modal buttons wiring
        const cancelModal = document.getElementById('cancelStaffBookingModal');
        if (cancelModal) {
            cancelModal.querySelectorAll('.modal-close-trigger, .modal-cancel-trigger').forEach(trigger => {
                trigger.addEventListener('click', window.closeStaffCancelModal);
            });
        }

        // Edit modal buttons wiring
        const editModal = document.getElementById('staffEditBookingModal');
        if (editModal) {
            editModal.querySelectorAll('.modal-close-trigger, .modal-cancel-trigger').forEach(trigger => {
                trigger.addEventListener('click', window.closeStaffEditModal);
            });
        }

        // Backdrop & Keyboard Closing
        window.addEventListener('click', function (e) {
            if (e.target === detailsModal) window.closeStaffDetails();
            if (e.target === cancelModal) window.closeStaffCancelModal();
            if (e.target === editModal) window.closeStaffEditModal();
        });

        window.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                window.closeStaffDetails();
                window.closeStaffCancelModal();
                window.closeStaffEditModal();
            }
        });
    });
</script>

@if (!isset($isIncluded))
        </main>
    </div>
</x-layout>
@endif
