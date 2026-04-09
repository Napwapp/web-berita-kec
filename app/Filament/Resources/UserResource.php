<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Kelola Pengguna';
    protected static ?string $modelLabel = 'User';
    protected static ?string $pluralModelLabel = 'Users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->maxLength(255)
                    ->validationMessages([
                        'required' => 'Nama tidak boleh kosong',
                        'max' => 'Nama tidak boleh lebih dari 255 karakter',
                    ]),

                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->validationMessages([
                        'required' => 'Email tidak boleh kosong',
                        'email' => 'Format email tidak valid. Harus berformat email seperti example@domain.com',
                        'max' => 'Email tidak boleh lebih dari 255 karakter',
                        'unique' => 'Email sudah terdaftar. Silakan gunakan email lain',
                    ]),

                Forms\Components\TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->revealable()
                    ->dehydrateStateUsing(fn($state) => Hash::make($state))
                    ->dehydrated(fn($state) => filled($state))
                    ->required(fn(string $context): bool => $context === 'create')
                    ->rule(Password::defaults())
                    ->minLength(8)
                    ->validationMessages([
                        'required' => 'Password tidak boleh kosong',
                        'min' => 'Password harus minimal 8 karakter',
                    ]),

                Forms\Components\TextInput::make('password_confirmation')
                    ->label('Konfirmasi Password')
                    ->password()
                    ->revealable()
                    ->dehydrated(false)
                    ->required(fn(string $context): bool => $context === 'create')
                    ->same('password')
                    ->validationMessages([
                        'required' => 'Konfirmasi password tidak boleh kosong',
                        'same' => 'Konfirmasi password tidak cocok',
                    ]),

                Forms\Components\Select::make('role')
                    ->label('Role')
                    ->options([
                        'admin' => 'Admin',
                        'user' => 'User',
                    ])
                    ->default('user')
                    ->required(),

                Forms\Components\FileUpload::make('profile_picture')
                    ->label('Foto Profil')
                    ->image()
                    ->disk('public')
                    ->directory('images/profile-pictures')
                    ->acceptedFileTypes(['image/jpg', 'image/jpeg', 'image/png', 'image/webp'])
                    ->maxSize(2048)
                    ->nullable()
                    ->validationMessages([
                        'image' => 'File harus berupa gambar yang valid',
                        'mimes' => 'Format gambar harus jpg/jpeg/png/webp',
                        'max' => 'Ukuran gambar tidak boleh lebih dari 2MB',
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('role')
                    ->label('Role / Peran')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'admin' => 'success',
                        'user' => 'warning',
                    }),

                Tables\Columns\ImageColumn::make('profile_picture')
                    ->label('Foto Profil')
                    ->circular()
                    ->disk('public')
                    ->getStateUsing(fn($record) => $record->profile_picture ?: 'images/profile-pictures/default-profile.webp'),

                Tables\Columns\TextColumn::make('email_verified_at')
                    ->label('Terverifikasi Pada')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Daftar Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->options([
                        'admin' => 'Admin',
                        'user' => 'User',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
