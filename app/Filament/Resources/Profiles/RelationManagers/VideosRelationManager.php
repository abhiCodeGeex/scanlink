<?php

namespace App\Filament\Resources\Profiles\RelationManagers;

use App\Models\Profile;
use App\Models\Video;
use App\Services\YouTubeService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class VideosRelationManager extends RelationManager
{
  protected static string $relationship = 'videos';

  protected static ?string $title = 'Videos';

  public function form(Schema $schema): Schema
  {
    return $schema
      ->components([
        TextInput::make('title')
          ->label('Video title')
          ->required()
          ->maxLength(255),
        TextInput::make('video_name')
          ->label('YouTube URL or video ID')
          ->required()
          ->helperText('Paste a YouTube watch URL or 11-character video ID.'),
      ]);
  }

  public function table(Table $table): Table
  {
    $youtube = app(YouTubeService::class);

    return $table
      ->columns([
        TextColumn::make('title')->label('Title'),
        TextColumn::make('video_name')
          ->label('YouTube')
          ->url(fn (Video $record): string => $youtube->watchUrl($record->video_name))
          ->openUrlInNewTab(),
        TextColumn::make('created_at')->label('Added')->dateTime('d/m/Y H:i'),
      ])
      ->headerActions([
        Action::make('addYouTubeLink')
          ->label('Add YouTube link')
          ->icon('heroicon-o-link')
          ->schema([
            TextInput::make('title')
              ->label('Video title')
              ->required()
              ->maxLength(255),
            TextInput::make('video_name')
              ->label('YouTube URL or video ID')
              ->required()
              ->helperText('Paste a YouTube watch URL or 11-character video ID.'),
          ])
          ->action(function (array $data): void {
            $videoId = app(YouTubeService::class)->parseVideoId($data['video_name'] ?? '');

            if ($videoId === null) {
              throw ValidationException::withMessages([
                'video_name' => 'Invalid YouTube URL or video ID.',
              ]);
            }

            /** @var Profile $profile */
            $profile = $this->getOwnerRecord();

            $profile->videos()->create([
              'client_id' => $profile->client_id,
              'user_id' => $profile->user_id,
              'title' => $data['title'],
              'video_name' => $videoId,
              'is_extra' => false,
            ]);

            Notification::make()
              ->title('Video linked to profile.')
              ->success()
              ->send();
          }),
        Action::make('pickExisting')
          ->label('Select existing video')
          ->icon('heroicon-o-queue-list')
          ->schema(function (): array {
            /** @var Profile $profile */
            $profile = $this->getOwnerRecord();
            $library = app(YouTubeService::class)->clientLibrary($profile->client_id, $profile->getKey());

            return [
              Select::make('library_video')
                ->label('Client video library')
                ->options(collect($library)->mapWithKeys(
                  fn (array $item): array => [$item['video_name'] => ($item['title'] ?: 'Untitled').' ('.$item['video_name'].')'],
                )->all())
                ->required()
                ->searchable(),
              TextInput::make('title')
                ->label('Title override')
                ->helperText('Optional — leave blank to reuse the library title.'),
            ];
          })
          ->action(function (array $data): void {
            /** @var Profile $profile */
            $profile = $this->getOwnerRecord();
            $videoId = $data['library_video'];
            $library = collect(app(YouTubeService::class)->clientLibrary($profile->client_id, $profile->getKey()));
            $source = $library->firstWhere('video_name', $videoId);

            $profile->videos()->create([
              'client_id' => $profile->client_id,
              'user_id' => $profile->user_id,
              'title' => filled($data['title'] ?? null) ? $data['title'] : ($source['title'] ?? 'Video'),
              'video_name' => $videoId,
              'is_extra' => false,
            ]);

            Notification::make()
              ->title('Video linked to profile.')
              ->success()
              ->send();
          }),
        Action::make('uploadToYouTube')
          ->label('Upload to YouTube')
          ->icon('heroicon-o-arrow-up-tray')
          ->visible(fn (): bool => app(YouTubeService::class)->hasUploadCredentials())
          ->schema([
            TextInput::make('title')
              ->label('Video title')
              ->required()
              ->maxLength(100),
            FileUpload::make('video_file')
              ->label('Video file')
              ->required()
              ->disk('local')
              ->directory('youtube-uploads')
              ->acceptedFileTypes(['video/mp4', 'video/quicktime', 'video/x-msvideo', 'video/webm'])
              ->maxSize(512000),
          ])
          ->action(function (array $data): void {
            /** @var Profile $profile */
            $profile = $this->getOwnerRecord();
            $path = is_array($data['video_file'] ?? null) ? ($data['video_file'][0] ?? null) : ($data['video_file'] ?? null);

            if (blank($path)) {
              Notification::make()->title('Choose a video file.')->danger()->send();

              return;
            }

            try {
              $videoId = app(YouTubeService::class)->uploadVideo($path, $data['title']);
            } catch (\Throwable $exception) {
              Notification::make()
                ->title('YouTube upload failed')
                ->body($exception->getMessage())
                ->danger()
                ->send();

              return;
            } finally {
              if (is_string($path) && Storage::disk('local')->exists($path)) {
                Storage::disk('local')->delete($path);
              }
            }

            $profile->videos()->create([
              'client_id' => $profile->client_id,
              'user_id' => $profile->user_id,
              'title' => $data['title'],
              'video_name' => $videoId,
              'is_extra' => false,
            ]);

            Notification::make()
              ->title('Video uploaded to YouTube and linked.')
              ->success()
              ->send();
          }),
      ])
      ->recordActions([
        DeleteAction::make()
          ->label('Delete')
          ->using(fn (Video $record) => app(YouTubeService::class)->deleteVideo($record)),
      ])
      ->emptyStateHeading('No videos linked');
  }

  public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
  {
    return str_contains($pageClass, 'EditProfile');
  }
}
