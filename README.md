# Introduction

A simple package to encourage/force developers to write tests.

# Why I built this package?

I'm working with multiple junior developers in multiple projects.

I always want to find a way to encourage/force them to write tests.

And I want this process to be integrated into the CI/CD and pre-push workflow.

# Installation

```
composer require "howtomakeaturn/easy-coverage:^0.1"
```

# Usage

Let's say you think `App\Services` namespace is very important, and you want to force all developers to write tests for every public method under this namespace.

Here is the basic example:

```
<?php

namespace Tests\Coverage;

use PHPUnit\Framework\TestCase;
use Howtomakeaturn\EasyCoverage\EasyCoverage;

class CoverageTest extends TestCase
{
    public function testCoverage()
    {
        $coverage = new EasyCoverage();

        $coverage->includeNamespaces([
            'App\Services',
        ]);

        $coverage->scan();

        if ($coverage->result()) {
            $this->assertTrue(true);
        } else {
            $num = count($coverage->missingMethods());

            $str = implode(', ', $coverage->missingMethods());

            $msg = "You need to write tests for these $num methods: $str";

            $this->fail($msg);
        }
    }
}
```

Anytime you create a new test for the related methods, use `PHP 8 Attribute Syntax` to label them:

```
<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Howtomakeaturn\EasyCoverage\Target;

class SimpleTaskOneTest extends TestCase
{
    #[Target('App\Services\SimpleTaskOne@doSomething')]
    public function testBasicTest()
    {
        $task = new \App\Services\SimpleTaskOne();

        $task->doSomething();

        $this->assertTrue(true);
    }
}
```

And that's it!

Anyone who creates new public methods in any classes under `App\Services` have to write tests!

---

If you want to ignore some methods, you can use

```
    $coverage->alwaysIgnoreMethods([
        '__construct',
]);
```

If you want to ignore some classes, you can use

```
    $coverage->ignoreClasses([
        'App\Services\LargeLegacyTask',
    ]);
```

If you want to ignore certain methods in classes, you can use

```
    $coverage->ignoreClassMethods([
        'App\Services\SimpleTaskTwo@doSomething',
    ]);
```

# Recommended workflow

1. You specify multiple namespaces that you think writing tests are necessary.

2. Run EasyCoverage in PHPUnit & CI/CD & pre-push workflow.

3. Anyone who writes new public methods under these namespaces will be forced to write tests!

# How does this work behind the scenes?

1. EasyCoverage finds out all the `public` & `non-static` methods under the namesapces you specify.

2. EasyCoverage finds out all the class methods you labeled with `Howtomakeaturn\EasyCoverage\Target` attribute under the `Tests` namespace.

3. EasyCoverage compares two array values above and return return the result.

# License

The EasyCoverage is open-sourced software licensed under the MIT license.
