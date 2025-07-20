<x-filament-panels::page>
    <div>
        <x-filament::link :href="route('filament.siswa.pages.dashboard')" color="info" icon="heroicon-o-arrow-left" icon-position="before"
            size="lg" class="inline-flex items-center">
            Kembali
        </x-filament::link>
    </div>
    @if ($sesiBelajars->count() > 0)
        @foreach ($sesiBelajars as $sesiBelajar)
            <x-filament::section icon="heroicon-o-clipboard-document-list" icon-color="info">
                <x-slot name="heading">
                    {{ $sesiBelajar['judul'] }}
                </x-slot>
                {{-- Content --}}
                <x-filament::button color="primary" wire:click="sesiBelajar('{{ $sesiBelajar['slug'] }}')"
                    class="ml-auto">
                    Lihat Sesi
                </x-filament::button>

            </x-filament::section>
        @endforeach
    @else
        <x-filament::section icon="heroicon-o-information-circle" icon-color="info" collapsible collapsed> 
            <x-slot name="heading">
                Guru belum membuat sesi belajar untuk mata pelajaran ini.
            </x-slot>
        </x-filament::section>
    @endif


</x-filament-panels::page>
