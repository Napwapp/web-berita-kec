<?php

// Schema form tambah berita dan kategorinya

namespace App\Filament\Resources\Schemas;

use Filament\Forms\Components\Group;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use App\Models\Category;
use Illuminate\Support\Str;


class NewsForm
{
    public static function schema(): array
    {
        return ([
            Group::make()
                ->schema([
                    // Informasi Berita
                    Section::make('Informasi Berita')
                        ->schema([
                            TextInput::make('title')
                                ->label('Judul')
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                    if ($operation === 'edit')
                                        return;
                                    $set('slug', Str::slug($state));
                                }),

                            TextInput::make('subtitle')
                                ->label('Sub Judul (Opsional)')
                                ->nullable()
                                ->maxLength(255),
                        ]),

                    Section::make('Konten')
                        ->schema([
                            RichEditor::make('content')
                                ->label('Isi Berita')
                                ->required()
                                ->fileAttachmentsDisk('public')
                                ->fileAttachmentsDirectory('news/attachments')
                                ->columnSpanFull(),

                            Textarea::make('excerpt')
                                ->label('Ringkasan Berita (Opsional)')
                                ->nullable()
                                ->rows(3)
                                ->maxLength(500)
                                ->helperText('Ringkasan singkat berita (opsional).'),
                        ]),
                ])
                ->columnSpan(['lg' => 2]),

            // Kolom kanan (sidebar)
            Group::make()
                ->schema([
                    // Publish atau draft
                    Section::make('Publikasi')
                        ->schema([
                            Toggle::make('is_published')
                                ->label('Publikasikan')
                                ->helperText('Aktifkan untuk mempublikasikan berita, nonaktifkan untuk menyimpan sebagai draft.')
                                ->default(false),
                        ]),

                    // Kategori berita
                    Section::make('Kategori')
                        ->schema([
                            Select::make('categories')
                                ->label('Kategori Berita')
                                ->required()
                                ->options(Category::pluck('name', 'id'))
                                ->multiple()
                                ->searchable()
                                ->preload()
                                ->noSearchResultsMessage('Kategori tidak ditemukan')

                                // Modal tambah kategori
                                ->createOptionForm([
                                    TextInput::make('name')
                                        ->label('Nama Kategori')
                                        ->required()
                                        ->maxLength(100)
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(function ($state, Forms\Set $set) {
                                            $set('slug', Str::slug($state));
                                        }),
                                ])

                                ->createOptionAction(function (Forms\Components\Actions\Action $action) {
                                    $action
                                        ->label('Tambah Kategori')
                                        ->modalHeading('Buat Kategori Baru')
                                        ->modalSubmitActionLabel('Simpan')
                                        ->modalWidth('md');
                                })

                                ->createOptionUsing(function (array $data): int{
                                    return Category::create([
                                        'name' => $data['name'],
                                        'slug' => $data['slug'] ?? Str::slug($data['name']),
                                    ])->getKey();
                                }),
                        ]),

                    Section::make('Thumbnail')
                        ->schema([
                            FileUpload::make('thumbnail')
                                ->label('Gambar Thumbnail')
                                ->image()
                                ->required()
                                ->disk('local')
                                ->directory('temp-uploads/news-thumbnails')
                                ->visibility('public')
                                ->imageResizeMode('cover')
                                ->imageCropAspectRatio('16:9')
                                ->imageResizeTargetWidth(1280)
                                ->imageResizeTargetHeight(720)
                                ->getUploadedFileNameForStorageUsing(function ($file) {
                                    $original = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                                    return 'news-' . now()->timestamp . '-' . str($original)->slug();
                                }),

                            TextInput::make('thumbnail_description')
                                ->label('Deskripsi Thumbnail (Opsional)')
                                ->nullable()
                                ->maxLength(2000)
                                ->helperText('Caption atau deskripsi gambar (opsional).'),
                        ]),
                ])
                ->columnSpan(['lg' => 1]),
        ]);
    }
}

?>