<?php

namespace App\Imports;

use App\Models\siswa;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SiswasImport implements ToModel, WithHeadingRow 
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $id_kelas = self::getClassId($row['id_kelas']);

            return new Siswa([
                'nis' => $row['nis'],
                'name' => $row['name'],
                'email' => $row['email'],
                'password' => bcrypt($row['password']),
                'jenis_kelamin' => $row['jenis_kelamin'],
                'id_kelas' => $id_kelas,
            ]);
    }

    public static function getClassId($id)
    {
        $class = \App\Models\Kelas::where('id', $id)->first();
        return $class ? $class->id : null;
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 20;
    }
}
