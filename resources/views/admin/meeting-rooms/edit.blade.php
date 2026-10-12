<x-layout title="Edit Meeting Room - DSC Admin">
    <div class="flex flex-col md:flex-row min-h-screen">
        <x-navbar active="meeting-rooms" class="h-full shrink-0 overflow-y-auto" />

        <main class="flex-1 p-6 sm:p-8 md:p-10 lg:p-12 overflow-x-hidden">
            <div class="max-w-4xl">
                
                <!-- Flash Error Notification -->
                <x-flash-error class="mb-6" />

                <!-- Header with Back Button -->
                <div class="flex items-center gap-4 mb-8">
                    <a href="{{ route('admin.meeting-rooms.index') }}"
                       class="p-2.5 rounded-xl border border-primary-300/80 bg-white/80 hover:bg-white text-primary-700 hover:text-primary-900 shadow-2xs hover:shadow-xs transition-all duration-200 group"
                       title="Back to Meeting Rooms">
                        <x-heroicon-o-arrow-left class="w-5 h-5 transition-transform group-hover:-translate-x-0.5" />
                    </a>
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-black text-primary-900 tracking-tight">
                            Edit Meeting Room
                        </h1>
                        <p class="text-xs sm:text-sm text-primary-600 font-medium mt-0.5">
                            Update room details, amenities description, and reservation availability.
                        </p>
                    </div>
                </div>

                <!-- Reusable Meeting Room Form Component -->
                <x-meeting-room-form :meeting-room="$meetingRoom" />

            </div>
        </main>
    </div>
</x-layout>
