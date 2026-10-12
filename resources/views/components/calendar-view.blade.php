@props([
    'days',
    'bookings',
    'selectedRoomId' => null,
    'roomTitle' => null,
    'startHour' => 8,
    'endHour' => 22,
    'hourHeight' => 32,
])

@php
    $totalHours = $endHour - $startHour + 1; // 15 hours
    $gridHeight = $hourHeight * $totalHours; // 480px
    $maxMinutes = $totalHours * 60; // 900 minutes
@endphp

<!-- Reusable Calendar View Component: 7 Columns -->
<div {{ $attributes->merge(['class' => 'bg-white rounded-3xl border border-primary-200/80 shadow-xs overflow-hidden']) }}>
    
    <!-- Calendar Header Info -->
    <div class="p-4 sm:p-5 border-b border-primary-200/60 bg-primary-50/40 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-2.5">
            <span class="text-xs sm:text-sm font-bold text-primary-900" id="currentRoomDisplayTitle">
                {{ $roomTitle ?? 'Room Schedule' }}
            </span>
        </div>
        <div class="flex items-center gap-4 text-xs font-medium text-primary-600">
            <div class="flex items-center gap-1.5">
                <span class="w-3.5 h-3.5 rounded-md bg-[#8B5A2B]/20 border border-[#8B5A2B]/40"></span>
                <span>Booked</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-3.5 h-3.5 rounded-md bg-white border border-primary-200"></span>
                <span>Available</span>
            </div>
        </div>
    </div>

    <!-- 7-Column Calendar Grid Container -->
    <div class="overflow-x-auto">
        <div class="min-w-[800px]">
            
            <!-- Day Column Headers -->
            <div class="grid grid-cols-[64px_repeat(7,1fr)] border-b border-primary-200/80 bg-primary-100/30">
                <!-- Time column header spacer -->
                <div class="py-3 px-2 text-center text-xs font-bold text-primary-500 uppercase tracking-wider border-r border-primary-200/60 flex items-center justify-center">
                    Time
                </div>

                @foreach ($days as $index => $day)
                    <div class="py-2.5 px-2 text-center border-r border-primary-200/60 last:border-r-0 {{ $day['is_today'] ? 'bg-primary-100/60' : '' }} {{ $index === 0 ? 'ring-1 ring-inset ring-secondary/30' : '' }}">
                        <div class="text-xs font-bold {{ $day['is_today'] ? 'text-secondary' : 'text-primary-600' }} uppercase tracking-wider">
                            {{ $day['day_name'] }}
                        </div>
                        <div class="text-sm sm:text-base font-black text-primary-950 flex items-center justify-center gap-1 mt-0.5">
                            <span>{{ $day['day_number'] }} {{ $day['month_name'] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Hourly Grid Body -->
            <div class="relative grid grid-cols-[64px_repeat(7,1fr)] bg-white" style="height: {{ $gridHeight }}px;" id="calendarGridBody">
                
                <!-- Time Labels Column -->
                <div class="border-r border-primary-200/60 select-none bg-primary-50/25 flex flex-col" style="height: {{ $gridHeight }}px;">
                    @for ($hour = $startHour; $hour <= $endHour; $hour++)
                        <div class="flex items-center justify-end pr-2.5 text-xs font-bold text-primary-500/90 border-b border-primary-100/70"
                             style="height: {{ $hourHeight }}px;">
                            {{ sprintf('%02d:00', $hour) }}
                        </div>
                    @endfor
                </div>

                <!-- 7 Day Columns -->
                @foreach ($days as $dayIndex => $day)
                    <div class="day-column relative border-r border-primary-200/60 last:border-r-0"
                         data-date="{{ $day['date_string'] }}"
                         data-day-index="{{ $dayIndex }}"
                         style="height: {{ $gridHeight }}px;">
                        
                        <!-- Clean Background Hour Grid Rows -->
                        <div class="absolute inset-0 flex flex-col pointer-events-none">
                            @for ($hour = $startHour; $hour <= $endHour; $hour++)
                                <div class="border-b border-primary-100/70"
                                     style="height: {{ $hourHeight }}px;"></div>
                            @endfor
                        </div>

                        <!-- Render Blocked Booking Sections for this column -->
                        @php
                            $dayBookings = $bookings->filter(function ($b) use ($day, $selectedRoomId) {
                                $bDate = is_string($b->date) ? $b->date : $b->date->format('Y-m-d');
                                return $bDate === $day['date_string'] && ($selectedRoomId ? ((string)$b->meeting_room_id === (string)$selectedRoomId) : true);
                            });
                        @endphp

                        @foreach ($dayBookings as $booking)
                            @php
                                // Parse start and end hours/minutes
                                $startParts = explode(':', $booking->start_time);
                                $endParts = explode(':', $booking->end_time);
                                $startMin = ((int)$startParts[0] - $startHour) * 60 + (int)($startParts[1] ?? 0);
                                $endMin = ((int)$endParts[0] - $startHour) * 60 + (int)($endParts[1] ?? 0);

                                // Clamp to 08:00 - 23:00 grid boundaries
                                $startMin = max(0, min($maxMinutes, $startMin));
                                $endMin = max(0, min($maxMinutes, $endMin));
                                $durationMin = max(24, $endMin - $startMin);

                                $topPx = round(($startMin / 60) * $hourHeight);
                                $heightPx = max(20, round(($durationMin / 60) * $hourHeight));
                            @endphp

                            <!-- Blocked Section with Title on Top-Left -->
                            <div class="booking-block absolute left-1 right-1 rounded-lg bg-primary-100/90 border-l-4 border-secondary text-primary-950 p-1 px-1.5 shadow-xs hover:shadow-md hover:bg-primary-200/90 transition-all duration-150 cursor-pointer overflow-hidden z-10"
                                 style="top: {{ $topPx }}px; height: {{ $heightPx }}px;"
                                 data-booking-id="{{ $booking->id }}"
                                 data-title="{{ $booking->title }}"
                                 data-date="{{ is_string($booking->date) ? $booking->date : $booking->date->format('Y-m-d') }}"
                                 data-date-formatted="{{ $day['day_full'] }}, {{ $day['date_formatted'] }}"
                                 data-start-time="{{ substr($booking->start_time, 0, 5) }}"
                                 data-end-time="{{ substr($booking->end_time, 0, 5) }}"
                                 data-room-name="{{ $booking->meetingRoom?->name ?? 'Meeting Room' }}"
                                 data-room-location="{{ $booking->meetingRoom?->location ?? '' }}"
                                 data-room-id="{{ $booking->meeting_room_id }}"
                                 data-user-name="{{ $booking->user?->name ?? 'Staff' }}"
                                 data-user-email="{{ $booking->user?->email ?? '' }}"
                                 data-user-id="{{ $booking->user_id }}"
                                 title="{{ $booking->title }} ({{ substr($booking->start_time, 0, 5) }} - {{ substr($booking->end_time, 0, 5) }})">
                                
                                <!-- Title written on top-left of the block -->
                                <div class="flex items-start justify-between gap-1">
                                    <span class="font-bold text-[11px] text-primary-950 truncate block leading-tight text-left">
                                        {{ $booking->title }}
                                    </span>
                                </div>
                                
                                <!-- Time subtitle under title -->
                                @if ($heightPx >= 28)
                                    <div class="text-[9px] font-semibold text-secondary leading-tight mt-0.5 truncate text-left">
                                        {{ substr($booking->start_time, 0, 5) }} - {{ substr($booking->end_time, 0, 5) }}
                                    </div>
                                @endif

                                @if ($heightPx >= 52 && $booking->user)
                                    <div class="text-[9px] text-primary-600 truncate mt-0.5 text-left">
                                        By {{ $booking->user->name }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endforeach

            </div>
        </div>
    </div>
</div>
