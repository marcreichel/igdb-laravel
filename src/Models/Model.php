<?php

namespace MarcReichel\IGDBLaravel\Models;

use Error;
use ArrayAccess;
use Carbon\Carbon;
use BadMethodCallException;
use Illuminate\Support\Str;
use MarcReichel\IGDBLaravel\Builder;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;
use MarcReichel\IGDBLaravel\Traits\HasAttributes;
use MarcReichel\IGDBLaravel\Traits\HasRelationships;

class Model implements ArrayAccess, Arrayable, Jsonable
{
    use HasAttributes,
        HasRelationships;

    public $identifier;
    public $privateEndpoint = false;

    protected $endpoint;
    public $builder;

    private static $instance;

    /**
     * Model constructor.
     *
     * @param array $properties
     */
    public function __construct($properties = [])
    {
        $this->builder = new Builder($this);
        self::$instance = $this;

        $this->setAttributes($properties);
        $this->setRelations($properties);
        $this->setIdentifier();
        $this->setEndpoint();
    }

    /**
     * @param $field
     *
     * @return mixed
     */
    public function __get($field)
    {
        return $this->getAttribute($field);
    }

    /**
     * @param $field
     *
     * @param $value
     */
    public function __set($field, $value)
    {
        $this->attributes[$field] = $value;
    }

    /**
     * @param mixed $field
     *
     * @return bool
     */
    public function offsetExists($field)
    {
        return isset($this->attributes[$field]) || isset($this->relations[$field]);
    }

    /**
     * @param mixed $field
     *
     * @return mixed
     */
    public function offsetGet($field)
    {
        return $this->getAttribute($field);
    }

    /**
     * @param mixed $field
     *
     * @return mixed
     */
    public function offsetSet($field, $value)
    {
        $this->attributes[$field] = $value;
    }

    /**
     * @param mixed $field
     */
    public function offsetUnset($field)
    {
        unset($this->attributes[$field], $this->relations[$field]);
    }

    /**
     * @param $field
     *
     * @return bool
     */
    public function __isset($field)
    {
        return $this->offsetExists($field);
    }

    /**
     * @param $field
     */
    public function __unset($field)
    {
        $this->offsetUnset($field);
    }
    
    /**
     * @param $method
     * @param $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->forwardCallTo($this->newQuery(), $method, $parameters);
    }

    /**
     * @param $method
     * @param $parameters
     *
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        return (new static)->$method(...$parameters);
    }

    /**
     * @return mixed
     */
    public static function all()
    {
        return (new static)->limit(config('igdb.per_page_limit', 50))
            ->get();
    }

    protected function setIdentifier()
    {
        $this->identifier = collect($this->attributes)->get('id');
    }

    /**
     * @param array $attributes
     */
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

    /**
     * @param array $attributes
     */
    protected function setRelations(array $attributes)
    {
        $this->relations = collect($attributes)
            ->diffKeys($this->attributes)->map(function ($value, $key) {
                if (is_array($value)) {
                    return collect($value)->map(function($value) use ($key) {
                        return $this->mapToModel($key, $value);
                    });
                }
                return $this->mapToModel($key, $value);
            });
    }

    /**
     * @param $object
     * @param $method
     * @param $parameters
     *
     * @return mixed
     */
    public function forwardCallTo($object, $method, $parameters)
    {
        try {
            return $object->$method(...$parameters);
        } catch (Error | BadMethodCallException $e) {
            throw new BadMethodCallException($e->getMessage());
        }
    }

    /**
     * @return \MarcReichel\IGDBLaravel\Builder
     */
    public function newQuery()
    {
        return new Builder($this);
    }

    /**
     * @param $fields
     *
     * @return \MarcReichel\IGDBLaravel\Models\Model
     */
    public function getInstance($fields)
    {
        if (is_null(self::$instance)) {
            $model = new static($fields);
            self::$instance = $model;
        }

        return self::$instance;
    }

    /**
     * @param $field
     *
     * @return mixed
     */
    public function getAttribute($field)
    {
        return collect($this->attributes)->merge($this->relations)->get($field);
    }

    /**
     * @param $property
     * @param $value
     *
     * @return array
     */
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

    /**
     * @param $property
     *
     * @return bool|mixed|string
     */
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

    /**
     * @param $value
     *
     * @return array
     */
    protected function getProperties($value)
    {
        return collect($value)->toArray();
    }

    protected function setEndpoint()
    {
        if (!$this->endpoint) {
            $class = class_basename(get_class($this));

            $this->endpoint = ($this->privateEndpoint ? '/private' : '') .
                Str::start(Str::snake(Str::plural($class)), '/');
        }
    }

    /**
     * @return mixed
     */
    protected function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        $attributes = collect($this->attributes);
        $relations = collect($this->relations)->map(function($relation) {
            if($relation instanceof Arrayable){
                return $relation->toArray();
            }
            return $relation;
        });
        return $attributes->merge($relations)->sortKeys()->toArray();
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0): string
    {
        return collect($this->toArray())->toJson($options);
    }
}