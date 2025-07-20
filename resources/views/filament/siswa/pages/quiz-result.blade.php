<!-- filepath: resources/views/filament/siswa/pages/quiz-result.blade.php -->
<x-filament::page>
    <div class="container mx-auto p-4">
        <!-- Menampilkan Pesan Kesalahan -->
        @if (session('error'))
            <div class="bg-red-500 text-white p-4 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <!-- Menampilkan Pesan Status -->
        @if (session('status'))
            <div class="bg-green-500 text-white p-4 rounded mb-4">
                {{ session('status') }}
            </div>
        @endif
        <x-filament::button wire:click="backToSession"
            class="bg-blue-500 dark:bg-blue-400 text-white dark:text-gray-900 mb-2">Kembali</x-filament::button>
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 mb-6">
            <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">Hasil Kuis:
                {{ $hasilKuis->kuis->judul }}</h2>
            <p class="text-lg mb-4 text-gray-900 dark:text-gray-100">Skor Anda: <span
                    class="font-semibold">{{ $hasilKuis->skor }}</span> dari <span
                    class="font-semibold">{{ $hasilKuis->kuis->pertanyaans->sum('bobot') }}</span> poin</p>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 mt-2">
            <h3 class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100">Jawaban Anda:</h3>
            <ul class="space-y-4">
                @foreach ($hasilKuis->kuis->pertanyaans as $pertanyaan)
                    <li class="p-4 rounded-lg shadow-sm bg-gray-100 dark:bg-gray-900">
                        <span>Soal: {!! $pertanyaan->pertanyaan !!}</span>
                        @php
                            $jawabanSiswa = $hasilKuis->jawabanSiswa->where('id_pertanyaan', $pertanyaan->id)->first();
                        @endphp
                        @if ($jawabanSiswa)
                            <p class="mt-2 text-gray-900 dark:text-gray-100">Jawaban Anda: <span
                                    class="font-semibold">{{ $jawabanSiswa->jawaban->jawaban }}</span></p>
                            @if ($jawabanSiswa->jawaban->jawaban_benar)
                                <span class="text-green-600 ">Status Jawaban : Benar</span>
                            @else
                                <span class="text-red-600 ">Status Jawaban : Salah</span>
                            @endif
                        @else
                            <p class="mt-2 text-gray-900 dark:text-gray-100">Anda tidak menjawab pertanyaan ini.</p>
                        @endif
                        <div class="mt-2">
                            <span>Pilihan Jawaban</span>
                            <ul class="list-disc list-inside">
                                @foreach ($pertanyaan->jawabans as $jawaban)
                                    <li
                                        class="text-gray-900 dark:text-gray-100 @if ($jawaban->jawaban_benar) font-bold @endif">
                                        {{ $jawaban->jawaban }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</x-filament::page>
