<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">
            Tambah Guru Mata Pelajaran
        </x-slot>

        <x-slot name="description">
            Pilih guru dan mata pelajaran yang akan diajarkan
        </x-slot>

        {{ $this->form }}

        <x-filament::button color="primary" wire:click="save" class="mt-6">
            Simpan
        </x-filament::button>
    </x-filament::section>

    <x-filament::section class="mt-6">
        <x-slot name="heading">
            Daftar Guru dan Mata Pelajaran
        </x-slot>

        {{ $this->table }}
    </x-filament::section>
</x-filament-panels::page>
