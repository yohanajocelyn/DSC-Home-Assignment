<!-- MODAL 1: Reusable Booking Details Modal with Role-Authorized Actions -->
<x-booking-details-modal id="detailsModal" :show-creator="true">
    @can('update', App\Models\Booking::class)
        <x-slot:actions>
            <button type="button" id="switchToEditModalBtn"
                    class="inline-flex items-center gap-1.5 px-5 py-2 text-xs sm:text-sm font-bold bg-primary text-white rounded-xl shadow-md hover:bg-secondary hover:shadow-lg transition-all transform hover:-translate-y-0.5 cursor-pointer">
                <x-heroicon-o-pencil-square class="w-4 h-4" />
                <span>Edit</span>
            </button>
        </x-slot:actions>
    @endcan
</x-booking-details-modal>

<!-- MODAL 2: "Book a Room" Modal (Opened when clicking "Book a Room" button) -->
<div id="createBookingModal" class="fixed inset-0 z-50 hidden bg-black/45 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl border border-primary-200 max-w-lg w-full overflow-hidden transform transition-all duration-200">
        <!-- Modal Header -->
        <div class="p-6 pb-4 border-b border-primary-200/60 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-primary/10 flex items-center justify-center text-primary">
                    <x-heroicon-o-plus class="w-5 h-5 stroke-[2.5]" />
                </div>
                <div>
                    <h3 class="text-lg font-black text-primary-900 tracking-tight">Book a Room</h3>
                    <p class="text-xs text-primary-600 font-medium">Reserve a timeslot for the selected meeting room</p>
                </div>
            </div>
            <button type="button" id="closeCreateModalBtn" class="p-1.5 rounded-lg text-primary-400 hover:text-primary-800 hover:bg-primary-100/60 transition-colors cursor-pointer">
                <x-heroicon-o-x-mark class="w-5 h-5" />
            </button>
        </div>

        <!-- Form: Booking Creation -->
        <form method="POST" action="{{ route('admin.bookings.store') }}" id="createBookingForm">
            @csrf
            <div class="p-6 space-y-4">
                <!-- Meeting Room -->
                <div>
                    <x-input-label for="create_room_id">
                        <span>Meeting Room</span>
                        <span class="text-rose-500 ml-0.5">*</span>
                    </x-input-label>
                    <select name="meeting_room_id"
                            id="create_room_id"
                            required
                            class="w-full mt-1.5 px-3.5 py-2.5 rounded-xl border border-primary-300 bg-white text-primary-900 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-secondary cursor-pointer">
                        @foreach ($meetingRooms as $room)
                            @if ($room->is_active)
                                <option value="{{ $room->id }}" {{ (string)$room->id === (string)($selectedRoomId ?? '') ? 'selected' : '' }}>
                                    {{ $room->name }} {{ $room->location ? '('.$room->location.')' : '' }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                    <x-input-error :messages="($errors ?? null)?->get('meeting_room_id')" class="mt-1" />
                </div>

                <!-- Title Field -->
                <div>
                    <x-input-label for="create_title">
                        <span>Title</span>
                        <span class="text-rose-500 ml-0.5">*</span>
                    </x-input-label>
                    <x-input id="create_title"
                             type="text"
                             name="title"
                             value="{{ old('title') }}"
                             required
                             placeholder="e.g. Weekly Engineering Sync"
                             class="w-full mt-1.5" />
                    <x-input-error :messages="($errors ?? null)?->get('title')" class="mt-1" />
                </div>

                <!-- Date Field -->
                <div>
                    <x-input-label for="create_date">
                        <span>Date</span>
                        <span class="text-rose-500 ml-0.5">*</span>
                    </x-input-label>
                    <x-input id="create_date"
                             type="date"
                             name="date"
                             value="{{ old('date', $selectedDate) }}"
                             required
                             class="w-full mt-1.5 cursor-pointer" />
                    <x-input-error :messages="($errors ?? null)?->get('date')" class="mt-1" />
                </div>

                <!-- Start Time & End Time -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="create_start_time">
                            <span>Start Time</span>
                            <span class="text-rose-500 ml-0.5">*</span>
                        </x-input-label>
                        <x-input id="create_start_time"
                                 type="time"
                                 name="start_time"
                                 value="{{ old('start_time', '09:00') }}"
                                 min="08:00"
                                 max="21:59"
                                 required
                                 class="w-full mt-1.5 cursor-pointer" />
                        <x-input-error :messages="($errors ?? null)?->get('start_time')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="create_end_time">
                            <span>End Time</span>
                            <span class="text-rose-500 ml-0.5">*</span>
                        </x-input-label>
                        <x-input id="create_end_time"
                                 type="time"
                                 name="end_time"
                                 value="{{ old('end_time', '10:00') }}"
                                 min="08:01"
                                 max="22:00"
                                 required
                                 class="w-full mt-1.5 cursor-pointer" />
                        <x-input-error :messages="($errors ?? null)?->get('end_time')" class="mt-1" />
                    </div>
                </div>
            </div>

            <!-- Modal Footer: Cancel and Book button on bottom right -->
            <div class="p-4 sm:p-5 border-t border-primary-200/60 bg-primary-50/30 flex items-center justify-end gap-3">
                <button type="button" id="cancelCreateModalBtn"
                        class="px-5 py-2.5 text-xs sm:text-sm font-semibold text-primary-700 bg-white hover:bg-primary-100/80 border border-primary-300 rounded-xl shadow-2xs transition-all cursor-pointer">
                    Cancel
                </button>
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-primary text-white text-xs sm:text-sm font-bold rounded-xl shadow-md hover:bg-secondary hover:shadow-lg transition-all transform hover:-translate-y-0.5 cursor-pointer">
                    <span>Book</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 3: Edit Booking Modal (Opened when clicking Edit inside Details modal) -->
<div id="editBookingModal" class="fixed inset-0 z-50 hidden bg-black/45 backdrop-blur-xs flex items-center justify-center p-4">
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
            <button type="button" id="closeEditModalBtn" class="p-1.5 rounded-lg text-primary-400 hover:text-primary-800 hover:bg-primary-100/60 transition-colors cursor-pointer">
                <x-heroicon-o-x-mark class="w-5 h-5" />
            </button>
        </div>

        <!-- Form: Booking Update -->
        <form method="POST" action="" id="editBookingForm">
            @csrf
            @method('PUT')
            <div class="p-6 space-y-4">
                <!-- Meeting Room -->
                <div>
                    <x-input-label for="edit_room_id">
                        <span>Meeting Room</span>
                        <span class="text-rose-500 ml-0.5">*</span>
                    </x-input-label>
                    <select name="meeting_room_id"
                            id="edit_room_id"
                            required
                            class="w-full mt-1.5 px-3.5 py-2.5 rounded-xl border border-primary-300 bg-white text-primary-900 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-secondary cursor-pointer">
                        @foreach ($meetingRooms as $room)
                            @if ($room->is_active)
                                <option value="{{ $room->id }}">
                                    {{ $room->name }} {{ $room->location ? '('.$room->location.')' : '' }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                    <x-input-error :messages="($errors ?? null)?->get('meeting_room_id')" class="mt-1" />
                </div>

                <!-- Title Field -->
                <div>
                    <x-input-label for="edit_title">
                        <span>Title</span>
                        <span class="text-rose-500 ml-0.5">*</span>
                    </x-input-label>
                    <x-input id="edit_title"
                             type="text"
                             name="title"
                             required
                             class="w-full mt-1.5" />
                </div>

                <!-- Date Field -->
                <div>
                    <x-input-label for="edit_date">
                        <span>Date</span>
                        <span class="text-rose-500 ml-0.5">*</span>
                    </x-input-label>
                    <x-input id="edit_date"
                             type="date"
                             name="date"
                             required
                             class="w-full mt-1.5 cursor-pointer" />
                </div>

                <!-- Start Time & End Time -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="edit_start_time">
                            <span>Start Time</span>
                            <span class="text-rose-500 ml-0.5">*</span>
                        </x-input-label>
                        <x-input id="edit_start_time"
                                 type="time"
                                 name="start_time"
                                 min="08:00"
                                 max="21:59"
                                 required
                                 class="w-full mt-1.5 cursor-pointer" />
                        <x-input-error :messages="($errors ?? null)?->get('start_time')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="edit_end_time">
                            <span>End Time</span>
                            <span class="text-rose-500 ml-0.5">*</span>
                        </x-input-label>
                        <x-input id="edit_end_time"
                                 type="time"
                                 name="end_time"
                                 min="08:01"
                                 max="22:00"
                                 required
                                 class="w-full mt-1.5 cursor-pointer" />
                        <x-input-error :messages="($errors ?? null)?->get('end_time')" class="mt-1" />
                    </div>
                </div>
            </div>

            <!-- Modal Footer: Cancel and Save changes button on bottom right -->
            <div class="p-4 sm:p-5 border-t border-primary-200/60 bg-primary-50/30 flex items-center justify-end gap-3">
                <button type="button" id="cancelEditModalBtn"
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

<!-- MODAL 4: Generic Confirmation Modal for Cancelling Bookings -->
<x-confirm-modal
    id="cancelBookingModal"
    title="Cancel Booking"
    message="Are you sure you want to cancel this booking? This will remove the reservation from the schedule."
    confirm-text="Yes, Cancel"
    cancel-text="Keep Booking"
    variant="danger"
    method="DELETE"
/>
