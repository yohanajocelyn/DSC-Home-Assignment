@if (!isset($isIncluded))
<x-layout>
    <div class="min-h-screen flex flex-col md:flex-row">
        <!-- Vertical Left Navbar: menus 'My Bookings' and 'Meeting Rooms' -->
        <x-navbar active="my-bookings" />

        <!-- Main Content Area: My Bookings -->
        <main class="flex-1 p-6 sm:p-8 md:p-10 lg:p-12 overflow-x-hidden">
@endif

<div class="max-w-6xl">
    <!-- Flash Success Notification -->
    @if (session('success'))
        <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-3">
                <x-heroicon-o-check-circle class="w-5 h-5 text-emerald-600 shrink-0" />
                <span class="font-semibold">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Validation Errors Alert if any -->
    @if (isset($errors) && $errors->any())
        <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm shadow-2xs">
            <div class="flex items-start gap-3">
                <x-heroicon-o-exclamation-circle class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" />
                <div>
                    <p class="font-bold mb-1">Action could not be processed:</p>
                    <ul class="list-disc list-inside space-y-0.5 text-xs">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Title: My Bookings -->
    <h1 class="text-2xl sm:text-3xl font-black text-primary-900 tracking-tight mb-6">
        My Bookings
    </h1>

    <!-- Search Bar Row (Right-Aligned) -->
    <div class="flex items-center justify-end mb-6">
        <!-- Search Input with Magnifying Glass -->
        <div class="relative w-full sm:w-72 md:w-80 flex items-center">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-primary-500 z-10">
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

                <!-- Right: Action Buttons (Details, Cancel) -->
                <div class="flex items-center justify-end gap-1.5 sm:gap-2 shrink-0">
                    <!-- Details Button -->
                    <button type="button"
                            onclick="event.stopPropagation(); window.openStaffDetails(this.closest('.staff-booking-row'))"
                            class="staff-details-btn inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-primary-900 bg-white hover:bg-primary-100 border border-primary-200 rounded-lg shadow-2xs hover:shadow-xs transition-all duration-200 cursor-pointer">
                        <x-heroicon-o-information-circle class="w-3.5 h-3.5 text-secondary" />
                        <span>Details</span>
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

<!-- MODAL: Reusable Booking Details Modal (Read-Only for Staff) -->
<x-booking-details-modal id="staffDetailsModal" :show-creator="false" />

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
    // Global modal functions
    window.openStaffDetails = function (row) {
        if (!row) return;
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

        // Cancel modal buttons wiring
        const cancelModal = document.getElementById('cancelStaffBookingModal');
        if (cancelModal) {
            cancelModal.querySelectorAll('.modal-close-trigger, .modal-cancel-trigger').forEach(trigger => {
                trigger.addEventListener('click', window.closeStaffCancelModal);
            });
        }

        // Backdrop & Keyboard Closing
        const detailsModal = document.getElementById('staffDetailsModal');
        window.addEventListener('click', function (e) {
            if (e.target === detailsModal) window.closeStaffDetails();
            if (e.target === cancelModal) window.closeStaffCancelModal();
        });

        window.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                window.closeStaffDetails();
                window.closeStaffCancelModal();
            }
        });
    });
</script>

@if (!isset($isIncluded))
        </main>
    </div>
</x-layout>
@endif
