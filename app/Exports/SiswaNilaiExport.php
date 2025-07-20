<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SiswaNilaiExport implements FromArray, WithHeadings, WithStyles
{
    protected $siswaNilai;

    public function __construct(array $siswaNilai)
    {
        $this->siswaNilai = $siswaNilai;
    }

    public function array(): array
    {
        $exportData = [];
    
        // Loop through each student
        foreach ($this->siswaNilai as $siswa) {
            $row = [
                'nama_siswa' => $siswa['nama_siswa']
            ];
    
            foreach ($siswa['nilai_sesi'] as $nilaiSesi) {
                // Replace 0 with "0" to make sure it appears in Excel
                $row['nilai_tugas_' . $nilaiSesi['sesi']] = $nilaiSesi['nilai_tugas'] === 0 ? '0' : $nilaiSesi['nilai_tugas'];
                $row['nilai_kuis_' . $nilaiSesi['sesi']] = $nilaiSesi['nilai_kuis'] === 0 ? '0' : $nilaiSesi['nilai_kuis'];
            }
    
            $exportData[] = $row;
        }
        return $exportData;
    }
    

    public function headings(): array
    {
        $headings = [];
    
        // First row: "Nama Siswa" and then each session spanning two columns
        $headings[] = array_merge(
            ['Nama Siswa'], // "Nama Siswa" will span A1 and A2
            ...array_map(function ($nilaiSesi) {
                return [$nilaiSesi['sesi'], null]; // Each session spans two columns
            }, $this->siswaNilai[0]['nilai_sesi'] ?? [])
        );
    
        // Second row: empty for 'Nama Siswa' column, and then 'Nilai Tugas' and 'Nilai Kuis' for each session
        $headings[] = array_merge(
            [''], // Leave blank for the first merged "Nama Siswa" cell
            ...array_map(function () {
                return ['Tugas', 'Kuis'];
            }, $this->siswaNilai[0]['nilai_sesi'] ?? [])
        );
    
        return $headings;
    }
    

    public function styles(Worksheet $sheet)
    {
        // Merge cells for "Nama Siswa" in the first column (A1:A2)
        $sheet->mergeCells('A1:A2');
    
        // Loop through and merge cells for each session
        $colIndex = 2; // Start from B1
        foreach ($this->siswaNilai[0]['nilai_sesi'] as $index => $nilaiSesi) {
            // Merge session titles
            $startCol = chr(65 + $colIndex - 1); // Convert number to column (B, C, D, etc.)
            $endCol = chr(65 + $colIndex); // Next column (for merging)
            $sheet->mergeCells("{$startCol}1:{$endCol}1");
    
            // Increment column index by 2 for the next session
            $colIndex += 2;
        }
    
        return [
            // Set header style (bold)
            1 => ['font' => ['bold' => true]], // First row
            2 => ['font' => ['bold' => true]], // Second row (subheadings)
        ];
    }
    
}

