<?php
namespace Goetas\Twital\Tests;

use Goetas\Twital\SourceAdapter\XMLAdapter;
use Goetas\Twital\Twital;
use Goetas\Twital\TwitalLoader;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Loader\ArrayLoader;
use Twig\Loader\LoaderInterface;

class TwitalLoaderTest extends TestCase
{
    protected $twital;
    protected $twigMajorVersion;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->twigMajorVersion = class_exists(Environment::class) ? Environment::MAJOR_VERSION : \Twig_Environment::MAJOR_VERSION;
    }

    public function getMatchedFilenames()
    {
        return array(
            array(
                'foo.twital.xml',
                true
            ),
            array(
                'foo.twital.html',
                true
            ),
            array(
                'foo.twital.xhtml',
                true
            ),
            array(
                'foo.twital.atom',
                false
            )
        );
    }

    public function testInternalLoader()
    {
        $loader = $this->createArrayLoader();

        $twitalLoader = new TwitalLoader($loader);

        $this->assertSame($loader, $twitalLoader->getLoader());

        $newLoader = $this->createArrayLoader();
        $twitalLoader->setLoader($newLoader);
        $this->assertSame($newLoader, $twitalLoader->getLoader());
        $this->assertNotSame($loader, $twitalLoader->getLoader());
    }

    public function testDefaultAdapters()
    {
        $twitalLoader = new TwitalLoader();

        $adapters = $twitalLoader->getSourceAdapters();

        $this->assertContainsOnlyInstancesOf('Goetas\Twital\SourceAdapter', $adapters);

        foreach ($this->getRequiredAdapters() as $class) {
            $filteredAdapters = array_filter($adapters, function ($adapter) use ($class) {
                return is_a($adapter, $class);
            });
            $this->assertGreaterThanOrEqual(1, count($filteredAdapters), "Cant find any $class adapter");
        }
    }

    /**
     *
     * @dataProvider getMatchedFilenames
     */
    public function testRegexAdapters($filename, $managed)
    {
        $twitalLoader = new TwitalLoader();
        $this->assertEquals($managed, !!$twitalLoader->getSourceAdapter($filename));
    }

    public function testTwitalFile()
    {
        $loader = $this->createArrayLoader();
        $loader->setTemplate('aaa.xml', '');

        $twital = $this->createMock(Twital::class);
        $twitalLoader = new TwitalLoader($loader, $twital, false);
        $twitalLoader->addSourceAdapter('/.*\.xml$/', new XMLAdapter());

        $twital->expects($this->once())->method('compile')->willReturn('');
        $twitalLoader->getSourceContext('aaa.xml');
    }

    public function testNonTwitalFile()
    {
        $loader = $this->createArrayLoader();
        $loader->setTemplate('aaa.txt', '');

        $twital = $this->createMock(Twital::class);
        $twitalLoader = new TwitalLoader($loader, $twital, false);
        $twitalLoader->addSourceAdapter('/.*\.xml$/', new XMLAdapter());

        $twital->expects($this->never())->method('compile');
        $twitalLoader->getSourceContext('aaa.txt');
    }

    public function testExistsWithBaseLoaderTwig1()
    {
        if ($this->twigMajorVersion >= 2) {
            $this->markTestSkipped("Twig > 1 has the Twig_LoaderInterface::exists method");
        }

        $mockLoader = $this->createLoaderMock();
        $mockLoader->expects($this->once())->method('getSource')->with($this->equalTo('foo'));

        $twitalLoader = new TwitalLoader($mockLoader, null, false);
        $this->assertTrue($twitalLoader->exists('foo'));
    }

    public function testNonExistsWithBaseLoaderTwig1()
    {
        if ($this->twigMajorVersion >= 2) {
            $this->markTestSkipped("Twig > 1 has the Twig_LoaderInterface::exists method");
        }

        $mockLoader = $this->createMock('Twig_LoaderInterface');

        $errorClass = class_exists(LoaderError::class) ? LoaderError::class : \Twig_Error_Loader::class;
        $mockLoader->expects($this->once())
            ->method('getSource')
            ->with($this->equalTo('foo'))
            ->will($this->throwException(new $errorClass("File not found")));

        $twitalLoader = new TwitalLoader($mockLoader, null, false);
        $this->assertFalse($twitalLoader->exists('foo'));
    }

    public function testExistsWithBaseLoaderTwigGte2()
    {
        if ($this->twigMajorVersion < 2) {
            $this->markTestSkipped("Twig >= 2 only");
        }

        $mockLoader = $this->createLoaderMock();

        $mockLoader->expects($this->once())->method('exists')->will($this->returnValue(true));

        $twitalLoader = new TwitalLoader($mockLoader, null, false);
        $this->assertTrue($twitalLoader->exists('foo'));
    }

    public function testNonExistsWithBaseLoaderTwigGte2()
    {
        if ($this->twigMajorVersion < 2) {
            $this->markTestSkipped("Twig >= 2 only");
        }

        $mockLoader = $this->createLoaderMock();

        $mockLoader->expects($this->once())->method('exists')->will($this->returnValue(false));

        $twitalLoader = new TwitalLoader($mockLoader, null, false);
        $this->assertFalse($twitalLoader->exists('foo'));
    }

    protected function getRequiredAdapters()
    {
        return array(
            'Goetas\Twital\SourceAdapter\HTML5Adapter',
            'Goetas\Twital\SourceAdapter\XMLAdapter',
            'Goetas\Twital\SourceAdapter\XHTMLAdapter'
        );
    }

    private function createLoaderMock()
    {
        $class = interface_exists(\Twig_LoaderInterface::class) ? \Twig_LoaderInterface::class : LoaderInterface::class;

        return $this->createMock($class);
    }

    private function createArrayLoader()
    {
        return class_exists(\Twig_Loader_Array::class) ? new \Twig_Loader_Array() : new ArrayLoader();
    }
}
