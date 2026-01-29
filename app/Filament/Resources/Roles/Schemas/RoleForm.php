<?php

namespace App\Filament\Resources\Roles\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RoleForm
{
    public static function schema(): array
    {
        return [
            Section::make('Información del Rol')
                ->schema([
                    TextInput::make('name')
                        ->label('Nombre del Rol (clave)')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255)
                        ->placeholder('admin, cajero, gerente')
                        ->helperText('Clave única del rol (sin espacios, minúsculas)'),

                    TextInput::make('display_name')
                        ->label('Nombre para Mostrar')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('Administrador, Cajero, Gerente'),

                    Textarea::make('description')
                        ->label('Descripción')
                        ->rows(3)
                        ->maxLength(500)
                        ->placeholder('Describe las responsabilidades de este rol'),
                ])
                ->columns(1),

            Section::make('Permisos')
                ->schema([
                    CheckboxList::make('permissions')
                        ->label('Selecciona los permisos')
                        ->relationship('permissions', 'display_name')
                        ->columns(3)
                        ->gridDirection('row')
                        ->searchable()
                        ->bulkToggleable(),
                ]),
        ];
    }
}
