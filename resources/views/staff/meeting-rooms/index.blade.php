<x-layout>
    <div class="min-h-screen flex flex-col md:flex-row">
        <!-- Vertical Left Navbar: menus 'My Bookings' and 'Meeting Rooms' -->
        <x-navbar active="meeting-rooms" />

        <!-- Main Content Area: Meeting Rooms List for Staff -->
        <main class="flex-1 p-6 sm:p-8 md:p-10 lg:p-12 overflow-x-hidden">
            <div class="max-w-6xl">

                <!-- Flash Notifications -->
                <x-flash-success class="mb-6" />
                <x-flash-error class="mb-6" />

                <!-- Header Row: Title & Search Bar (Search Aligned Right) -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                    <!-- Title: Meeting Rooms -->
                    <h1 class="text-2xl sm:text-3xl font-black text-primary-900 tracking-tight">
                        Meeting Rooms
                    </h1>

                    <!-- Search Input with Guaranteed Visible Magnifying Glass on the Right -->
                    <div class="relative w-full sm:w-72 md:w-80 flex items-center">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-primary-500 z-10">
                            <x-heroicon-o-magnifying-glass class="w-4 h-4 text-primary-500" />
                        </span>
                        <input type="text"
                               id="roomSearchInput"
                               placeholder="Search meeting room by name..."
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-primary-300/80 bg-white/70 backdrop-blur-xs text-primary-900 text-sm placeholder-primary-400 focus:outline-none focus:ring-2 focus:ring-secondary focus:border-secondary shadow-2xs transition-all" />
                    </div>
                </div>

                @php
                    // Only active meeting rooms (inactive ones excluded)
                    $activeRooms = $meetingRooms->where('is_active', true);
                @endphp

                <!-- List / Table of Active Meeting Rooms -->
                <div class="divide-y divide-primary-200/60 border-t border-b border-primary-200/60" id="meetingRoomsList">
                    @forelse ($activeRooms as $room)
                        <div class="room-list-item py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition-colors hover:bg-primary-100/50 rounded-xl px-3 sm:px-4"
                             data-room-name="{{ strtolower($room->name) }}"
                             data-room-location="{{ strtolower($room->location) }}">
                            
                            <!-- Active Status Dot & Name -->
                            <div class="flex items-center gap-3 min-w-0 sm:w-1/3">
                                <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-2xs shrink-0" title="Active"></span>
                                <span class="text-base font-bold text-primary-900 truncate" title="{{ $room->name }}">
                                    {{ $room->name }}
                                </span>
                            </div>

                            <!-- Location (Truncated) & Capacity -->
                            <div class="flex items-center justify-between sm:justify-start gap-4 sm:w-1/2 min-w-0">
                                <!-- Location Truncated -->
                                <div class="flex items-center gap-1.5 text-xs sm:text-sm text-primary-600 truncate max-w-[170px] sm:max-w-[200px] md:max-w-xs"
                                     title="{{ $room->location }}">
                                    <x-heroicon-o-map-pin class="w-4 h-4 shrink-0 text-primary-400" />
                                    <span class="truncate">{{ $room->location }}</span>
                                </div>

                                <!-- Capacity -->
                                <div class="flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-primary-800 bg-primary-100/80 border border-primary-200/60 px-2.5 py-1 rounded-lg shrink-0">
                                    <x-heroicon-o-users class="w-4 h-4 text-secondary shrink-0" />
                                    <span>{{ $room->capacity }} People</span>
                                </div>
                            </div>

                            <!-- Action Button: Book (Opens staff.meeting-rooms.show) -->
                            <div class="flex items-center justify-end shrink-0">
                                <a href="{{ route('staff.meeting-rooms.show', $room->id) }}"
                                   class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-white bg-primary hover:bg-secondary rounded-xl shadow-2xs hover:shadow-xs transition-all duration-200 transform hover:-translate-y-0.5 cursor-pointer">
                                    <span>Book</span>
                                    <x-heroicon-o-calendar-days class="w-3.5 h-3.5" />
                                </a>
                            </div>
                        </div>
                    @empty
                        <!-- Empty State: No active rooms -->
                        <div class="py-16 px-4 text-center">
                            <div class="w-16 h-16 mx-auto mb-3 rounded-2xl flex items-center justify-center text-primary-400">
                                <x-heroicon-o-building-office-2 class="w-8 h-8 text-secondary" />
                            </div>
                            <h3 class="text-lg font-bold text-primary-900 mb-1">No Meeting Rooms Available</h3>
                            <p class="text-sm text-primary-600 max-w-md mx-auto leading-relaxed">
                                There are currently no active meeting rooms available for reservation.
                            </p>
                        </div>
                    @endforelse
                </div>

                <!-- No Search Results Indicator -->
                <div id="noSearchResults" class="hidden py-12 text-center text-primary-500">
                    <p class="font-medium text-sm">No meeting rooms match your search query.</p>
                </div>

            </div>
        </main>
    </div>

    <!-- Client-side Real-time Search Filter -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('roomSearchInput');
            const roomItems = document.querySelectorAll('.room-list-item');
            const noResults = document.getElementById('noSearchResults');

            if (searchInput) {
                searchInput.addEventListener('input', function (e) {
                    const query = e.target.value.toLowerCase().trim();
                    let matchCount = 0;

                    roomItems.forEach(item => {
                        const name = item.getAttribute('data-room-name') || '';
                        const location = item.getAttribute('data-room-location') || '';
                        if (name.includes(query) || location.includes(query)) {
                            item.style.display = '';
                            matchCount++;
                        } else {
                            item.style.display = 'none';
                        }
                    });

                    if (noResults) {
                        noResults.classList.toggle('hidden', matchCount > 0);
                    }
                });
            }
        });
    </script>
</x-layout>
