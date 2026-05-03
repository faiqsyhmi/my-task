<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 bg-[#161e2d] border border-slate-700 rounded-md font-semibold text-xs text-slate-300 uppercase tracking-widest shadow-sm hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-[#0a0f1d] disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
