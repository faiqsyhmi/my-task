@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'bg-[#0a0f1d] border-slate-800 text-slate-100 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm']) }}>
