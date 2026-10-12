<x-layout>
    <div class="min-h-screen flex flex-col md:flex-row">
        <!-- Vertical Left Navbar (Same as Meeting Rooms) -->
        <x-navbar active="bookings" />

        <!-- Main Content Area: Matching padding and margin of Meeting Rooms view -->
        <main class="flex-1 p-6 sm:p-8 md:p-10 lg:p-12 overflow-x-hidden">
            <div class="max-w-7xl">

                <!-- Flash Notifications -->
                <x-flash-success class="mb-6" />
                <x-flash-error class="mb-6" title="Booking could not be processed:" />

                <!-- Header Row: Bookings Title and 2-Icon View Switcher -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                    <!-- Title: Bookings (Positioned with exact same margin/padding as Meeting Rooms title) -->
                    <h1 class="text-2xl sm:text-3xl font-black text-primary-900 tracking-tight">
                        Bookings
                    </h1>

                    <!-- 2 Icon Buttons Side by Side (Switch between Calendar Index and List Index) -->
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
            // ==========================================
            // 1. VIEW SWITCHER (CALENDAR <-> LIST)
            // ==========================================
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

            // ==========================================
            // 2. CALENDAR ROOM & DATE NAVIGATION
            // ==========================================
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

            // ==========================================
            // 3. LIST VIEW SEARCH & DUAL FILTERS
            // ==========================================
            const bookingSearchInput = document.getElementById('bookingSearchInput');
            const bookingFilterRoom = document.getElementById('bookingFilterRoom');
            const bookingFilterUser = document.getElementById('bookingFilterUser');
            const bookingFilterTiming = document.getElementById('bookingFilterTiming');
            const listRows = document.querySelectorAll('.booking-list-row');
            const noBookingsFilterResults = document.getElementById('noBookingsFilterResults');
            const todayStr = '{{ now()->format("Y-m-d") }}';

            function filterListBookings() {
                const query = bookingSearchInput ? bookingSearchInput.value.trim().toLowerCase() : '';
                const selectedRoom = bookingFilterRoom ? bookingFilterRoom.value : '';
                const selectedUser = bookingFilterUser ? bookingFilterUser.value : '';
                const selectedTiming = bookingFilterTiming ? bookingFilterTiming.value : '';

                let visibleCount = 0;

                listRows.forEach(function (row) {
                    const searchContent = row.dataset.search || '';
                    const rowRoomId = row.dataset.roomId || '';
                    const rowUserId = row.dataset.userId || '';
                    const rowDate = row.dataset.date || '';

                    const matchesQuery = !query || searchContent.includes(query);
                    const matchesRoom = !selectedRoom || rowRoomId === selectedRoom;
                    const matchesUser = !selectedUser || rowUserId === selectedUser;

                    let matchesTiming = true;
                    if (selectedTiming === 'upcoming') {
                        matchesTiming = rowDate >= todayStr;
                    } else if (selectedTiming === 'past') {
                        matchesTiming = rowDate < todayStr;
                    }

                    if (matchesQuery && matchesRoom && matchesUser && matchesTiming) {
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
            if (bookingFilterTiming) {
                bookingFilterTiming.addEventListener('change', filterListBookings);
            }

            // ==========================================
            // 4. SHARED MODAL: DETAILS MODAL
            // ==========================================
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
                // Clicking anywhere on row (except when clicking Edit or Cancel button) opens details modal
                row.addEventListener('click', function (e) {
                    if (e.target.closest('.row-edit-btn') || e.target.closest('.open-cancel-modal-btn')) return;

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

            // ==========================================
            // 5. SHARED MODAL: CREATE BOOKING MODAL
            // ==========================================
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

            // ==========================================
            // 6. SHARED MODAL: EDIT BOOKING MODAL
            // ==========================================
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

            // CANCEL BOOKING MODAL
            const cancelModal = document.getElementById('cancelBookingModal');
            const cancelForm = document.getElementById('cancelBookingModal_form');
            const cancelMessage = document.getElementById('cancelBookingModal_message');

            function openCancelModal(actionUrl, bookingTitle) {
                if (cancelForm && actionUrl) {
                    cancelForm.action = actionUrl;
                }
                if (cancelMessage) {
                    cancelMessage.textContent = bookingTitle
                        ? `Are you sure you want to cancel the booking "${bookingTitle}"? This reservation will be removed from the schedule.`
                        : 'Are you sure you want to cancel this booking? This reservation will be removed from the schedule.';
                }
                if (cancelModal) cancelModal.classList.remove('hidden');
            }

            function closeCancelModal() {
                if (cancelModal) cancelModal.classList.add('hidden');
            }

            document.querySelectorAll('.open-cancel-modal-btn').forEach(function (btn) {
                btn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    const actionUrl = this.dataset.action;
                    const bookingTitle = this.dataset.bookingTitle;
                    openCancelModal(actionUrl, bookingTitle);
                });
            });

            if (cancelModal) {
                cancelModal.querySelectorAll('.modal-close-trigger, .modal-cancel-trigger').forEach(function (btn) {
                    btn.addEventListener('click', closeCancelModal);
                });
            }

            // BACKDROP & KEYBOARD CLOSING
            window.addEventListener('click', function (e) {
                if (e.target === detailsModal) closeDetailsModal();
                if (e.target === createModal) closeCreateModal();
                if (e.target === editModal) closeEditModal();
                if (e.target === cancelModal) closeCancelModal();
            });

            window.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    closeDetailsModal();
                    closeCreateModal();
                    closeEditModal();
                    closeCancelModal();
                }
            });
        });
    </script>
</x-layout>
