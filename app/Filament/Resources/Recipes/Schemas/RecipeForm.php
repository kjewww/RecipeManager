<?php

namespace App\Filament\Resources\Recipes\Schemas;

use App\Enums\Complexity;
use App\Models\Author;
use App\Models\Category;
use App\Models\Cookbook;
use App\Models\Tag;
use App\Services\RecipeImport\RecipeJsonSchema;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RecipeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Select::make('author_id')
                    ->label(__('Author'))
                    ->required()
                    ->searchable()
                    ->preload()
                    ->visible(fn (): bool => (bool) user()?->admin)
                    ->options(fn (): array => Author::query()->orderBy('name')->pluck('name', 'id')->all()),
                Select::make('cookbook_id')
                    ->label(__('Cookbook'))
                    ->nullable()
                    ->searchable()
                    ->preload()
                    ->options(function (): array {
                        if (user()?->admin) {
                            return Cookbook::query()->orderBy('name')->pluck('name', 'id')->all();
                        }

                        return Cookbook::query()
                            ->where('author_id', user()?->author_id)
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->all();
                    })
                    ->createOptionForm([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(100),
                    ])
                    ->createOptionUsing(function (array $data): int {
                        return Cookbook::query()->create([
                            'name' => $data['name'],
                            'author_id' => user()->author_id,
                        ])->id;
                    }),
                Select::make('category_id')
                    ->label(__('Category'))
                    ->required()
                    ->searchable()
                    ->preload()
                    ->options(fn (): array => Category::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->createOptionForm([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(100),
                    ])
                    ->createOptionUsing(function (array $data): int {
                        return Category::query()->create([
                            'name' => $data['name'],
                        ])->id;
                    }),
                TextInput::make('name')
                    ->required()
                    ->maxLength(100),
                TextInput::make('servings')
                    ->label(__('Servings'))
                    ->numeric()
                    ->minValue(0)
                    ->step(0.5)
                    ->nullable(),
                TextInput::make('serving_type')
                    ->label(__('Serving type'))
                    ->maxLength(20)
                    ->nullable(),
                Select::make('complexity')
                    ->label(__('Complexity'))
                    ->required()
                    ->options([
                        Complexity::Simple->value => Complexity::Simple->label(),
                        Complexity::Normal->value => Complexity::Normal->label(),
                        Complexity::Difficult->value => Complexity::Difficult->label(),
                    ]),
                TimePicker::make('preparation_time')
                    ->label(__('Preparation time'))
                    ->seconds(false)
                    ->nullable(),
                Toggle::make('is_public')
                    ->label(__('Public'))
                    ->helperText(__('Public recipes and their images are visible to everyone, including visitors who are not logged in.'))
                    ->default(false),
                Select::make('tags')
                    ->label(__('Tags'))
                    ->relationship('tags', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->options(fn (): array => Tag::query()->orderBy('name')->pluck('name', 'id')->all())
                    // Mirrors LookupType::canBeCreatedBy(): only admins add new tags.
                    ->createOptionForm(fn (): ?array => user()?->admin ? [
                        TextInput::make('name')
                            ->required()
                            ->maxLength(RecipeJsonSchema::MAX_TAG_NAME)
                            ->unique(Tag::class, 'name'),
                    ] : null)
                    ->createOptionUsing(function (array $data): int {
                        abort_unless((bool) user()?->admin, 403);

                        return Tag::query()->create(['name' => $data['name']])->id;
                    })
                    ->columnSpanFull(),
                RichEditor::make('instructions')
                    ->label(__('Instructions'))
                    ->required()
                    ->columnSpanFull(),
                FileUpload::make('photos')
                    ->label(__('Images'))
                    ->image()
                    ->multiple()
                    ->reorderable()
                    ->appendFiles()
                    ->disk('recipes')
                    ->directory(fn ($record): ?string => $record ? (string) $record->getKey() : null)
                    ->visibility('private')
                    ->visibleOn('edit')
                    ->columnSpanFull(),
                IngredientSchema::repeater(
                    'ungroupedIngredients',
                    __('Ingredients without group'),
                    ungrouped: true,
                ),
                Section::make(__('Ingredient groups'))
                    ->schema([
                        Repeater::make('ingredientGroups')
                            ->label(__('Ingredient groups'))
                            ->relationship()
                            ->orderColumn('position')
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(20),
                                IngredientSchema::repeater(
                                    'topLevelIngredients',
                                    __('Ingredients'),
                                ),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
