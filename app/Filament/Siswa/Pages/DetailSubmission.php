<?php

namespace App\Filament\Siswa\Pages;

use App\Models\FilePengumpulanTugas;
use App\Models\PengumpulanTugas;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DetailSubmission extends Page implements HasTable
{
    use InteractsWithTable;
    use InteractsWithFormActions;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.siswa.pages.detail-submission';

    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'submission/{idTugas}/session/{slugSesi}/edit';


    public $slugSubmission;
    public $submission;
    public ?array $file = [];
    public $slugSession;


    public function mount()
    {
        $this->slugSubmission = request()->query('slugSubmission');
        $this->submission = PengumpulanTugas::where('slug', $this->slugSubmission)->first();
        $this->slugSession = session('slugSession');// Ambil slugSession dari session
    }

    public function getFormSchema(): array
    {
        return [
            Grid::make(1)
                ->schema([
                    FileUpload::make('file')
                        ->required()
                        ->multiple()
                        ->maxFiles(2048)
                        ->helperText('Ukuran maksimal file 2MB per file.')
                ])
                ,
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn() => FilePengumpulanTugas::where('pengumpulan_tugas_id', $this->submission->id))
            ->columns([
                TextColumn::make('nama_file')
                    ->url(fn($record) => Storage::url($record->file)) // Membuat link ke file
                    ->openUrlInNewTab(),
            ])
            ->actions([
                \Filament\Tables\Actions\DeleteAction::make()
                    ->action(function (FilePengumpulanTugas $record) {
                        Storage::disk('public')->delete($record->file); // Hapus file dari storage
                        $record->delete(); // Hapus record dari database
                        Notification::make()
                            ->title('File berhasil dihapus')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->action(function (\Illuminate\Support\Collection $records) {
                            foreach ($records as $record) {
                                Storage::disk('public')->delete($record->file);
                                $record->delete();
                            }
                        })
                        ->requiresConfirmation()
                        ->label('Hapus File'),
                ])
            ]);
    }

    public function save()
    {
        $this->validate([
            'file' => 'required',
        ]);

        $filePaths = []; // Array untuk menyimpan path file yang berhasil disimpan

        // Iterasi melalui array file dan simpan satu per satu
        foreach ($this->file as $file) {
            if ($file instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                // Pastikan file adalah objek TemporaryUploadedFile sebelum kita menyimpannya
                // Membuat nama acak untuk file
                $randomName = Str::uuid() . '.' . $file->getClientOriginalExtension();

                // Simpan file di disk 'public' dengan nama acak
                $filePath = $file->storeAs('filepengumpulantugas', $randomName, 'public');
                // Simpan path dan nama asli file ke dalam array
                $filePaths[] = [
                    'path' => $filePath,
                    'name' => $file->getClientOriginalName(),
                ];

                // Hapus file sementara setelah upload selesai
                if (file_exists($file->getRealPath())) {
                    unlink($file->getRealPath());
                }
            }
        }
        $this->submission->update();

        // Simpan data ke database untuk setiap file yang di-upload
        foreach ($filePaths as $fileData) {
            FilePengumpulanTugas::create([
                'pengumpulan_tugas_id' => $this->submission->id,
                'file' => $fileData['path'],  // Simpan path file (nama acak)
                'nama_file' => $fileData['name'], // Simpan nama asli file
            ]);
        }
        return redirect()->route('filament.siswa.pages.submission.{idTugas}.session.{slugSesi}.edit', [
            'idTugas' => $this->submission->tugas->id, // Pastikan ini mengambil idTugas dengan benar
            'slugSesi' => $this->slugSession, // Menggunakan slugSesi dari session
            'slugSubmission' => $this->slugSubmission // Menggunakan slugSubmission dari session
        ]);
    }

    public function backToSubmission()
    {
        return redirect()->route(
            'filament.siswa.pages.my-courses.session.{slug}',
            ['slug' => $this->slugSession]
        );

    }
}