<x-layout>
    <style>
        /* Rich Text Content Styling (from Rich Editor) */
        .room-rich-description {
            text-align: left;
            color: #3d2100;
            font-size: 0.95rem;
            line-height: 1.65;
        }
        .room-rich-description > *:first-child {
            margin-top: 0 !important;
            padding-top: 0 !important;
        }
        .room-rich-description > *:last-child {
            margin-bottom: 0 !important;
            padding-bottom: 0 !important;
        }
        .room-rich-description p {
            margin-top: 0.25rem;
            margin-bottom: 0.5rem;
            text-align: left;
        }
        .room-rich-description strong {
            font-weight: 700;
            color: #261500;
        }
        .room-rich-description em {
            font-style: italic;
        }
        .room-rich-description u {
            text-decoration: underline;
        }
        .room-rich-description s {
            text-decoration: line-through;
        }
        .room-rich-description h1 {
            font-size: 1.5rem;
            font-weight: 800;
            color: #261500;
            margin-top: 0.75rem;
            margin-bottom: 0.35rem;
            text-align: left;
        }
        .room-rich-description h2 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #261500;
            margin-top: 0.5rem;
            margin-bottom: 0.25rem;
            text-align: left;
        }
        .room-rich-description h3 {
            font-size: 1.1rem;
            font-weight: 700;
            color: #261500;
            margin-top: 0.5rem;
            margin-bottom: 0.25rem;
            text-align: left;
        }
        .room-rich-description ul {
            list-style-type: disc;
            padding-left: 1.25rem;
            margin-top: 0.25rem;
            margin-bottom: 0.5rem;
            text-align: left;
        }
        .room-rich-description ol {
            list-style-type: decimal;
            padding-left: 1.25rem;
            margin-top: 0.25rem;
            margin-bottom: 0.5rem;
            text-align: left;
        }
        .room-rich-description li {
            margin-bottom: 0.25rem;
            text-align: left;
        }
        .room-rich-description blockquote {
            border-left: 3px solid #b98762;
            padding-left: 0.75rem;
            font-style: italic;
            color: #734837;
            margin-top: 0.5rem;
            margin-bottom: 0.5rem;
            text-align: left;
        }
    </style>

    <div class="min-h-screen flex flex-col md:flex-row">
        <!-- Vertical Left Navbar: menus 'My Bookings' and 'Meeting Rooms' -->
        <x-navbar active="meeting-rooms" />

        <!-- Main Content Area: Room Details, Calendar, and Booking Form -->
        <main class="flex-1 p-6 sm:p-8 md:p-10 lg:p-12 overflow-x-hidden">
            <div class="max-w-6xl space-y-8">

                <!-- Back Navigation Link -->
                <div>
                    <a href="{{ route('staff.meeting-rooms.index') }}"
                       class="inline-flex items-center gap-2 text-sm font-bold text-primary-700 hover:text-primary-950 transition-colors">
                        <x-heroicon-o-arrow-left class="w-4 h-4 stroke-[2.5]" />
                        <span>Back to Meeting Rooms</span>
                    </a>
                </div>

                <!-- Flash Notifications -->
                <x-flash-success class="mb-6" />
                <x-flash-error class="mb-6" title="Reservation could not be processed:" />

                <!-- Room Details Card (Title, Location, Capacity, and Rich Description) -->
                <div class="bg-white rounded-3xl border border-primary-200/80 shadow-2xs p-6 sm:p-8">
                    <!-- Title & Badges -->
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-5">
                        <div class="space-y-2">
                            <div class="flex items-center gap-2.5">
                                <h1 class="text-2xl sm:text-3xl font-black text-primary-900 tracking-tight">
                                    {{ $meetingRoom->name }}
                                </h1>
                            </div>
                            
                            <!-- Location and Capacity -->
                            <div class="flex flex-wrap items-center gap-3 text-xs sm:text-sm text-primary-600">
                                <div class="flex items-center gap-1.5 font-medium">
                                    <x-heroicon-o-map-pin class="w-4 h-4 text-primary-400 shrink-0" />
                                    <span>{{ $meetingRoom->location }}</span>
                                </div>
                                <span class="text-primary-300">•</span>
                                <div class="flex items-center gap-1.5 font-semibold text-primary-800 bg-primary-100/80 border border-primary-200/60 px-2.5 py-1 rounded-lg">
                                    <x-heroicon-o-users class="w-4 h-4 text-secondary shrink-0" />
                                    <span>{{ $meetingRoom->capacity }} People Capacity</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description Section (Styling from Rich Text Editor) -->
                    <div class="pt-5 border-t border-primary-100 text-left">
                        <h2 class="text-xs font-bold text-primary-500 uppercase tracking-wider mb-2 text-left">
                            Description
                        </h2>
                        <div class="room-rich-description text-left">
                            {!! $meetingRoom->description !!}
                        </div>
                    </div>
                </div>

                <!-- Calendar View Section -->
                <div class="space-y-4">
                    <!-- Controls: Select Date -->
                    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                        <div class="w-full sm:w-64">
                            <label for="staffDatePicker" class="block text-xs font-bold text-primary-800 uppercase tracking-wider mb-1.5">
                                Select Date
                            </label>
                            <div class="relative">
                                <input type="date"
                                       id="staffDatePicker"
                                       value="{{ $selectedDate }}"
                                       class="w-full pl-3.5 pr-4 py-2.5 rounded-xl border border-primary-300/80 bg-white text-sm font-semibold text-primary-900 focus:outline-none focus:ring-2 focus:ring-secondary focus:border-secondary shadow-2xs cursor-pointer" />
                            </div>
                        </div>
                    </div>

                    <!-- Reusable Calendar Grid Component -->
                    <x-calendar-view
                        :days="$days"
                        :bookings="$bookings"
                        :selected-room-id="$meetingRoom->id"
                        :room-title="'Schedule for ' . $meetingRoom->name"
                    />
                </div>

                <!-- Booking Form Section (Below Calendar) -->
                <div class="">
                    <div class="flex items-center gap-2.5 mb-6 pb-4 border-b border-primary-200/60">
                        <div>
                            <h2 class="text-lg font-black text-primary-900 tracking-tight">Book {{ $meetingRoom->name }}</h2>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('staff.bookings.store') }}" id="staffBookRoomForm">
                        @csrf
                        <input type="hidden" name="meeting_room_id" value="{{ $meetingRoom->id }}" />

                        <div class="space-y-5">
                            <!-- Input: Title -->
                            <div>
                                <label for="title" class="block text-xs font-bold text-primary-800 uppercase tracking-wider mb-1.5">
                                    Title <span class="text-rose-500">*</span>
                                </label>
                                <input type="text"
                                       name="title"
                                       id="title"
                                       value="{{ old('title') }}"
                                       required
                                       placeholder="e.g., Q3 Strategy Planning, Design Critique"
                                       class="w-full px-4 py-2.5 rounded-xl border border-primary-300/80 bg-white text-sm font-semibold text-primary-900 focus:outline-none focus:ring-2 focus:ring-secondary focus:border-secondary shadow-2xs transition-all" />
                            </div>

                            <!-- Inputs: Date, Start Time, and End Time -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <!-- Date Input (defaults to currently selected calendar date) -->
                                <div>
                                    <label for="form_booking_date" class="block text-xs font-bold text-primary-800 uppercase tracking-wider mb-1.5">
                                        Date <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="date"
                                               name="date"
                                               id="form_booking_date"
                                               value="{{ old('date', $selectedDate) }}"
                                               required
                                               class="w-full px-4 py-2.5 rounded-xl border border-primary-300/80 bg-white text-sm font-semibold text-primary-900 focus:outline-none focus:ring-2 focus:ring-secondary focus:border-secondary shadow-2xs cursor-pointer" />
                                    </div>
                                </div>

                                <!-- Start Time Input -->
                                <div>
                                    <label for="form_start_time" class="block text-xs font-bold text-primary-800 uppercase tracking-wider mb-1.5">
                                        Start Time <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="time"
                                               name="start_time"
                                               id="form_start_time"
                                               value="{{ old('start_time', '09:00') }}"
                                               required
                                               class="w-full px-4 py-2.5 rounded-xl border border-primary-300/80 bg-white text-sm font-semibold text-primary-900 focus:outline-none focus:ring-2 focus:ring-secondary focus:border-secondary shadow-2xs cursor-pointer" />
                                    </div>
                                </div>

                                <!-- End Time Input -->
                                <div>
                                    <label for="form_end_time" class="block text-xs font-bold text-primary-800 uppercase tracking-wider mb-1.5">
                                        End Time <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="time"
                                               name="end_time"
                                               id="form_end_time"
                                               value="{{ old('end_time', '10:00') }}"
                                               required
                                               class="w-full px-4 py-2.5 rounded-xl border border-primary-300/80 bg-white text-sm font-semibold text-primary-900 focus:outline-none focus:ring-2 focus:ring-secondary focus:border-secondary shadow-2xs cursor-pointer" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bottom Right: Button Book Room -->
                        <div class="mt-8 pt-5 border-t border-primary-100 flex items-center justify-end">
                            <button type="submit"
                                    id="bookRoomSubmitBtn"
                                    class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-primary text-white text-sm font-bold rounded-xl shadow-md hover:bg-secondary hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5 cursor-pointer">
                                <x-heroicon-o-check class="w-4 h-4 stroke-[2.5]" />
                                <span>Book Room</span>
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </main>
    </div>

    <!-- Reusable Booking Details Modal for clicking on calendar slots -->
    <x-booking-details-modal id="staffDetailsModal" :show-creator="true">
        <x-slot:actions>
            <!-- Cancel Button for Staff if it's their own booking -->
            <form id="cancelStaffBookingForm" method="POST" action="" class="hidden">
                @csrf
                @method('DELETE')
                <button type="submit"
                        onclick="return confirm('Are you sure you want to cancel this booking reservation?');"
                        class="px-4 py-2 text-xs sm:text-sm font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-xl transition-all cursor-pointer">
                    Cancel Booking
                </button>
            </form>
        </x-slot:actions>
    </x-booking-details-modal>

    <!-- Interactive JavaScript for Date Selection and Calendar Details Modal -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Date Picker Navigation
            const staffDatePicker = document.getElementById('staffDatePicker');
            const formBookingDate = document.getElementById('form_booking_date');

            if (staffDatePicker) {
                staffDatePicker.addEventListener('change', function () {
                    const newDate = this.value;
                    if (newDate) {
                        const url = new URL(window.location);
                        url.searchParams.set('date', newDate);
                        window.location.href = url.toString();
                    }
                });
            }

            // Sync date in form if datePicker is present
            if (staffDatePicker && formBookingDate && !formBookingDate.value) {
                formBookingDate.value = staffDatePicker.value;
            }

            // 2. Calendar Booking Slot Details Modal
            const detailsModal = document.getElementById('staffDetailsModal');
            const closeBtn = document.getElementById('closeStaffDetailsModalBtn');
            const footerCloseBtn = document.getElementById('closeStaffDetailsModalFooterBtn');
            const cancelForm = document.getElementById('cancelStaffBookingForm');
            const currentUserId = {{ auth()->id() ?? 'null' }};

            function openDetailsModal(data) {
                if (!detailsModal) return;

                const titleEl = document.getElementById('staffDetailsTitle');
                const roomEl = document.getElementById('staffDetailsRoom');
                const locationEl = document.getElementById('staffDetailsLocation');
                const userEl = document.getElementById('staffDetailsUser');
                const emailEl = document.getElementById('staffDetailsUserEmail');
                const dateEl = document.getElementById('staffDetailsDate');
                const timeEl = document.getElementById('staffDetailsTime');

                if (titleEl) titleEl.textContent = data.title || '-';
                if (roomEl) roomEl.textContent = data.roomName || '-';
                if (locationEl) locationEl.textContent = data.roomLocation ? 'Location: ' + data.roomLocation : '';
                if (userEl) userEl.textContent = data.userName || '-';
                if (emailEl) emailEl.textContent = data.userEmail || '';
                if (dateEl) dateEl.textContent = data.dateFormatted || data.date || '-';
                if (timeEl) timeEl.textContent = (data.startTime || '-') + ' - ' + (data.endTime || '-');

                // If this booking belongs to current staff user, allow cancelling
                if (cancelForm && data.userId && currentUserId && String(data.userId) === String(currentUserId)) {
                    cancelForm.action = '/staff/my-bookings/' + data.id;
                    cancelForm.classList.remove('hidden');
                } else if (cancelForm) {
                    cancelForm.classList.add('hidden');
                }

                detailsModal.classList.remove('hidden');
            }

            function closeDetailsModal() {
                if (detailsModal) detailsModal.classList.add('hidden');
            }

            if (closeBtn) closeBtn.addEventListener('click', closeDetailsModal);
            if (footerCloseBtn) footerCloseBtn.addEventListener('click', closeDetailsModal);

            // Clicking any booking block on the calendar opens details modal
            document.querySelectorAll('.booking-block').forEach(function (block) {
                block.addEventListener('click', function (e) {
                    e.stopPropagation();
                    const data = {
                        id: this.dataset.bookingId,
                        title: this.dataset.title,
                        date: this.dataset.date,
                        dateFormatted: this.dataset.dateFormatted,
                        startTime: this.dataset.startTime,
                        endTime: this.dataset.endTime,
                        roomName: this.dataset.roomName,
                        roomLocation: this.dataset.roomLocation,
                        roomId: this.dataset.roomId,
                        userName: this.dataset.userName,
                        userEmail: this.dataset.userEmail,
                        userId: this.dataset.userId
                    };
                    openDetailsModal(data);
                });
            });

            // Close on background click or Escape
            window.addEventListener('click', function (e) {
                if (e.target === detailsModal) closeDetailsModal();
            });

            window.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') closeDetailsModal();
            });
        });
    </script>
</x-layout>
