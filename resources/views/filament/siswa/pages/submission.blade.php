<x-filament-panels::page>
    <div>
        <x-filament::link wire:click="backToSession" tag="button">
            Kembali
        </x-filament::link>
    </div>
    @if ($this->tugas != null)
        <x-filament::section>
            <x-slot name="heading">
                Info Tugas
            </x-slot>
            <table>
                <tr>
                    <td>Nama Tugas</td>
                    <td>:</td>
                    <td>{{ $tugas->judul }}</td>
                </tr>
                @if ($tugas->deskripsi != null)
                    <tr>
                        <td>Deskripsi</td>
                        <td>:</td>
                        <td>{!! $tugas->deskripsi !!}</td>
                    </tr>
                @endif
                @if ($tugas->deadline)
                    <tr>
                    <td>Deadline</td>
                    <td>:</td>
                    <td>
                        <x-filament::badge>
                            {{ $tugas->deadline }}
                        </x-filament::badge>

                    </td>
                </tr>
                @endif
            </table>
        </x-filament::section>

        @if ($this->pengumpulanTugas == null)
            <x-filament::section>
                <x-slot name="heading">
                    Upload Tugas
                </x-slot>
                {{ $this->form }}
                <x-filament::button color="primary" wire:click="save" class="mt-6">
                    Simpan
                </x-filament::button>
            </x-filament::section>
        @else
            <x-filament::section>
                <x-slot name="heading">
                    Pengumpulan Tugas
                    <div class="float-left mt-2">
                        <x-filament::button color="warning" wire:click="edit('{{ $this->pengumpulanTugas->slug }}')">
                            Edit
                        </x-filament::button>
                        <x-filament::modal id="delete-confirmation-modal">
                            <x-slot name="trigger">
                                <x-filament::button color="danger">
                                    Hapus
                                </x-filament::button>
                            </x-slot>

                            <p>Apakah kamu yakin ingin menghapus pengumpulan tugas ini?</p>

                            <!-- Button to confirm task deletion -->
                            <x-filament::button wire:click="deleteSubmission" color="danger">
                                Ya, hapus tugas
                            </x-filament::button>

                            <!-- Button to cancel deletion and close the modal -->
                            <x-filament::button color="success"
                                x-on:click="$dispatch('close-modal', { id: 'delete-confirmation-modal' })">
                                Tidak, batalkan
                            </x-filament::button>
                        </x-filament::modal>
                    </div>
                </x-slot>
                <table>
                    @php
                        $deadline = \Carbon\Carbon::parse($tugas->deadline);
                        $createdAt = \Carbon\Carbon::parse($this->pengumpulanTugas->created_at);
                        $remainingTime = $deadline->diffForHumans($createdAt, true); // Menghitung sisa waktu
                        $isLate = $createdAt > $deadline; // Mengecek jika terlambat
                        $lateDuration = $createdAt->diff($deadline); // Durasi keterlambatan
                    @endphp
                    <tr>
                        <td>Status Pengumpulan </td>
                        <td> : </td>
                        <td>
                            @if ($isLate)
                                <span class="text-red-500 font-bold">
                                    <x-filament::badge color="danger">
                                        Terlambat
                                    </x-filament::badge>
                                </span>
                            @else
                                <x-filament::badge color="success">
                                    Tepat Waktu
                                </x-filament::badge>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Waktu Pengumpulan </td>
                        <td> : </td>
                        <td>{{ $createdAt->translatedFormat('l, d F Y H:i:s') }}</td>
                    </tr>
                    </td>
                    </tr>
                    <tr>
                        <td>Sisa Waktu </td>
                        <td> : </td>
                        <td>

                            @if ($isLate)
                                <span class="text-red-500 font-bold">
                                    Terlambat {{ $lateDuration->format('%d hari %h jam %i menit') }}
                                </span>
                            @else
                                {{ $remainingTime }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="border px-4 py-2 text-center">File </td>
                        <td colspan="2" class="border px-4 py-2 text-center">Nilai </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="border px-4 py-2">
                            @if ($this->pengumpulanTugas->filePengumpulanTugas->count() < 1)
                                Tidak ada file yang diunggah
                            @elseif ($this->pengumpulanTugas->filePengumpulanTugas->count() < 2)
                                @php
                                    $filePengumpulan = $this->pengumpulanTugas->filePengumpulanTugas->first();
                                @endphp
                                <a href="{{ Storage::url($filePengumpulan->file) }}"
                                    class="text-blue-500 hover:underline" target="_blank">
                                    {{ $filePengumpulan->nama_file }}
                                </a>
                            @elseif ($this->pengumpulanTugas->filePengumpulanTugas->count() > 1)
                                <ul>
                                    @foreach ($this->pengumpulanTugas->filePengumpulanTugas as $file)
                                        <li>
                                            - <a href="{{ Storage::url($file->file) }}"
                                                class="text-blue-500 hover:underline" target="_blank">
                                                {{ $file->nama_file }}
                                            </a>
                                            <br>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </td>
                        <td colspan="2" class="border px-4 py-2">
                            <span class="font-bold">
                                {{ $this->pengumpulanTugas->nilai }}
                            </span>
                        </td>
                    </tr>
                </table>
            </x-filament::section>
        @endif
    @else
        @php
            return redirect()->route('filament.siswa.pages.my-courses.session.{slug}', [
                'slug' => $this->slugSession,
            ]);
        @endphp
    @endif

</x-filament-panels::page>
