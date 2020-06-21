<?php

namespace Goetas\Twital\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

if (class_exists(BaseTestCase::class)) {
    abstract class TestCase extends BaseTestCase
    {
    }
} else {
    abstract class TestCase extends \PHPUnit_Framework_TestCase
    {
        protected function createMock($originalClassName)
        {
            return $this->getMockBuilder($originalClassName)
                ->disableOriginalConstructor()
                ->disableOriginalClone()
                ->disableArgumentCloning()
                ->getMock();
        }
    }
}
