<!-- filepath: resources/views/filament/guru/pages/view-quiz-result.blade.php -->
<x-filament::page>
    <div>
        <x-filament::link wire:click="backToSession" tag="button">
            Kembali
        </x-filament::link>
    </div>
    <x-filament::section collapsible>
        <x-slot name="heading">
            Daftar Siswa yang menyelesaikan kuis: {{ $kuis->judul }}
        </x-slot>

        {{ $this->table }}
    </x-filament::section>
</x-filament::page>
