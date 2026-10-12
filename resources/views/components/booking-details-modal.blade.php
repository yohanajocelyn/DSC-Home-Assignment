@props([
    'id' => 'bookingDetailsModal',
    'prefix' => null,
    'showCreator' => null,
])

@php
    $prefix = $prefix ?? ($id === 'staffDetailsModal' ? 'staffDetails' : ($id === 'detailsModal' ? 'details' : 'bookingDetails'));
    $showCreator = $showCreator ?? (auth()->user()?->isAdmin() ?? false);
@endphp

<!-- Reusable Booking Details Modal Component -->
<div id="{{ $id }}"
     {{ $attributes->merge(['class' => 'booking-details-modal fixed inset-0 z-50 hidden bg-black/45 backdrop-blur-xs flex items-center justify-center p-4 transition-opacity duration-200']) }}
     role="dialog"
     aria-modal="true"
     aria-labelledby="{{ $prefix }}Title">
    <div class="bg-white rounded-3xl shadow-2xl border border-primary-200 max-w-lg w-full overflow-hidden transform transition-all duration-200">
        <!-- Modal Header -->
        <div class="p-6 pb-4 border-b border-primary-200/60 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-secondary/15 flex items-center justify-center text-secondary">
                    <x-heroicon-o-calendar-days class="w-5 h-5" />
                </div>
                <div>
                    <h3 class="text-lg font-black text-primary-900 tracking-tight">Booking Details</h3>
                </div>
            </div>
            <button type="button"
                    id="{{ $id === 'detailsModal' ? 'closeDetailsModalBtn' : ($id === 'staffDetailsModal' ? 'closeStaffDetailsModalBtn' : $prefix . 'CloseBtn') }}"
                    onclick="const m = this.closest('.booking-details-modal'); if (m) m.classList.add('hidden');"
                    class="modal-close-trigger p-1.5 rounded-lg text-primary-400 hover:text-primary-800 hover:bg-primary-100/60 transition-colors cursor-pointer"
                    aria-label="Close modal">
                <x-heroicon-o-x-mark class="w-5 h-5" />
            </button>
        </div>

        <!-- Modal Content Details -->
        <div class="p-6 space-y-4">
            <!-- Title Display -->
            <div>
                <label class="block text-[11px] font-bold text-primary-500 uppercase tracking-wider mb-0.5">Booking Title</label>
                <p id="{{ $prefix }}Title" class="text-base sm:text-lg font-black text-primary-900 leading-snug modal-detail-title"></p>
            </div>

            <!-- Grid: Room, Reserved By (or Capacity), Date & Time -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-primary-100">
                <div>
                    <label class="block text-[11px] font-bold text-primary-500 uppercase tracking-wider mb-0.5">Meeting Room</label>
                    <p id="{{ $prefix }}Room" class="text-sm font-bold text-primary-900 modal-detail-room"></p>
                    <p id="{{ $prefix }}Location" class="text-xs text-primary-600 font-medium mt-0.5 modal-detail-location"></p>
                </div>

                @if ($showCreator)
                    <div>
                        <label class="block text-[11px] font-bold text-primary-500 uppercase tracking-wider mb-0.5">Reserved By</label>
                        <p id="{{ $prefix }}User" class="text-sm font-bold text-primary-900 modal-detail-user"></p>
                        <p id="{{ $prefix }}UserEmail" class="text-xs text-primary-600 font-medium mt-0.5 modal-detail-user-email"></p>
                    </div>
                @else
                    <div>
                        <label class="block text-[11px] font-bold text-primary-500 uppercase tracking-wider mb-0.5">Room Capacity</label>
                        <p id="{{ $prefix }}Capacity" class="text-sm font-bold text-primary-900 modal-detail-capacity"></p>
                    </div>
                @endif

                <div>
                    <label class="block text-[11px] font-bold text-primary-500 uppercase tracking-wider mb-0.5">Date</label>
                    <p id="{{ $prefix }}Date" class="text-sm font-bold text-primary-900 modal-detail-date"></p>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-primary-500 uppercase tracking-wider mb-0.5">Time Interval</label>
                    <p id="{{ $prefix }}Time" class="text-sm font-bold text-secondary modal-detail-time"></p>
                </div>
            </div>
        </div>

        @if (isset($actions) && trim($actions) !== '')
            <!-- Modal Footer: Role-Authorized Actions Slot -->
            <div class="p-4 sm:p-5 border-t border-primary-200/60 bg-primary-50/30 flex items-center justify-end gap-3">
                {{ $actions }}
            </div>
        @endif
    </div>
</div>
