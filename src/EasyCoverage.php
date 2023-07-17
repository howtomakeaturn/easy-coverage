<?php

namespace Howtomakeaturn\EasyCoverage;

use HaydenPierce\ClassFinder\ClassFinder;

class EasyCoverage
{
    protected $includedNamespaces = [];

    protected $alwaysIgnoredMethods = [];

    protected $ignoredClasses = [];

    protected $ignoredClassMethods = [];

    protected $result;

    protected $missingMethods = [];

    public function includeNamespaces($namespaces)
    {
        $this->includedNamespaces = $namespaces;
    }

    public function alwaysIgnoreMethods($methods)
    {
        $this->alwaysIgnoredMethods = $methods;
    }

    public function ignoreClasses($classes)
    {
        $this->ignoredClasses = $classes;
    }

    public function ignoreClassMethods($classMethods)
    {
        $this->ignoredClassMethods = $classMethods;
    }

    public function result()
    {
        if ($this->result === null) {
            throw new Exceptions\NoScanYetException("Please call scan() before you call result().");
        }

        return $this->result;
    }

    public function missingMethods()
    {
        return $this->missingMethods;
    }

    public function scan()
    {
        $methods = $this->getIncludedMethods();

        if (count($methods) === 0) {
            throw new Exceptions\MethodsNotFoundException("Didn't find any methods to scan. You might have a typo when you includeNamespaces()");
        }

        $tests = $this->getTestedMethods();

        $missing = [];

        foreach ($methods as $method) {
            if (in_array($method, $tests)) {
                // no-op
            } else {
                $missing[] = $method;
            }
        }

        if (count($missing) > 0) {
            $this->missingMethods = $missing;

            $this->result = false;
        } else {
            $this->result = true;
        }
    }

    protected function getIncludedMethods()
    {
        ClassFinder::disablePSR4Vendors();

        $classes = [];

        foreach ($this->includedNamespaces as $namespace) {
            $classes = array_merge($classes, ClassFinder::getClassesInNamespace($namespace, ClassFinder::RECURSIVE_MODE));
        }

        $result = [];

        foreach ($classes as $c) {
            $class = new \ReflectionClass($c);

            $methods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);

            foreach ($methods as $i => $m) {
                if ($m->getFileName() !== $class->getFileName()) {
                    continue;
                }

                if ($m->isStatic()) {
                    continue;
                }

                if (in_array($c, $this->ignoredClasses)) {
                    continue;
                }

                if (in_array($m->getName(), $this->alwaysIgnoredMethods)) {
                    continue;
                }

                $classMethod = $c . '@' . $m->getName();

                if (in_array($classMethod, $this->ignoredClassMethods)) {
                    continue;
                }

                $result[] = $classMethod;
            }
        }

        return $result;
    }

    protected function getTestedMethods()
    {
        ClassFinder::disablePSR4Vendors();

        $classes = ClassFinder::getClassesInNamespace('Tests', ClassFinder::RECURSIVE_MODE);

        $result = [];

        foreach ($classes as $c) {
            $class = new \ReflectionClass($c);

            $methods = $class->getMethods();

            foreach ($methods as $m) {
                foreach ($m->getAttributes(Target::class) as $attr) {
                    $result = array_merge($result, $attr->getArguments());
                }
            }
        }

        return $result;
    }
}
