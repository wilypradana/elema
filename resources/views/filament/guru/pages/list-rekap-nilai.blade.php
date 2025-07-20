<x-filament::page>
    <div class="space-y-4">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold mb-4">Rekap Nilai - {{ $guruMapel->mataPelajaran->nama }}</h2>
            <x-filament::link wire:click="exportNilaiSiswa"
            icon="heroicon-m-arrow-top-right-on-square"
            tag="button">
                Export
            </x-filament::link>
            
        </div>
        @if (!empty($siswaNilai))
            <div class="overflow-x-auto">
                <table class="table-auto w-full text-left border border-gray-200">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-gray-700">
                            <th class="px-4 py-2 text-black dark:text-white">Nama Siswa</th>
                            @foreach ($guruMapel->sesiBelajar as $sesi)
                                <th class="px-4 py-2 text-center text-black dark:text-white">
                                    Tugas ({{ $sesi->judul }})
                                </th>
                                <th class="px-4 py-2 text-center text-black dark:text-white">
                                    Kuis ({{ $sesi->judul }})
                                </th>
                            @endforeach
                        </tr>

                    </thead>
                    <tbody>
                        @foreach ($siswaNilai as $nilaiSiswa)
                            <tr>
                                <td class="border px-4 py-2">{{ $nilaiSiswa['nama_siswa'] }}</td>
                                @foreach ($nilaiSiswa['nilai_sesi'] as $nilai)
                                    <td class="border px-4 py-2 text-center">{{ $nilai['nilai_tugas'] }}</td>
                                    <td class="border px-4 py-2 text-center">{{ $nilai['nilai_kuis'] }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p>Tidak ada data nilai yang ditemukan.</p>
        @endif
    </div>
</x-filament::page>
