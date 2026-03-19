@props([
    'excelUrl' => '#',
    'pdfUrl'   => '#',
    'label'    => 'Export',
])

{{--
    USAGE:
    <x-export-dropdown
        excel-url="{{ url('reports/users?export_type=excel') }}"
        pdf-url="{{ url('reports/users?export_type=pdf') }}" />

    Props:
      excel-url — string — URL for Excel download
      pdf-url   — string — URL for PDF download
      label     — string — button label (default: Export)
--}}

<div class="relative z-50" x-data="{ open: false }">
    <button class="flex items-center gap-2 px-4 py-2.5 rounded-full text-[13px] font-semibold transition-all hover:scale-[1.02]"
            :style="theme === 'perfectlum' ? 'background:rgba(0,0,0,0.04);border:1px solid rgba(0,0,0,0.08);color:#1f2937;' : 'background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.1);color:inherit;'"
            @click="open = !open"
            @click.away="open = false"
            :aria-expanded="open.toString()"
            type="button">
        <i data-lucide="download" class="w-4 h-4"></i>
        {{ $label }}
        <i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
    </button>
    <div x-show="open"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 translate-y-2 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-1 scale-95"
         class="absolute right-0 mt-2 min-w-[170px] rounded-2xl border p-2 shadow-2xl"
         :class="theme === 'perfectlum' ? 'bg-white border-black/10' : 'bg-[#161820] border-white/10'"
         style="display: none;">
        <a class="flex items-center gap-2 rounded-xl px-3 py-2 text-[13px] transition-colors"
           href="{{ $excelUrl }}"
           target="_blank"
           :class="theme === 'perfectlum' ? 'text-gray-600 hover:bg-black/5' : 'text-[#e2e1e6] hover:bg-white/5'">
            <i data-lucide="file-spreadsheet" style="width:14px;height:14px;color:#4ade80;"></i>
            Download Excel
        </a>
        <a class="flex items-center gap-2 rounded-xl px-3 py-2 text-[13px] transition-colors"
           href="{{ $pdfUrl }}"
           target="_blank"
           :class="theme === 'perfectlum' ? 'text-gray-600 hover:bg-black/5' : 'text-[#e2e1e6] hover:bg-white/5'">
            <i data-lucide="file-text" style="width:14px;height:14px;color:#f87171;"></i>
            Download PDF
        </a>
    </div>
</div>
