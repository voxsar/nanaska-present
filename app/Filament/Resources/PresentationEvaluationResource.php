<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PresentationEvaluationResource\Pages;
use App\Filament\Resources\PresentationEvaluationResource\RelationManagers;
use App\Models\PresentationEvaluation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class PresentationEvaluationResource extends Resource
{
    protected static ?string $model = PresentationEvaluation::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Evaluations';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Presentation Information')
                    ->schema([
                        Forms\Components\Select::make('presentation_id')
                            ->relationship('presentation', 'title')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('Evaluation Prompt')
                    ->description('Enter the evaluation criteria and prompt for this presentation')
                    ->schema([
                        Forms\Components\Textarea::make('prompt')
                            ->label('Evaluation Prompt')
                            ->placeholder('Enter the evaluation prompt or criteria...')
                            ->rows(5)
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('Evaluation Results')
                    ->schema([
                        Forms\Components\Textarea::make('feedback')
                            ->label('Feedback')
                            ->placeholder('Enter detailed feedback for the presentation...')
                            ->rows(5)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('score')
                            ->label('Overall Score')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('/100'),
                        Forms\Components\KeyValue::make('criteria_scores')
                            ->label('Criteria Scores')
                            ->keyLabel('Criteria')
                            ->valueLabel('Score')
                            ->addActionLabel('Add Criteria')
                            ->columnSpanFull(),
                        Forms\Components\Hidden::make('evaluated_by')
                            ->default(fn () => Auth::id()),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('presentation.title')
                    ->label('Presentation')
                    ->sortable()
                    ->searchable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('presentation.user.name')
                    ->label('Presenter')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('evaluator.name')
                    ->label('Evaluated By')
                    ->sortable(),
                Tables\Columns\TextColumn::make('score')
                    ->label('Score')
                    ->suffix('/100')
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state >= 80 => 'success',
                        $state >= 60 => 'warning',
                        default => 'danger',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListPresentationEvaluations::route('/'),
            'create' => Pages\CreatePresentationEvaluation::route('/create'),
            'view' => Pages\ViewPresentationEvaluation::route('/{record}'),
            'edit' => Pages\EditPresentationEvaluation::route('/{record}/edit'),
        ];
    }
}
