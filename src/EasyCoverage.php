<?php

namespace Howtomakeaturn\EasyCoverage;

use Howtomakeaturn\EasyCoverage\Exceptions\NoScanYetException;

class EasyCoverage
{
    protected $includedNamespaces = [];

    protected $alwaysIgnoredMethods = [];

    protected $ignoredClasses = [];

    protected $ignoredClassMethods = [];

    protected $result;

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

    public function scan()
    {
        // todo

        $this->result = false;
    }

    public function result()
    {
        if ($this->result === null) {
            throw new NoScanYetException();
        }

        return $this->result;
    }
}
