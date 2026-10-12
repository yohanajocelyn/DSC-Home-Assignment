@props([
    'id' => 'confirmModal',
    'title' => 'Confirm Action',
    'subtitle' => null,
    'message' => 'Are you sure you want to proceed?',
    'confirmText' => 'Confirm',
    'cancelText' => 'Cancel',
    'variant' => 'danger', // 'danger' | 'primary'
    'method' => 'POST',
    'action' => '',
])

@php
    $isDanger = $variant === 'danger';
    $iconBg = $isDanger
        ? 'bg-rose-100/70 text-rose-600 border border-rose-200/60'
        : 'bg-primary-100/70 text-secondary border border-primary-200/60';
    $confirmBtnClasses = $isDanger
        ? 'bg-rose-600 hover:bg-rose-700 text-white shadow-md hover:shadow-lg focus:ring-rose-500'
        : 'bg-primary hover:bg-secondary text-white shadow-md hover:shadow-lg focus:ring-secondary';
@endphp

<!-- Generic Confirmation Modal Component -->
<div id="{{ $id }}"
     class="confirm-modal fixed inset-0 z-50 hidden bg-black/45 backdrop-blur-xs flex items-center justify-center p-4 transition-opacity duration-200"
     role="dialog"
     aria-modal="true"
     aria-labelledby="{{ $id }}_title"
     aria-describedby="{{ $id }}_message"
     onclick="if (event.target === this) this.classList.add('hidden');">
    <div class="bg-white rounded-3xl shadow-2xl border border-primary-200 max-w-md w-full overflow-hidden transform transition-all duration-200">
        <!-- Modal Header -->
        <div class="p-6 pb-4 border-b border-primary-200/60 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl {{ $iconBg }} flex items-center justify-center shrink-0">
                    @if ($isDanger)
                        <x-heroicon-o-exclamation-triangle class="w-5 h-5" />
                    @else
                        <x-heroicon-o-information-circle class="w-5 h-5" />
                    @endif
                </div>
                <div>
                    <h3 id="{{ $id }}_title" class="text-lg font-black text-primary-900 tracking-tight modal-title">
                        {{ $title }}
                    </h3>
                    @if ($subtitle)
                        <p id="{{ $id }}_subtitle" class="text-xs text-primary-600 font-medium modal-subtitle">
                            {{ $subtitle }}
                        </p>
                    @endif
                </div>
            </div>
            <button type="button"
                    class="modal-close-trigger p-1.5 rounded-lg text-primary-400 hover:text-primary-800 hover:bg-primary-100/60 transition-colors cursor-pointer"
                    aria-label="Close modal"
                    onclick="const m = this.closest('.confirm-modal'); if (m) m.classList.add('hidden');">
                <x-heroicon-o-x-mark class="w-5 h-5" />
            </button>
        </div>

        <!-- Form Wrapping Modal Content & Actions -->
        <form id="{{ $id }}_form" method="POST" action="{{ $action }}" class="modal-form m-0">
            @csrf
            @if (strtoupper($method) !== 'POST')
                @method($method)
            @endif

            <!-- Modal Body -->
            <div class="p-6 space-y-3">
                <p id="{{ $id }}_message" class="text-sm text-primary-700 leading-relaxed modal-message">
                    {{ $message }}
                </p>
                {{ $slot }}
            </div>

            <!-- Modal Footer -->
            <div class="p-4 sm:p-5 border-t border-primary-200/60 bg-primary-50/30 flex items-center justify-end gap-3">
                <button type="button"
                        class="modal-cancel-trigger px-5 py-2.5 text-xs sm:text-sm font-semibold text-primary-700 bg-white hover:bg-primary-100/80 border border-primary-300 rounded-xl shadow-2xs transition-all cursor-pointer"
                        onclick="const m = this.closest('.confirm-modal'); if (m) m.classList.add('hidden');">
                    {{ $cancelText }}
                </button>
                <button type="submit"
                        id="{{ $id }}_confirmBtn"
                        class="modal-confirm-btn inline-flex items-center justify-center gap-2 px-6 py-2.5 {{ $confirmBtnClasses }} text-xs sm:text-sm font-bold rounded-xl transition-all transform hover:-translate-y-0.5 cursor-pointer">
                    <span>{{ $confirmText }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

@once
<script>
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.confirm-modal:not(.hidden)').forEach(function (modal) {
                modal.classList.add('hidden');
            });
        }
    });
</script>
@endonce
