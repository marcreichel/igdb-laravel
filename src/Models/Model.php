<?php

namespace MarcReichel\IGDBLaravel\Models;

use Carbon\Carbon;
use Error;
use BadMethodCallException;
use Illuminate\Support\Str;
use MarcReichel\IGDBLaravel\Builder;
use MarcReichel\IGDBLaravel\Traits\HasAttributes;
use MarcReichel\IGDBLaravel\Traits\HasRelationships;

class Model
{
    use HasAttributes,
        HasRelationships;

    public $identifier;
    public $privateEndpoint = false;

    protected $endpoint;
    public $builder;

    private static $instance;

    public function __construct($properties = [])
    {
        $this->builder = new Builder($this);
        self::$instance = $this;

        $this->setAttributes($properties);
        $this->setRelations($properties);
        $this->setIdentifier();
        $this->setEndpoint();
    }

    public function __get($field)
    {
        return $this->getAttribute($field);
    }

    public function __call($method, $parameters)
    {
        return $this->forwardCallTo($this->newQuery(), $method, $parameters);
    }

    public static function __callStatic($method, $parameters)
    {
        return (new static)->$method(...$parameters);
    }

    public static function all()
    {
        return (new static)->limit(config('igdb.per_page_limit', 50))
            ->get();
    }

    protected function setIdentifier()
    {
        $this->identifier = collect($this->attributes)->get('id');
    }

    protected function setAttributes(array $attributes)
    {
        $this->attributes = collect($attributes)->filter(function ($value) {
            if (is_array($value)) {
                return collect($value)->filter(function ($value) {
                    return is_object($value);
                })->isEmpty();
            }
            return !is_object($value);
        })->map(function ($value, $key) {
            $dates = collect($this->dates);
            if ($dates->contains($key)) {
                return Carbon::createFromTimestamp($value);
            }
            return $value;
        })->toArray();
    }

    protected function setRelations(array $attributes)
    {
        $this->relations = collect($attributes)
            ->diffKeys($this->attributes)->map(function ($value, $key) {
                if (is_array($value)) {
                    return collect($value)->map(function($value) use ($key) {
                        return $this->mapToModel($key, $value);
                    })->toArray();
                }
                return $this->mapToModel($key, $value);
            })->toArray();
    }

    public function forwardCallTo($object, $method, $parameters)
    {
        try {
            return $object->$method(...$parameters);
        } catch (Error | BadMethodCallException $e) {
            throw new BadMethodCallException($e->getMessage());
        }
    }

    public function newQuery()
    {
        return new Builder($this);
    }

    public function getInstance($fields)
    {
        if (is_null(self::$instance)) {
            $model = new static($fields);
            self::$instance = $model;
        }

        return self::$instance;
    }

    public function getAttribute($field)
    {
        return collect($this->attributes)->merge($this->relations)->get($field);
    }

    private function mapToModel($property, $value)
    {
        $class = $this->getClassNameForProperty($property);
        if ($class) {
            if (is_object($value)) {
                $properties = $this->getProperties($value);
                $model = new $class($properties);

                return $model;
            } else {
                if (is_array($value)) {
                    return collect($value)->map(function ($single) use (
                        $property,
                        $value,
                        $class
                    ) {
                        if (is_object($single)) {
                            $properties = $this->getProperties($single);
                            $model = new $class($properties);
                            return $model;
                        }
                        return $single;
                    })->toArray();
                }
            }
        }

        return $value;
    }

    protected function getClassNameForProperty($property)
    {
        if (collect($this->casts)->has($property)) {
            $class = collect($this->casts)->get($property);

            if (class_exists($class)) {
                return $class;
            }
        }

        $class = __NAMESPACE__ . '\\' . Str::singular(Str::studly($property));

        if (class_exists($class)) {
            return $class;
        }

        $class = __NAMESPACE__ . '\\' . class_basename(get_class($this)) . Str::singular(Str::studly($property));

        if (class_exists($class)) {
            return $class;
        }

        return false;
    }

    protected function getProperties($value)
    {
        return collect($value)->toArray();
    }

    protected function setEndpoint()
    {
        if (!$this->endpoint) {
            $class = class_basename(get_class($this));

            $this->endpoint = ($this->privateEndpoint ? '/private' : '') .
                str_start(snake_case(str_plural($class)), '/');
        }
    }

    protected function getEndpoint()
    {
        return $this->endpoint;
    }
}
