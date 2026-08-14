<?php

namespace DeVy\Core;

use ReflectionClass;
use ReflectionParameter;
use RuntimeException;

class Container
{
    private array $bindings = [];
    private array $instances = [];

    public function bind(string $id, callable $resolver): void
    {      
        $this->bindings[$id] = $resolver;
    }

    public function singleton(string $id, callable $resolver): void
    {         
        $this->bindings[$id] = function ($container) use ($resolver, $id) {
            if (!isset($this->instances[$id])) {
                $this->instances[$id] = $resolver($container);
            }
            return $this->instances[$id];
        };
    }

    public function get(string $id): mixed
    {
        // If explicitly bound
        if (isset($this->bindings[$id])) {
            return $this->bindings[$id]($this);
        }

        // Auto-resolve concrete class
        if (class_exists($id)) {
            return $this->build($id);
        }

        throw new RuntimeException("Service {$id} not bound.");
    }

    public function has(string $id): bool
    {
        if (isset($this->bindings[$id])) {
            return true;
        }

        if (isset($this->instances[$id])) {
            return true;
        }

        if (class_exists($id)) {
            return true; // auto-resolvable
        }

        return false;
    }

    private function build(string $class): object
    {
        $reflection = new ReflectionClass($class);

        if (!$reflection->isInstantiable()) {
            throw new RuntimeException("Class {$class} is not instantiable.");
        }

        $constructor = $reflection->getConstructor();

        // No constructor → just create instance
        if (!$constructor) {
            return new $class;
        }

        $dependencies = array_map(
            fn(ReflectionParameter $param) => $this->resolveParameter($param, $class),
            $constructor->getParameters()
        );

        return $reflection->newInstanceArgs($dependencies);
    }

    private function resolveParameter(ReflectionParameter $param, string $class): mixed
    {
        $type = $param->getType();

        // If it's a class dependency
        if ($type && !$type->isBuiltin()) {
            return $this->get($type->getName());
        }

        // If default value exists
        if ($param->isDefaultValueAvailable()) {
            return $param->getDefaultValue();
        }

        throw new RuntimeException(
            "Cannot resolve parameter \${$param->getName()} in {$class}"
        );
    }
}