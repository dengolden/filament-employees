<?php

namespace App\Filament\Resources\DepartmentResource\RelationManagers;

use Filament\Forms;
use App\Models\city;
use Filament\Tables;
use App\Models\state;
use App\Models\country;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;

class EmployeesRelationManager extends RelationManager
{
    protected static string $relationship = 'employees';

    protected static ?string $recordTitleAttribute = 'first_name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('country_id')
                        ->label('Country')
                        ->options(country::all()->pluck('name', 'id')->toArray())
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(fn (callable $set) => $set('state_id', null)),
                Select::make('state_id')
                        ->label('State')
                        ->required()
                        ->options(function (callable $get){
                            $country = country::find($get('country_id'));
                            if(!$country){
                                return state::all()->pluck('name', 'id');
                            }
                                return $country->states->pluck('name', 'id');
                            })
                        ->reactive()
                        ->afterStateUpdated(fn (callable $set) => $set('city_id', null)),
                Select::make('city_id')
                        ->label('City')
                        ->required()
                        ->options(function (callable $get){
                            $state = state::find($get('state_id'));
                            if(!$state){
                                return city::all()->pluck('name', 'id');
                            }
                            return $state->cities->pluck('name', 'id');
                        })
                        ->reactive(),
                    TextInput::make('first_name')->required()->maxLength(255),
                    TextInput::make('last_name')->required()->maxLength(255),
                    TextInput::make('address')->required()->maxLength(255),
                    TextInput::make('zip_code')->required()->maxLength(5),
                    DatePicker::make('birth_date')->required(),
                    DatePicker::make('date_hired')->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('first_name')->sortable()->searchable(),
                TextColumn::make('last_name')->sortable()->searchable(),
                TextColumn::make('department.name')->sortable(),
                TextColumn::make('date_hired')->date(),
                TextColumn::make('created_at')->dateTime(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }    
}
