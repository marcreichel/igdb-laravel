<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Tests;

use BackedEnum;
use MarcReichel\IGDBLaravel\Models\Model;
use ReflectionClass;
use ReflectionMethod;

/**
 * Compares the models and enums with IGDB's protobuf schema in tests/Fixtures/igdbapi.proto.
 * A failure means the models/enums and the fixture disagree: either the fixture was refreshed
 * with upstream changes, or a local model, cast, or enum drifted from it.
 *
 * @internal
 */
class SchemaTest extends TestCase
{
    private const MODELS = 'MarcReichel\IGDBLaravel\Models\\';

    private const ENUMS = 'MarcReichel\IGDBLaravel\Enums\\';

    /**
     * Proto messages that intentionally have no model.
     */
    private const IGNORED_MESSAGES = [
        'Count' => 'response wrapper',
        'MultiQueryResult' => 'response wrapper',
        'MultiQueryResultArray' => 'response wrapper',
        'TestDummy' => 'internal to IGDB',
        'ContentSafetyRating' => 'not documented on api-docs.igdb.com',
        'ContentSafetyRatingDimension' => 'not documented on api-docs.igdb.com',
        'GameContentSafetyRating' => 'not documented on api-docs.igdb.com',
    ];

    /**
     * Proto enum => PHP enums using its values. The names can't be derived.
     */
    private const ENUM_MAP = [
        'AgeRatingCategoryEnum' => ['AgeRating\Category'],
        'AgeRatingRatingEnum' => ['AgeRating\Rating'],
        'AgeRatingContentDescriptionCategoryEnum' => ['AgeRatingContentDescription\Category'],
        'GenderGenderEnum' => ['Character\Gender'],
        'CharacterSpeciesEnum' => ['Character\Species'],
        'DateFormatChangeDateCategoryEnum' => ['Company\ChangeDateCategory', 'Company\StartDateCategory', 'PlatformVersionReleaseDate\Category', 'ReleaseDate\Category'],
        'WebsiteCategoryEnum' => ['Website\Category', 'CompanyWebsite\Category', 'PlatformWebsite\Category'],
        'ExternalGameCategoryEnum' => ['ExternalGame\Category'],
        'ExternalGameMediaEnum' => ['ExternalGame\Media'],
        'GameCategoryEnum' => ['Game\Category'],
        'GameStatusEnum' => ['Game\Status'],
        'GameVersionFeatureCategoryEnum' => ['GameVersionFeature\Category'],
        'GameVersionFeatureValueIncludedFeatureEnum' => ['GameVersionFeatureValue\IncludedFeature'],
        'PlatformCategoryEnum' => ['Platform\Category'],
        'RegionRegionEnum' => ['PlatformVersionReleaseDate\Region', 'ReleaseDate\Region'],
        'PopularitySourcePopularitySourceEnum' => ['Popularity\Source'],
        'TestDummyEnumTestEnum' => [],
    ];

    /**
     * PHP enums whose existing values mean something else upstream. Kept as-is, because changing
     * a case value silently breaks stored data; both enums are deprecated upstream.
     */
    private const KNOWN_ENUM_CONFLICTS = [
        'AgeRatingContentDescription\Category',
        'PlatformWebsite\Category',
    ];

    public function testEveryProtoMessageHasAModel(): void
    {
        $missing = collect($this->messages())
            ->keys()
            ->reject(fn (string $message) => $this->isIgnored($message) || class_exists(self::MODELS . $message))
            ->values()
            ->all();

        $this->assertSame([], $missing, 'Proto messages without a model.');
    }

    public function testEveryModelHasAProtoMessage(): void
    {
        $orphans = collect($this->models())
            ->reject(fn (string $model) => isset($this->messages()[$model]))
            ->values()
            ->all();

        $this->assertSame([], $orphans, 'Models without a proto message.');
    }

    public function testRelationsResolveToTheProtoType(): void
    {
        $failures = [];

        foreach ($this->models() as $model) {
            $instance = $this->model($model);
            $resolve = new ReflectionMethod($instance, 'getClassNameForProperty');

            foreach ($this->messages()[$model] ?? [] as $field => $info) {
                if (!isset($this->messages()[$info['type']]) || $this->isIgnored($info['type'])) {
                    continue;
                }

                $resolved = $resolve->invoke($instance, $field);

                if ($resolved !== self::MODELS . $info['type']) {
                    $failures[] = sprintf('%s.%s resolves to %s, expected %s', $model, $field, var_export($resolved, true), $info['type']);
                }
            }
        }

        $this->assertSame([], $failures);
    }

    public function testCastsMatchProtoFields(): void
    {
        $failures = [];

        foreach ($this->models() as $model) {
            $fields = $this->messages()[$model] ?? [];
            $source = (string) file_get_contents((string) (new ReflectionClass($this->model($model)))->getFileName());

            foreach (array_keys($this->casts($model)) as $key) {
                if (!isset($fields[$key])) {
                    $failures[] = sprintf('%s: cast \'%s\' has no proto field', $model, $key);
                } elseif ($fields[$key]['deprecated'] && !preg_match("/'{$key}' =>.*@deprecated/", $source)) {
                    $failures[] = sprintf('%s: cast \'%s\' is deprecated upstream, mark it // @deprecated', $model, $key);
                }
            }
        }

        $this->assertSame([], $failures);
    }

    public function testEnumsMatchProtoEnums(): void
    {
        $failures = [];

        foreach ($this->enums() as $protoEnum => $cases) {
            if (!array_key_exists($protoEnum, self::ENUM_MAP)) {
                $failures[] = sprintf('Proto enum %s is not mapped in SchemaTest::ENUM_MAP', $protoEnum);

                continue;
            }

            foreach (self::ENUM_MAP[$protoEnum] as $phpEnum) {
                $class = self::ENUMS . $phpEnum;
                $deprecated = collect($cases)->every(fn (array $case) => $case['deprecated']);

                if ($deprecated && !str_contains((string) (new ReflectionClass($class))->getDocComment(), '@deprecated')) {
                    $failures[] = sprintf('%s is deprecated upstream, mark it @deprecated', $phpEnum);
                }

                if (in_array($phpEnum, self::KNOWN_ENUM_CONFLICTS, true)) {
                    continue;
                }

                $local = collect($class::cases())->mapWithKeys(fn (BackedEnum $case) => [$case->value => $case->name]);

                foreach ($cases as $value => $case) {
                    if (!$local->has($value)) {
                        $failures[] = sprintf('%s: missing case %s = %d', $phpEnum, $case['name'], $value);
                    } elseif ($this->normalize((string) $local->get($value)) !== $this->normalize($case['name'])) {
                        $failures[] = sprintf('%s: %d is %s locally, %s upstream', $phpEnum, $value, $local->get($value), $case['name']);
                    }
                }
            }
        }

        $this->assertSame([], $failures);
    }

    /**
     * @return array<string, array<string, array{type: string, deprecated: bool}>>
     */
    private function messages(): array
    {
        static $messages;

        if ($messages === null) {
            preg_match_all('/^message (\w+) \{(.*?)^}/ms', $this->proto(), $matches, PREG_SET_ORDER);

            $messages = collect($matches)
                ->reject(fn (array $match) => str_ends_with($match[1], 'Result'))
                ->mapWithKeys(function (array $match) {
                    preg_match_all('/^\s*(?:repeated )?([\w.]+) (\w+) = \d+( \[deprecated = true])?;/m', $match[2], $fields, PREG_SET_ORDER);

                    return [$match[1] => collect($fields)->mapWithKeys(fn (array $field) => [
                        $field[2] => ['type' => $field[1], 'deprecated' => isset($field[3])],
                    ])->all()];
                })
                ->all();
        }

        return $messages;
    }

    /**
     * Cases without the *_NULL = 0 placeholder.
     *
     * @return array<string, array<int, array{name: string, deprecated: bool}>>
     */
    private function enums(): array
    {
        preg_match_all('/^enum (\w+) \{(.*?)^}/ms', $this->proto(), $matches, PREG_SET_ORDER);

        return collect($matches)->mapWithKeys(function (array $match) {
            preg_match_all('/^\s*(\w+) = (\d+)( \[deprecated = true])?;/m', $match[2], $cases, PREG_SET_ORDER);

            return [$match[1] => collect($cases)
                ->reject(fn (array $case) => str_ends_with($case[1], '_NULL'))
                ->mapWithKeys(fn (array $case) => [(int) $case[2] => ['name' => $case[1], 'deprecated' => isset($case[3])]])
                ->all()];
        })->all();
    }

    /**
     * @return array<int, string>
     */
    private function models(): array
    {
        return collect(glob(__DIR__ . '/../src/Models/*.php') ?: [])
            ->map(fn (string $file) => basename($file, '.php'))
            ->filter(fn (string $name) => is_subclass_of(self::MODELS . $name, Model::class)
                && (new ReflectionClass(self::MODELS . $name))->isInstantiable())
            ->values()
            ->all();
    }

    private function model(string $model): Model
    {
        $instance = new (self::MODELS . $model)();
        assert($instance instanceof Model);

        return $instance;
    }

    /**
     * @return array<string, mixed>
     */
    private function casts(string $model): array
    {
        $instance = $this->model($model);

        return (array) (new ReflectionClass($instance))->getProperty('casts')->getValue($instance);
    }

    private function isIgnored(string $message): bool
    {
        return array_key_exists($message, self::IGNORED_MESSAGES);
    }

    /**
     * Upstream prefixes some cases (WEBSITE_STEAM, EXTERNALGAME_GOG) and spells some without underscores.
     */
    private function normalize(string $name): string
    {
        return str_replace('_', '', (string) preg_replace('/^(WEBSITE|EXTERNALGAME)_/', '', $name));
    }

    private function proto(): string
    {
        return (string) file_get_contents(__DIR__ . '/Fixtures/igdbapi.proto');
    }
}
