<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;

class UserForm
{
    public static function schema(): array
    {
        return [
            Section::make('Información del Usuario')
                ->schema([
                    TextInput::make('name')
                        ->label('Nombre Completo')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('email')
                        ->label('Correo Electrónico')
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),

                    TextInput::make('password')
                        ->label('Contraseña')
                        ->password()
                        ->required(fn($record) => $record === null)
                        ->dehydrated(fn($state) => filled($state))
                        ->minLength(8)
                        ->helperText('Mínimo 8 caracteres. Dejar en blanco para mantener la contraseña actual al editar.'),
                ])
                ->columns(1),

            Section::make('Roles y Permisos')
                ->schema([
                    CheckboxList::make('roles')
                        ->label('Asignar Roles')
                        ->relationship('roles', 'display_name')
                        ->columns(3)
                        ->gridDirection('row')
                        ->helperText('Los permisos del usuario serán la suma de todos los roles asignados'),
                ]),
        ];
    }
}
