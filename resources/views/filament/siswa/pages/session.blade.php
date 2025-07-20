<x-filament-panels::page>
    <div class="mb-4">
        <x-filament::link :href="route('filament.siswa.pages.my-courses.{slugMapel}', ['slugMapel' => $slugMapel])" color="info" icon="heroicon-o-arrow-left" icon-position="before"
            size="lg" class="inline-flex items-center">
            Kembali
        </x-filament::link>
    </div>
    
    <!-- Bagian Materi -->
    <div>
        <x-filament::section icon="heroicon-o-book-open" collapsible>
            <x-slot name="heading">
                Materi
            </x-slot>
            @if ($materi)
                <x-slot name="heading">
                    {{ $materi->judul }}
                </x-slot>
                <div class="dark:tetx-white">
                    {!! $materi->deskripsi !!}
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400">Materi tidak ditemukan</p>
            @endif

        </x-filament::section>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        <!-- Bagian File Materi -->
        <div class="col-span-1 md:col-span-1">
            <x-filament::section icon="heroicon-o-clipboard-document-list" collapsible collapsed>
                <x-slot name="heading">
                    File Materi
                </x-slot>
                @if ($fileMateri != null && $fileMateri->count() > 0)
                    @foreach ($fileMateri as $file)
                        <x-filament::badge icon="heroicon-o-document" class="mb-2" size="lg" color="gray">
                            <button wire:click="downloadFile('{{ $file->file }}')">{{ $file->nama }}
                            </button>
                        </x-filament::badge>
                    @endforeach
                @else
                    <p class="text-gray-500 dark:text-gray-400">Tidak ada file materi untuk sesi ini.</p>
                @endif
            </x-filament::section>
        </div>

        <!-- Tabel Tugas -->
        <div class="col-span-1 md:col-span-1">
            <x-filament::section icon="heroicon-o-document-check" collapsible collapsed>
                <x-slot name="heading">
                    Daftar Tugas
                </x-slot>
                @if ($tugas->count() > 0)
                    @foreach ($tugas as $t)
                        <div class="flex mb-2 items-center">
                            <p class="mr-2">{{ $t->judul }}</p>
                            <x-filament::icon-button wire:click="kumpulkanTugas('{{ $t->id }}')"
                                icon="heroicon-m-arrow-up-right" color="info" label="Kumpulkan" />
                        </div>
                    @endforeach
                @else
                    <p class="text-gray-500 dark:text-gray-400">Belum ada tugas yang di-upload.</p>
                @endif
            </x-filament::section>
        </div>

        <!-- Bagian Kuis -->
        <div class="col-span-1 md:col-span-1">
            <x-filament::section collapsible collapsed icon="heroicon-o-puzzle-piece">
                <x-slot name="heading">
                    Kuis
                </x-slot>

                @if ($kuis->count() > 0)
                    @foreach ($kuis as $k)
                        <x-filament::section class="mb-4" collapsible collapsed>
                            <x-slot name="heading">
                                {{ $k->judul }}
                            </x-slot>
                            <div class="prose max-w-none dark:prose-dark">
                                <p class="text-gray-900 dark:text-white">Deskripsi: {{ $k->deskripsi }}</p>
                                <p class="text-gray-900 dark:text-white">Intruksi Kuis</p>
                                <p class="text-gray-900 dark:text-white">Durasi: {{ $k->durasi }} menit</p>
                                <p class="text-gray-900 dark:text-white">Nilai Minimal: {{ $k->nilai_minimal }}</p>
                                <p class="text-gray-900 dark:text-white">Jumlah Soal: {{ $k->pertanyaans()->count() }}
                                    soal</p>
                                <p class="text-gray-900 dark:text-white">Waktu Mulai: {{ $k->waktu_mulai }}</p>
                                <p class="text-gray-900 dark:text-white">Waktu Selesai: {{ $k->waktu_selesai }}</p>
                                <p class="text-gray-900 dark:text-white">Petunjuk: Pastikan Anda mengerjakan semua soal
                                    dengan seksama. Waktu akan dimulai setelah Anda memulai kuis ini.</p>
                            </div>
                            <!-- Tombol untuk memulai kuis atau melihat hasil -->
                            @php
                                $hasilKuis = $k->hasilKuis->where('id_siswa', auth()->id())->first();
                            @endphp
                            @if ($hasilKuis)
                                @if ($hasilKuis->status == 'completed')
                                    <x-filament::button color="info" wire:click="lihatHasil('{{ $k->slug }}')"
                                        class="mt-3" icon="heroicon-m-arrow-top-right-on-square"
                                        icon-position="after">
                                        Lihat Hasil
                                    </x-filament::button>
                                @elseif ($hasilKuis->status == 'in_progress')
                                    @if (now('Asia/Jakarta')->lessThanOrEqualTo($k->waktu_selesai))
                                        <x-filament::button color="info"
                                            wire:click="lanjutkanKuis('{{ $k->slug }}')" class="mt-3">
                                            Lanjutkan Kuis
                                        </x-filament::button>
                                    @else
                                        <x-filament::button color="info"
                                            wire:click="lihatHasil('{{ $k->slug }}')" class="mt-3">
                                            Waktu Habis - Lihat Hasil
                                        </x-filament::button>
                                    @endif
                                @elseif ($hasilKuis->status == 'expired')
                                    <x-filament::button color="info" wire:click="lihatHasil('{{ $k->slug }}')"
                                        class="mt-3">
                                        Waktu Habis - Lihat Hasil
                                    </x-filament::button>
                                @endif
                            @else
                                @if (now('Asia/Jakarta')->lessThanOrEqualTo($k->waktu_selesai))
                                    <x-filament::button color="info" wire:click="startQuiz('{{ $k->slug }}')"
                                        class="mt-3">
                                        Mulai Kuis
                                    </x-filament::button>
                                @else
                                    <x-filament::button color="info" class="mt-3" disabled>
                                        Kuis Selesai
                                    </x-filament::button>
                                @endif
                            @endif
                        </x-filament::section>
                    @endforeach
                @else
                    <!-- Pesan Jika Tidak Ada Kuis -->
                    <p class="text-gray-500 dark:text-gray-400">Tidak ada kuis untuk sesi ini.</p>
                @endif
            </x-filament::section>
        </div>


    </div>
</x-filament-panels::page>
