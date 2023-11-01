<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Models;

class Game extends Model
{
    protected array $casts = [
        'age_ratings' => AgeRating::class,
        'alternative_names' => AlternativeName::class,
        'artwork' => Artwork::class,
        'bundles' => self::class,
        'collection' => Collection::class,
        'collections' => Collection::class,
        'cover' => Cover::class,
        'dlcs' => self::class,
        'expanded_games' => self::class,
        'expansions' => self::class,
        'external_games' => ExternalGame::class,
        'forks' => self::class,
        'franchise' => Franchise::class,
        'franchises' => Franchise::class,
        'game_engines' => GameEngine::class,
        'game_localizations' => GameLocalization::class,
        'game_modes' => GameMode::class,
        'genres' => Genre::class,
        'involved_companies' => InvolvedCompany::class,
        'keywords' => Keyword::class,
        'language_supports' => LanguageSupport::class,
        'multiplayer_modes' => MultiplayerMode::class,
        'parent_game' => self::class,
        'platforms' => Platform::class,
        'player_perspectives' => PlayerPerspective::class,
        'ports' => self::class,
        'release_dates' => ReleaseDate::class,
        'remakes' => self::class,
        'remasters' => self::class,
        'screenshots' => Screenshot::class,
        'similar_games' => self::class,
        'standalone_expansions' => self::class,
        'tags' => null,
        'themes' => Theme::class,
        'version_parent' => self::class,
        'videos' => GameVideo::class,
        'websites' => Website::class,
    ];
}
