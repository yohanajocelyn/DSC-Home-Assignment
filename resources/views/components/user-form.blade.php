@props([
    'user' => null,
    'roles' => null,
    'action' => null,
    'method' => null,
    'submitText' => null,
    'cancelUrl' => null,
])

@php
    $isEdit = !empty($user);
    $formAction = $action ?? ($isEdit ? route('admin.users.update', $user) : route('admin.users.store'));
    $formMethod = $method ?? ($isEdit ? 'PUT' : 'POST');
    $btnText = $submitText ?? ($isEdit ? 'Save' : 'Create');
    $backUrl = $cancelUrl ?? route('admin.users.index');
    $selectedRoleId = (string) old('role_id', $user?->role_id ?? '');
    $allRoles = $roles ?? \App\Models\Role::orderBy('name')->get();
@endphp

<form method="POST"
      action="{{ $formAction }}"
      class="space-y-6 p-6 sm:p-8">
    @csrf
    @if (strtoupper($formMethod) !== 'POST')
        @method($formMethod)
    @endif

    <div>
        <x-input-label for="name">
            <span>Name</span>
            <span class="text-rose-500 ml-0.5">*</span>
        </x-input-label>
        <x-input id="name"
                 type="text"
                 name="name"
                 value="{{ old('name', $user?->name) }}"
                 required
                 autofocus
                 placeholder="e.g. John Doe"
                 class="w-full mt-1.5" />
        <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
    </div>

    <div>
        <x-input-label for="email">
            <span>Email</span>
            <span class="text-rose-500 ml-0.5">*</span>
        </x-input-label>
        <x-input id="email"
                 type="email"
                 name="email"
                 value="{{ old('email', $user?->email) }}"
                 required
                 placeholder="e.g. john@example.com"
                 class="w-full mt-1.5" />
        <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <x-input-label for="role_id">
                <span>Role</span>
                <span class="text-rose-500 ml-0.5">*</span>
            </x-input-label>
            @if ($isEdit)
                <div class="relative mt-1.5">
                    <input type="text"
                           value="{{ $user?->role?->name ?? 'Staff' }}"
                           readonly
                           disabled
                           class="w-full pl-3.5 pr-4 py-2.5 rounded-xl border border-primary-200/80 bg-primary-100/50 text-primary-700 text-sm font-semibold cursor-not-allowed select-none shadow-2xs focus:outline-none" />
                    <input type="hidden" name="role_id" value="{{ $user->role_id }}" />
                </div>
            @else
                <div class="relative mt-1.5">
                    <select id="role_id"
                            name="role_id"
                            required
                            class="w-full pl-3.5 pr-8 py-2.5 rounded-xl border border-primary-300/80 bg-white/80 text-primary-900 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-secondary focus:bg-white shadow-2xs appearance-none transition-all cursor-pointer">
                        <option value="" disabled {{ $selectedRoleId === '' ? 'selected' : '' }}>Select a role</option>
                        @foreach ($allRoles as $role)
                            <option value="{{ $role->id }}" {{ $selectedRoleId == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-primary-500">
                        <x-heroicon-o-chevron-down class="w-4 h-4" />
                    </div>
                </div>
            @endif
            <x-input-error :messages="$errors->get('role_id')" class="mt-1.5" />
        </div>

        <div>
            <x-input-label for="password">
                <span>Password</span>
                @if (!$isEdit)
                    <span class="text-rose-500 ml-0.5">*</span>
                @else
                    <span class="text-xs text-primary-500 font-normal ml-1">(Leave blank to keep current)</span>
                @endif
            </x-input-label>
            @if ($isEdit)
                <x-input id="password"
                         type="password"
                         name="password"
                         placeholder="••••••••"
                         autocomplete="new-password"
                         class="w-full mt-1.5" />
            @else
                <x-input id="password"
                         type="password"
                         name="password"
                         required
                         placeholder="Minimum 6 characters"
                         autocomplete="new-password"
                         class="w-full mt-1.5" />
            @endif
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>
    </div>

    <div class="flex items-center justify-end gap-3 pt-6 border-t border-primary-200/60">
        <a href="{{ $backUrl }}"
           class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-semibold text-primary-700 bg-white hover:bg-primary-100/80 border border-primary-300 rounded-xl shadow-2xs hover:shadow-xs transition-all duration-200">
            Cancel
        </a>

        <button type="submit"
                class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-primary text-white text-sm font-bold rounded-xl shadow-md hover:bg-secondary hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5 cursor-pointer">
            <span>{{ $btnText }}</span>
        </button>
    </div>
</form>
