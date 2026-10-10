@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-primary-300 focus:border-secondary focus:ring-secondary rounded-xl shadow-sm bg-white/80 backdrop-blur-sm px-4 py-3 text-primary-900 transition-all duration-300 hover:bg-white placeholder-primary-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-opacity-50']) }}>
