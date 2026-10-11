<x-layout>
    <div class="min-h-screen flex flex-col md:flex-row">
        <!-- Vertical Left Navbar (Same as Meeting Rooms) -->
        <x-navbar active="bookings" />

        <!-- Main Content Area: Matching padding and margin of Meeting Rooms view -->
        <main class="flex-1 p-6 sm:p-8 md:p-10 lg:p-12 overflow-x-hidden">
            <div class="max-w-7xl">

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
                                <p class="font-bold mb-1">Booking could not be processed:</p>
                                <ul class="list-disc list-inside space-y-0.5 text-xs">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Header -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                    <!-- Title: Bookings -->
                    <h1 class="text-2xl sm:text-3xl font-black text-primary-900 tracking-tight">
                        Bookings
                    </h1>

                    <!-- Switch between Calendar Index and List Index -->
                    <div class="inline-flex items-center p-1 bg-primary-100/70 border border-primary-200/80 rounded-2xl shadow-2xs shrink-0 self-start sm:self-auto" role="group" aria-label="View toggle">
                        <button type="button"
                                id="viewCalendarBtn"
                                class="view-toggle-btn inline-flex items-center gap-2 px-3.5 py-1.5 text-xs sm:text-sm font-bold rounded-xl transition-all duration-150 cursor-pointer {{ ($currentView ?? 'calendar') === 'calendar' ? 'bg-white text-secondary shadow-2xs border border-primary-200/60' : 'text-primary-600 hover:text-primary-900 hover:bg-white/50 border border-transparent' }}"
                                title="Calendar View"
                                data-target-view="calendar">
                            <x-heroicon-o-calendar-days class="w-4 h-4 shrink-0" />
                            <span>Calendar</span>
                        </button>

                        <button type="button"
                                id="viewListBtn"
                                class="view-toggle-btn inline-flex items-center gap-2 px-3.5 py-1.5 text-xs sm:text-sm font-bold rounded-xl transition-all duration-150 cursor-pointer {{ ($currentView ?? 'calendar') === 'list' ? 'bg-white text-secondary shadow-2xs border border-primary-200/60' : 'text-primary-600 hover:text-primary-900 hover:bg-white/50 border border-transparent' }}"
                                title="List View"
                                data-target-view="list">
                            <x-heroicon-o-list-bullet class="w-4 h-4 shrink-0" />
                            <span>List</span>
                        </button>
                    </div>
                </div>

                <!-- Calendar View Section -->
                <div id="calendarViewContainer" class="{{ ($currentView ?? 'calendar') === 'calendar' ? '' : 'hidden' }}">
                    @include('admin.bookings.calendar-index')
                </div>

                <!-- List View Section -->
                <div id="listViewContainer" class="{{ ($currentView ?? 'calendar') === 'list' ? '' : 'hidden' }}">
                    @include('admin.bookings.list-index')
                </div>

            </div>
        </main>
    </div>

    <!-- Reusable Shared Modals (Details, Create, Edit) -->
    @include('admin.bookings.modals')

    <!-- Shared Interactive JavaScript (View Toggle, Filters, Navigation, and Modals) -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // VIEW SWITCHER (CALENDAR <-> LIST)
            const viewCalendarBtn = document.getElementById('viewCalendarBtn');
            const viewListBtn = document.getElementById('viewListBtn');
            const calendarViewContainer = document.getElementById('calendarViewContainer');
            const listViewContainer = document.getElementById('listViewContainer');

            let activeView = '{{ $currentView ?? "calendar" }}';

            function setView(viewName) {
                activeView = viewName;

                if (viewName === 'calendar') {
                    calendarViewContainer.classList.remove('hidden');
                    listViewContainer.classList.add('hidden');

                    // Style Calendar Active
                    viewCalendarBtn.className = "view-toggle-btn inline-flex items-center gap-2 px-3.5 py-1.5 text-xs sm:text-sm font-bold rounded-xl transition-all duration-150 cursor-pointer bg-white text-secondary shadow-2xs border border-primary-200/60";
                    viewListBtn.className = "view-toggle-btn inline-flex items-center gap-2 px-3.5 py-1.5 text-xs sm:text-sm font-bold rounded-xl transition-all duration-150 cursor-pointer text-primary-600 hover:text-primary-900 hover:bg-white/50 border border-transparent";
                } else {
                    listViewContainer.classList.remove('hidden');
                    calendarViewContainer.classList.add('hidden');

                    // Style List Active
                    viewListBtn.className = "view-toggle-btn inline-flex items-center gap-2 px-3.5 py-1.5 text-xs sm:text-sm font-bold rounded-xl transition-all duration-150 cursor-pointer bg-white text-secondary shadow-2xs border border-primary-200/60";
                    viewCalendarBtn.className = "view-toggle-btn inline-flex items-center gap-2 px-3.5 py-1.5 text-xs sm:text-sm font-bold rounded-xl transition-all duration-150 cursor-pointer text-primary-600 hover:text-primary-900 hover:bg-white/50 border border-transparent";
                }

                // Update URL parameter without page reload
                const url = new URL(window.location);
                url.searchParams.set('view', viewName);
                window.history.replaceState({}, '', url);
            }

            if (viewCalendarBtn) {
                viewCalendarBtn.addEventListener('click', function () {
                    setView('calendar');
                });
            }

            if (viewListBtn) {
                viewListBtn.addEventListener('click', function () {
                    setView('list');
                });
            }

            // CALENDAR ROOM & DATE NAVIGATION
            const roomSelect = document.getElementById('roomSelect');
            const datePicker = document.getElementById('datePicker');

            function navigate(newRoomId, newDate) {
                const params = new URLSearchParams(window.location.search);
                if (newRoomId) params.set('room_id', newRoomId);
                if (newDate) params.set('date', newDate);
                params.set('view', activeView);
                window.location.search = params.toString();
            }

            if (roomSelect) {
                roomSelect.addEventListener('change', function () {
                    navigate(this.value, datePicker ? datePicker.value : '');
                });
            }

            if (datePicker) {
                datePicker.addEventListener('change', function () {
                    navigate(roomSelect ? roomSelect.value : '', this.value);
                });
            }

            // LIST VIEW SEARCH AND FILTERS
            const bookingSearchInput = document.getElementById('bookingSearchInput');
            const bookingFilterRoom = document.getElementById('bookingFilterRoom');
            const bookingFilterUser = document.getElementById('bookingFilterUser');
            const listRows = document.querySelectorAll('.booking-list-row');
            const noBookingsFilterResults = document.getElementById('noBookingsFilterResults');

            function filterListBookings() {
                const query = bookingSearchInput ? bookingSearchInput.value.trim().toLowerCase() : '';
                const selectedRoom = bookingFilterRoom ? bookingFilterRoom.value : '';
                const selectedUser = bookingFilterUser ? bookingFilterUser.value : '';

                let visibleCount = 0;

                listRows.forEach(function (row) {
                    const searchContent = row.dataset.search || '';
                    const rowRoomId = row.dataset.roomId || '';
                    const rowUserId = row.dataset.userId || '';

                    const matchesQuery = !query || searchContent.includes(query);
                    const matchesRoom = !selectedRoom || rowRoomId === selectedRoom;
                    const matchesUser = !selectedUser || rowUserId === selectedUser;

                    if (matchesQuery && matchesRoom && matchesUser) {
                        row.classList.remove('hidden');
                        visibleCount++;
                    } else {
                        row.classList.add('hidden');
                    }
                });

                if (noBookingsFilterResults) {
                    if (visibleCount === 0 && listRows.length > 0) {
                        noBookingsFilterResults.classList.remove('hidden');
                    } else {
                        noBookingsFilterResults.classList.add('hidden');
                    }
                }
            }

            if (bookingSearchInput) {
                bookingSearchInput.addEventListener('input', filterListBookings);
            }
            if (bookingFilterRoom) {
                bookingFilterRoom.addEventListener('change', filterListBookings);
            }
            if (bookingFilterUser) {
                bookingFilterUser.addEventListener('change', filterListBookings);
            }

            // SHARED MODAL: DETAILS MODAL
            const detailsModal = document.getElementById('detailsModal');
            const closeDetailsModalBtn = document.getElementById('closeDetailsModalBtn');
            const dismissDetailsBtn = document.getElementById('dismissDetailsBtn');
            const switchToEditModalBtn = document.getElementById('switchToEditModalBtn');

            let currentActiveBooking = null;

            function openDetailsModal(bookingData) {
                currentActiveBooking = bookingData;
                const detailsTitle = document.getElementById('detailsTitle');
                const detailsRoom = document.getElementById('detailsRoom');
                const detailsLocation = document.getElementById('detailsLocation');
                const detailsUser = document.getElementById('detailsUser');
                const detailsUserEmail = document.getElementById('detailsUserEmail');
                const detailsDate = document.getElementById('detailsDate');
                const detailsTime = document.getElementById('detailsTime');

                if (detailsTitle) detailsTitle.textContent = bookingData.title;
                if (detailsRoom) detailsRoom.textContent = bookingData.roomName;
                if (detailsLocation) detailsLocation.textContent = bookingData.roomLocation ? 'Location: ' + bookingData.roomLocation : '';
                if (detailsUser) detailsUser.textContent = bookingData.userName;
                if (detailsUserEmail) detailsUserEmail.textContent = bookingData.userEmail;
                if (detailsDate) detailsDate.textContent = bookingData.dateFormatted;
                if (detailsTime) detailsTime.textContent = bookingData.startTime + ' - ' + bookingData.endTime;

                if (detailsModal) detailsModal.classList.remove('hidden');
            }

            function closeDetailsModal() {
                if (detailsModal) detailsModal.classList.add('hidden');
            }

            if (closeDetailsModalBtn) closeDetailsModalBtn.addEventListener('click', closeDetailsModal);
            if (dismissDetailsBtn) dismissDetailsBtn.addEventListener('click', closeDetailsModal);

            // Calendar Cards -> Open Details Modal
            document.querySelectorAll('.booking-block').forEach(function (block) {
                block.addEventListener('click', function () {
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
                        userEmail: this.dataset.userEmail
                    };
                    openDetailsModal(data);
                });
            });

            // List Rows / Details Buttons -> Open Details Modal
            listRows.forEach(function (row) {
                // Opens details modal
                row.addEventListener('click', function (e) {
                    if (e.target.closest('.row-edit-btn')) return;

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
                        userEmail: this.dataset.userEmail
                    };
                    openDetailsModal(data);
                });
            });

            // SHARED MODAL: CREATE BOOKING MODAL
            const createModal = document.getElementById('createBookingModal');
            const closeCreateModalBtn = document.getElementById('closeCreateModalBtn');
            const cancelCreateModalBtn = document.getElementById('cancelCreateModalBtn');
            const createRoomSelect = document.getElementById('create_room_id');
            const createDateInput = document.getElementById('create_date');

            function openCreateModal() {
                // If opened in calendar view, synchronize room and date
                if (activeView === 'calendar' && roomSelect && createRoomSelect) {
                    createRoomSelect.value = roomSelect.value;
                } else if (bookingFilterRoom && bookingFilterRoom.value && createRoomSelect) {
                    createRoomSelect.value = bookingFilterRoom.value;
                }

                if (datePicker && createDateInput && !createDateInput.value) {
                    createDateInput.value = datePicker.value;
                }

                if (createModal) createModal.classList.remove('hidden');
            }

            function closeCreateModal() {
                if (createModal) createModal.classList.add('hidden');
            }

            document.querySelectorAll('.open-booking-modal-trigger').forEach(function (btn) {
                btn.addEventListener('click', openCreateModal);
            });

            const openBookingModalBtn = document.getElementById('openBookingModalBtn');
            if (openBookingModalBtn) openBookingModalBtn.addEventListener('click', openCreateModal);

            if (closeCreateModalBtn) closeCreateModalBtn.addEventListener('click', closeCreateModal);
            if (cancelCreateModalBtn) cancelCreateModalBtn.addEventListener('click', closeCreateModal);

            // SHARED MODAL: EDIT BOOKING MODAL
            const editModal = document.getElementById('editBookingModal');
            const closeEditModalBtn = document.getElementById('closeEditModalBtn');
            const cancelEditModalBtn = document.getElementById('cancelEditModalBtn');
            const editForm = document.getElementById('editBookingForm');

            function openEditModal(bookingData) {
                if (!bookingData) return;
                closeDetailsModal();

                const editRoomSelect = document.getElementById('edit_room_id');
                const editTitle = document.getElementById('edit_title');
                const editDate = document.getElementById('edit_date');
                const editStartTime = document.getElementById('edit_start_time');
                const editEndTime = document.getElementById('edit_end_time');

                if (editRoomSelect) editRoomSelect.value = bookingData.roomId;
                if (editTitle) editTitle.value = bookingData.title;
                if (editDate) editDate.value = bookingData.date;
                if (editStartTime) editStartTime.value = bookingData.startTime;
                if (editEndTime) editEndTime.value = bookingData.endTime;

                if (editForm) {
                    editForm.action = '/admin/bookings/' + bookingData.id;
                }

                if (editModal) editModal.classList.remove('hidden');
            }

            function closeEditModal() {
                if (editModal) editModal.classList.add('hidden');
            }

            if (switchToEditModalBtn) {
                switchToEditModalBtn.addEventListener('click', function () {
                    openEditModal(currentActiveBooking);
                });
            }

            // List View: Clicking "Edit" button on a row opens edit modal directly
            document.querySelectorAll('.row-edit-btn').forEach(function (btn) {
                btn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    const row = this.closest('.booking-list-row');
                    if (!row) return;

                    const data = {
                        id: row.dataset.bookingId,
                        title: row.dataset.title,
                        date: row.dataset.date,
                        dateFormatted: row.dataset.dateFormatted,
                        startTime: row.dataset.startTime,
                        endTime: row.dataset.endTime,
                        roomName: row.dataset.roomName,
                        roomLocation: row.dataset.roomLocation,
                        roomId: row.dataset.roomId,
                        userName: row.dataset.userName,
                        userEmail: row.dataset.userEmail
                    };
                    openEditModal(data);
                });
            });

            if (closeEditModalBtn) closeEditModalBtn.addEventListener('click', closeEditModal);
            if (cancelEditModalBtn) cancelEditModalBtn.addEventListener('click', closeEditModal);

            // BACKDROP & KEYBOARD CLOSING
            window.addEventListener('click', function (e) {
                if (e.target === detailsModal) closeDetailsModal();
                if (e.target === createModal) closeCreateModal();
                if (e.target === editModal) closeEditModal();
            });

            window.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    closeDetailsModal();
                    closeCreateModal();
                    closeEditModal();
                }
            });
        });
    </script>
</x-layout>
