<x-filament-panels::page>
    <x-filament::section collapsible>
        <x-slot name="heading">
            {{ $mataPelajaran }}
        </x-slot>
        <x-slot name="headerEnd">
          
        </x-slot>
        @foreach ($kelas as $k)
            <div class="mb-2">
                <a href="{{ route('filament.guru.pages.list-rekap-nilai.{slug}', ['slug' => $slugGuruMapel, 'kelas' => $k['id_kelas']]) }}"
                    target="_blank" class="filament-link">

                    <button type="button" class="text-sm text-gray-600 inline-flex items-center space-x-2">
                        <svg class="heroicon-m-window h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor" aria-hidden="true">
                            <path
                                d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h12a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6z" />
                        </svg>
                        <span>Rekap Nilai - {{ $k['nama_kelas'] }}</span>
                    </button>
                </a>
            </div>
        @endforeach
    </x-filament::section>

    <x-filament::modal id="tambah-sesi-modal">
        <x-slot name="trigger">
            <x-filament::button>
                + Tambah Sesi
            </x-filament::button>
        </x-slot>

        <x-slot name="heading">
            Tambah Sesi Pelajaran
        </x-slot>

        {{ $this->form }}

        <x-filament::button color="primary" wire:click="save" class="mt-6">
            Simpan
        </x-filament::button>
    </x-filament::modal>
    <x-slot name="heading">
        Daftar Sesi Belajar
    </x-slot>

    <div class="flex flex-wrap gap-4">
        @foreach ($sesiBelajar as $sesi)
            <x-filament::section icon="heroicon-o-clipboard-document-list" icon-color="info">
                <x-slot name="heading">
                    {{ $sesi['judul'] }}
                </x-slot>
                {{-- Content --}}
                <x-filament::link color="primary" icon="heroicon-m-eye" :href="route('filament.guru.resources.sesi-belajars.edit', $sesi['slug'])">
                    Detail Sesi
                </x-filament::link>

                <x-filament::modal id="hapus-sesi-modal">
                    <x-slot name="trigger">
                        <x-filament::link color="danger" icon="heroicon-m-trash">
                            Hapus
                        </x-filament::link>
                    </x-slot>

                    <x-slot name="heading">
                        Hapus Sesi Pelajaran
                    </x-slot>
                    Apakah yakin ingin menghapus sesi {{ $sesi['judul'] }}?<br>

                    <x-filament::button color="danger" wire:click="deleteSesiBelajar('{{ $sesi['id'] }}')"
                        class="mt-6">
                        Ya
                    </x-filament::button>
                </x-filament::modal>
            </x-filament::section>
        @endforeach
    </div>
</x-filament-panels::page>
