<div class="max-w-6xl">
    <!-- Title: Meeting Rooms -->
    <h1 class="text-2xl sm:text-3xl font-black text-primary-900 tracking-tight mb-6">
        Meeting Rooms
    </h1>

    <!-- Action & Search Bar Row (Justify Alignment: Button on Left, Search on Right) -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <!-- Create Button Under Title -->
        <a href="{{ \Route::has('admin.meeting-rooms.create') ? route('admin.meeting-rooms.create') : '#' }}"
           class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-primary text-white text-sm font-bold rounded-xl shadow-md hover:bg-secondary hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5 shrink-0">
            <x-heroicon-o-plus class="w-4 h-4 stroke-[2.5]" />
            <span>Create a New Meeting Room</span>
        </a>

        <!-- Search Input with Guaranteed Visible Magnifying Glass -->
        <div class="relative w-full sm:w-72 md:w-80 flex items-center">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-primary-500 z-10">
                <x-heroicon-o-magnifying-glass class="w-4 h-4 text-primary-500" />
            </span>
            <input type="text"
                   id="roomSearchInput"
                   placeholder="Search meeting room by name..."
                   class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-primary-300/80 bg-white/70 backdrop-blur-xs text-primary-900 text-sm placeholder-primary-400 focus:outline-none focus:ring-2 focus:ring-secondary focus:bg-white shadow-2xs transition-all duration-200" />
        </div>
    </div>

    <!-- Meeting Rooms List Data -->
    @php
        $rooms = $meetingRooms ?? \App\Models\MeetingRoom::all();
        if ($rooms->isEmpty()) {
            // Demo fallback rooms for visual preview
            $rooms = collect([
                (object)[
                    'id' => 1,
                    'name' => 'Boardroom Olympus',
                    'location' => 'Main Tower, 12th Floor, Suite 1201 (Executive Wing)',
                    'capacity' => 20,
                    'is_active' => true,
                ],
                (object)[
                    'id' => 2,
                    'name' => 'Innovation Hub',
                    'location' => 'Creative Wing, 3rd Floor, Room 304 near Central Cafeteria',
                    'capacity' => 12,
                    'is_active' => true,
                ],
                (object)[
                    'id' => 3,
                    'name' => 'Focus Room Alpha',
                    'location' => 'East Wing, 2nd Floor, Room 210',
                    'capacity' => 4,
                    'is_active' => false,
                ],
                (object)[
                    'id' => 4,
                    'name' => 'Conference Hall A',
                    'location' => 'Ground Floor, Convention Center, West Hallway',
                    'capacity' => 50,
                    'is_active' => true,
                ],
            ]);
        }
    @endphp

    <!-- List of Meeting Rooms (No white container, base background) -->
    <div class="divide-y divide-primary-200/60 border-t border-b border-primary-200/60" id="meetingRoomsList">
        @forelse ($rooms as $room)
            <div class="room-list-item py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition-colors hover:bg-primary-100/50 rounded-xl px-3 sm:px-4"
                 data-room-name="{{ strtolower($room->name) }}">
                
                <!-- Active Status (Static green dot for yes, red dot for no) & Name -->
                <div class="flex items-center gap-3 min-w-0 sm:w-1/3">
                    @if ($room->is_active)
                        <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-2xs shrink-0" title="Active"></span>
                    @else
                        <span class="inline-block w-2.5 h-2.5 rounded-full bg-rose-500 shadow-2xs shrink-0" title="Inactive"></span>
                    @endif

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
                        <x-heroicon-o-users class="w-4 h-4 text-secondary" />
                        <span>{{ $room->capacity }} People</span>
                    </div>
                </div>

                <!-- Action Button to Edit Each Room -->
                <div class="flex items-center justify-end shrink-0">
                    <a href="{{ \Route::has('admin.meeting-rooms.edit') ? route('admin.meeting-rooms.edit', $room->id) : '#' }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-bold text-primary-900 bg-white hover:bg-primary-100 border border-primary-200 rounded-lg shadow-2xs hover:shadow-xs transition-all duration-200">
                        <x-heroicon-o-pencil-square class="w-3.5 h-3.5 text-secondary" />
                        <span>Edit</span>
                    </a>
                </div>
            </div>
        @empty
            <div class="py-12 text-center text-primary-500">
                <p class="font-medium text-sm">No meeting rooms found.</p>
            </div>
        @endforelse
    </div>

    <!-- No Search Results Indicator -->
    <div id="noSearchResults" class="hidden py-12 text-center text-primary-500">
        <p class="font-medium text-sm">No meeting rooms match your search query.</p>
    </div>
</div>

<!-- Client-side real-time name filter -->
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
                    if (name.includes(query)) {
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
