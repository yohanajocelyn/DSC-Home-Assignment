@php
    $meetingRoom = $meetingRoom ?? $room;
@endphp

<x-layout>
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>

    <style>
        /* Quill theme customizer to match website aesthetic */
        .ql-toolbar.ql-snow {
            border-color: #eadecc !important;
            border-top-left-radius: 0.75rem;
            border-top-right-radius: 0.75rem;
            background-color: #ffffff;
            padding: 0.625rem 0.75rem;
        }
        .ql-container.ql-snow {
            border-color: #eadecc !important;
            border-bottom-left-radius: 0.75rem;
            border-bottom-right-radius: 0.75rem;
            background-color: rgba(255, 255, 255, 0.85);
            min-height: 180px;
            font-family: inherit;
            font-size: 0.925rem;
            color: #3d2100;
        }
        .ql-snow .ql-stroke {
            stroke: #734837 !important;
        }
        .ql-snow .ql-fill {
            fill: #734837 !important;
        }
        .ql-snow.ql-toolbar button:hover .ql-stroke,
        .ql-snow .ql-toolbar button.ql-active .ql-stroke {
            stroke: #3d2100 !important;
        }
        .ql-snow.ql-toolbar button:hover .ql-fill,
        .ql-snow .ql-toolbar button.ql-active .ql-fill {
            fill: #3d2100 !important;
        }
        .ql-editor.ql-blank::before {
            color: #c9a484 !important;
            font-style: normal;
        }
    </style>

    <!-- Outer viewport container: fixed height with no global scroll -->
    <div class="h-screen flex flex-col md:flex-row overflow-hidden">
    
        <x-navbar active="meeting-rooms" class="h-full shrink-0 overflow-y-auto" />

        <main class="flex-1 h-full overflow-y-auto p-6 sm:p-8 md:p-10 lg:p-12">
            <div class="max-w-4xl">
                
                <!-- Back Link -->
                <div class="mb-4">
                    <a href="{{ route('admin.meeting-rooms.index') }}"
                       class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-secondary hover:text-primary-900 transition-colors">
                        <x-heroicon-o-arrow-left class="w-4 h-4" />
                        <span>Back</span>
                    </a>
                </div>

                <!-- Page Header -->
                <div class="mb-8">
                    <h1 class="text-2xl sm:text-3xl font-black text-primary-900 tracking-tight">
                        Meeting Room Form
                    </h1>
                    <p class="text-xs sm:text-sm text-primary-600 font-medium mt-1">
                        Fill in all room specifications.
                    </p>
                </div>

                <!-- Flash Notifications -->
                <x-flash-error class="mb-6" title="Please correct the following errors:" />

                <!-- Edit Form -->
                <form method="POST" action="{{ route('admin.meeting-rooms.update', $meetingRoom) }}" id="meetingRoomForm" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Grid: Room Name & Location -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Room Name -->
                        <div>
                            <x-input-label for="name">
                                <span>Room Name</span>
                                <span class="text-rose-500 ml-0.5">*</span>
                            </x-input-label>
                            <x-input id="name"
                                     type="text"
                                     name="name"
                                     value="{{ old('name', $meetingRoom->name) }}"
                                     required
                                     placeholder="e.g. Executive Boardroom Alpha"
                                     class="w-full mt-1.5" />
                            <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
                        </div>

                        <!-- Location -->
                        <div>
                            <x-input-label for="location">
                                <span>Location</span>
                                <span class="text-rose-500 ml-0.5">*</span>
                            </x-input-label>
                            <x-input id="location"
                                     type="text"
                                     name="location"
                                     value="{{ old('location', $meetingRoom->location) }}"
                                     required
                                     placeholder="e.g. Building A, 3rd Floor, Suite 302"
                                     class="w-full mt-1.5" />
                            <x-input-error :messages="$errors->get('location')" class="mt-1.5" />
                        </div>
                    </div>

                    <!-- Grid: Capacity & Status -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Capacity -->
                        <div>
                            <x-input-label for="capacity">
                                <span>Capacity (Persons)</span>
                                <span class="text-rose-500 ml-0.5">*</span>
                            </x-input-label>
                            <x-input id="capacity"
                                     type="number"
                                     name="capacity"
                                     min="1"
                                     value="{{ old('capacity', $meetingRoom->capacity) }}"
                                     required
                                     placeholder="e.g. 16"
                                     class="w-full mt-1.5" />
                            <x-input-error :messages="$errors->get('capacity')" class="mt-1.5" />
                        </div>

                        <!-- Active Status Selection -->
                        <div>
                            <x-input-label for="is_active">
                                <span>Active Status</span>
                                <span class="text-rose-500 ml-0.5">*</span>
                            </x-input-label>
                            
                            <div class="grid grid-cols-2 gap-3 mt-3.5">
                                <!-- Option: Active -->
                                <label class="relative flex items-center justify-between p-3 min-h-[48px] rounded-xl border border-primary-300/80 bg-white/70 cursor-pointer transition-all hover:bg-white has-checked:border-secondary has-checked:bg-primary-100/50 has-checked:ring-2 has-checked:ring-secondary/40">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>
                                        <span class="text-sm font-semibold text-primary-900">Active</span>
                                    </div>
                                    <input type="radio"
                                           name="is_active"
                                           value="1"
                                           required
                                           {{ old('is_active', (string)(int)$meetingRoom->is_active) === '1' ? 'checked' : '' }}
                                           class="text-secondary focus:ring-secondary focus:ring-offset-0 border-primary-300 w-4 h-4 cursor-pointer" />
                                </label>

                                <!-- Option: Inactive -->
                                <label class="relative flex items-center justify-between p-3 min-h-[48px] rounded-xl border border-primary-300/80 bg-white/70 cursor-pointer transition-all hover:bg-white has-checked:border-secondary has-checked:bg-primary-100/50 has-checked:ring-2 has-checked:ring-secondary/40">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-block w-2.5 h-2.5 rounded-full bg-rose-500 shrink-0"></span>
                                        <span class="text-sm font-semibold text-primary-900">Inactive</span>
                                    </div>
                                    <input type="radio"
                                           name="is_active"
                                           value="0"
                                           required
                                           {{ old('is_active', (string)(int)$meetingRoom->is_active) === '0' ? 'checked' : '' }}
                                           class="text-secondary focus:ring-secondary focus:ring-offset-0 border-primary-300 w-4 h-4 cursor-pointer" />
                                </label>
                            </div>
                            <x-input-error :messages="$errors->get('is_active')" class="mt-1.5" />
                        </div>
                    </div>

                    <!-- Room Description: Rich Text Editor -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <x-input-label for="description_editor">
                                <span>Room Description & Amenities</span>
                                <span class="text-rose-500 ml-0.5">*</span>
                            </x-input-label>
                        </div>

                        <!-- Quill Editor Container -->
                        <div id="editor" class="shadow-2xs"></div>

                        <!-- Hidden Textarea bound to Form for Submission -->
                        <textarea id="description" name="description" class="hidden" required>{{ old('description', $meetingRoom->description) }}</textarea>

                        <!-- Client-side error if empty on submit -->
                        <p id="editorError" class="hidden text-xs text-rose-600 font-medium mt-1.5">
                            Please provide a description of the meeting room.
                        </p>
                        <x-input-error :messages="$errors->get('description')" class="mt-1.5" />
                    </div>

                    <!-- Action Buttons: Cancel and Submit (Right Aligned) -->
                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-primary-200/60">
                        <!-- Cancel Button -->
                        <a href="{{ route('admin.meeting-rooms.index') }}"
                           class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-semibold text-primary-700 bg-white hover:bg-primary-100/80 border border-primary-300 rounded-xl shadow-2xs hover:shadow-xs transition-all duration-200">
                            Cancel
                        </a>

                        <!-- Submit Button -->
                        <button type="submit"
                                class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-primary text-white text-sm font-bold rounded-xl shadow-md hover:bg-secondary hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5 cursor-pointer">
                            <span>Save</span>
                        </button>
                    </div>
                </form>

            </div>
        </main>
    </div>

    <!-- Script to Initialize and Sync Quill Rich Text Editor -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize Quill
            const quill = new Quill('#editor', {
                theme: 'snow',
                placeholder: 'Describe room equipment, facilities (e.g. 4K display, video conferencing, whiteboard, soundproofing)...',
                modules: {
                    toolbar: [
                        [{ 'header': [2, 3, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        ['blockquote'],
                        ['clean']
                    ]
                }
            });

            const hiddenTextarea = document.getElementById('description');
            const editorError = document.getElementById('editorError');
            const form = document.getElementById('meetingRoomForm');

            // Pre-fill existing content if old() or meeting room description was provided
            if (hiddenTextarea.value.trim().length > 0) {
                quill.root.innerHTML = hiddenTextarea.value;
            }

            // Sync editor content on text change
            quill.on('text-change', function () {
                const text = quill.getText().trim();
                if (text.length > 0) {
                    hiddenTextarea.value = quill.root.innerHTML;
                    if (editorError) editorError.classList.add('hidden');
                } else {
                    hiddenTextarea.value = '';
                }
            });

            // Form Submit validation check for rich text
            if (form) {
                form.addEventListener('submit', function (e) {
                    const text = quill.getText().trim();
                    if (!text || text.length === 0) {
                        e.preventDefault();
                        if (editorError) editorError.classList.remove('hidden');
                        quill.focus();
                        return false;
                    }
                    hiddenTextarea.value = quill.root.innerHTML;
                });
            }
        });
    </script>
</x-layout>
