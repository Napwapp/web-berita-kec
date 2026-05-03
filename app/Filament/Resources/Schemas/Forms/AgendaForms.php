<?php 
namespace App\Filament\Resources\Schemas\Forms;

use App\Models\CategoryAgenda;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Support\Str;


class AgendaForms
{
    public static function schema(): array {
        return ([
            Group::make()
                    ->schema([
                        Section::make('Informasi Agenda')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Agenda')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $state, Set $set): void {
                                        $set('slug', Str::slug($state));
                                    })
                                    ->columnSpanFull(),

                                Textarea::make('description')
                                    ->label('Deskripsi (Opsional)')
                                    ->rows(5)
                                    ->nullable()
                                    ->columnSpanFull(),
                            ]),

                        Section::make('Waktu Pelaksanaan')
                            ->schema([
                                Toggle::make('is_all_day')
                                    ->label('Sepanjang Hari')
                                    ->helperText('Aktifkan jika agenda berlangsung sepanjang hari tanpa jam tertentu.')
                                    ->default(false)
                                    ->live()
                                    ->columnSpanFull(),

                                DateTimePicker::make('start_at')
                                    ->label('Tanggal & Jam Mulai')
                                    ->required()
                                    ->seconds(false)
                                    ->displayFormat(function (Get $get): string {
                                        return $get('is_all_day') ? 'd/m/Y' : 'd/m/Y H:i';
                                    }),

                                DateTimePicker::make('end_at')
                                    ->label('Tanggal & Jam Selesai')
                                    ->required()
                                    ->seconds(false)
                                    ->after('start_at')
                                    ->displayFormat(function (Get $get): string {
                                        return $get('is_all_day') ? 'd/m/Y' : 'd/m/Y H:i';
                                    }),
                            ])
                            ->columns(2),

                    ])
                    ->columnSpan(['lg' => 2]),


                Group::make()
                    ->schema([
                        Section::make('Publikasi')
                            ->schema([
                                Toggle::make('is_published')
                                    ->label('Terbitkan Agenda')
                                    ->helperText(function (Get $get): string {
                                        return $get('is_published')
                                            ? 'Agenda akan tampil ke publik.'
                                            : 'Jika tidak diterbitkan, agenda disimpan sebagai draft.';
                                    })
                                    ->default(false)
                                    ->live(),

                                // Hint readonly — tampil saat sudah pernah diterbitkan
                                Placeholder::make('published_at_info')
                                    ->label('Diterbitkan pada')
                                    ->content(
                                        fn($record) => $record?->published_at
                                        ? Carbon::parse($record->published_at)
                                            ->translatedFormat('d F Y, H:i') . ' WIB'
                                        : '—'
                                    )
                                    ->visible(fn($record) => filled($record?->published_at)),
                            ]),

                        // Kategori
                        Section::make('Kategori')
                            ->schema([
                                Select::make('category_id')
                                    ->label('Kategori Agenda (Opsional)')
                                    ->options(CategoryAgenda::pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->nullable()
                                    ->noSearchResultsMessage('Kategori tidak ditemukan.')

                                    // ── Modal inline: Tambah Kategori ───
                                    ->createOptionForm([
                                        TextInput::make('name')
                                            ->label('Nama Kategori')
                                            ->required()
                                            ->maxLength(100)
                                            ->live(onBlur: true)
                                        ->afterStateUpdated(function (string $state, Set $set): void {
                                            $set('slug', Str::slug($state));
                                        }),
                                        ColorPicker::make('color')
                                            ->label('Warna Kategori (Opsional)')
                                            ->nullable()
                                            ->helperText('Pilih warna yang diinginkan untuk membedakan warna pada kategori ini.'),
                                    ])

                                    ->createOptionAction(function (Action $action) {
                                        $action
                                            ->label('Tambah Kategori')
                                            ->icon('heroicon-m-plus')
                                            ->modalHeading('Buat Kategori Baru')
                                            ->modalDescription('Tambahkan kategori agenda baru. Slug akan terisi otomatis dari nama.')
                                            ->modalSubmitActionLabel('Simpan Kategori')
                                            ->modalWidth('md');
                                    })
                                    ->createOptionUsing(function (array $data): int {
                                        return CategoryAgenda::create([
                                            'name' => $data['name'],
                                            'slug' => $data['slug'] ?? Str::slug($data['name']),
                                            'color' => $data['color'] ?? null,
                                        ])->getKey();
                                    }),
                            ]),

                        // Lokasi & Tautan
                        Section::make('Lokasi & Tautan')
                            ->schema([
                                Toggle::make('is_online')
                                    ->label('Agenda Online')
                                    ->helperText('Aktifkan jika agenda dilaksanakan secara daring.')
                                    ->default(false)
                                    ->live(),

                                TextInput::make('online_link')
                                    ->label('Tautan Meeting / Webinar')
                                    ->url()
                                    ->nullable()
                                    ->placeholder('Contoh: https://meet.google.com/...')
                                    ->prefixIcon('heroicon-m-video-camera')
                                    ->visible(function (Get $get): bool {
                                        return (bool) $get('is_online');
                                    })
                                    ->required(function (Get $get): bool {
                                        return (bool) $get('is_online');
                                    })
                                    ->helperText('Masukkan link Zoom, Meet, atau platform daring lainnya.'),

                                TextInput::make('location')
                                    ->label('Lokasi / Tempat')
                                    ->nullable()
                                    ->placeholder('Contoh: Aula Kecamatan Binong')
                                    ->prefixIcon('heroicon-m-map-pin')
                                    ->visible(function (Get $get): bool {
                                        return !(bool) $get('is_online');
                                    })
                                    ->required(function (Get $get): bool {
                                        return !(bool) $get('is_online');
                                    })
                                    ->helperText('Masukkan alamat lengkap tempat pelaksanaan agenda.'),
                            ]),

                    ])
                    ->columnSpan(['lg' => 1]),

            ]);
    }
}
?>