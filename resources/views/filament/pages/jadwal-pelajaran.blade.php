<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">
            Tambah Jadwal Pelajaran
        </x-slot>

        <x-slot name="description">
            Pilih tahun pelajaran, kelas, hari, dan mata pelajaran
        </x-slot>

        {{ $this->form }}

        <x-filament::button color="primary" wire:click="save" class="mt-6">
            Simpan
        </x-filament::button>
    </x-filament::section>

    <x-filament::section class="mt-6">
        <x-slot name="heading">
            Daftar Jadwal Pelajaran Tahun {{ $tahunAktif->nama ?? '' }}
        </x-slot>

        {{ $this->table }}
    </x-filament::section>
</x-filament-panels::page>