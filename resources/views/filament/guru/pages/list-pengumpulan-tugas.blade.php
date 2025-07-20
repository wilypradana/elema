<x-filament::page>
        <div>
        <x-filament::link :href="$this->activeRelationManager" color="info">
            Kembali
        </x-filament::link>
    </div>
        {{ $this->table }}
</x-filament::page>