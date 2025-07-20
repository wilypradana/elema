<?php

namespace App\Imports;

use App\Models\Jawaban;
use App\Models\Pertanyaan;
use DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class PertanyaansImport implements ToModel, WithChunkReading, WithBatchInserts
{
    private $idKuis;
    public function __construct($idKuis)
    {
        $this->idKuis = $idKuis;
    }
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        DB::beginTransaction();
        try {
            // Buat pertanyaan baru
            $pertanyaan = Pertanyaan::create([
                'id_kuis' => $this->idKuis,
                'pertanyaan' => $row[0] ?? null,
                'bobot' => $row[1] ?? 0,
            ]);

            // Simpan jawaban dan status benar
            $jawabanDanStatus = [];
            for ($i = 2; $i < count($row); $i += 2) {
                $jawaban = $row[$i] ?? null;
                $statusBenar = isset($row[$i + 1]) ? (int)$row[$i + 1] : 0;

                if ($jawaban === null || $jawaban === '') {
                    continue;
                }

                $jawabanDanStatus[] = [
                    'jawaban' => $jawaban,
                    'jawaban_benar' => $statusBenar,
                ];
            }
            // Simpan jawaban ke dalam database
            foreach ($jawabanDanStatus as $item) {
                Jawaban::create([
                    'id_pertanyaan' => $pertanyaan->id,
                    'jawaban' => $item['jawaban'],
                    'jawaban_benar' => $item['jawaban_benar'],
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('Error importing data: ' . $e->getMessage());
        }
    }

    public function chunkSize(): int
    {
        return 10;
    }

    public function batchSize(): int
    {
        return 10;
    }
}
