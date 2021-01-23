<?php
namespace Goetas\Twital\Tests;

use Goetas\Twital\SourceAdapter\XMLAdapter;
use Goetas\Twital\Twital;
use Goetas\Twital\TwitalLoader;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Loader\ArrayLoader;
use Twig\Loader\LoaderInterface;

class TwitalLoaderTest extends TestCase
{
    protected $twital;

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
        $loader = new ArrayLoader();
        $twitalLoader = new TwitalLoader($loader);

        $this->assertSame($loader, $twitalLoader->getLoader());

        $newLoader = new ArrayLoader();
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
        $loader = new ArrayLoader();
        $loader->setTemplate('aaa.xml', '');

        $twital = $this->createMock(Twital::class);
        $twitalLoader = new TwitalLoader($loader, $twital, false);
        $twitalLoader->addSourceAdapter('/.*\.xml$/', new XMLAdapter());

        $twital->expects($this->once())->method('compile')->willReturn('');
        $twitalLoader->getSourceContext('aaa.xml');
    }

    public function testNonTwitalFile()
    {
        $loader = new ArrayLoader();
        $loader->setTemplate('aaa.txt', '');

        $twital = $this->createMock(Twital::class);
        $twitalLoader = new TwitalLoader($loader, $twital, false);
        $twitalLoader->addSourceAdapter('/.*\.xml$/', new XMLAdapter());

        $twital->expects($this->never())->method('compile');
        $twitalLoader->getSourceContext('aaa.txt');
    }

    public function testExistsWithBaseLoaderTwig1()
    {
        if (Environment::MAJOR_VERSION >= 2) {
            $this->markTestSkipped("Twig > 1 has the LoaderInterface::exists method");
        }

        $mockLoader = $this->createMock(LoaderInterface::class);
        $mockLoader->expects($this->once())->method('getSource')->with($this->equalTo('foo'));

        $twitalLoader = new TwitalLoader($mockLoader, null, false);
        $this->assertTrue($twitalLoader->exists('foo'));
    }

    public function testNonExistsWithBaseLoaderTwig1()
    {
        if (Environment::MAJOR_VERSION >= 2) {
            $this->markTestSkipped("Twig > 1 has the LoaderInterface::exists method");
        }

        $mockLoader = $this->createMock(LoaderInterface::class);

        $mockLoader->expects($this->once())
            ->method('getSource')
            ->with($this->equalTo('foo'))
            ->will($this->throwException(new LoaderError("File not found")));

        $twitalLoader = new TwitalLoader($mockLoader, null, false);
        $this->assertFalse($twitalLoader->exists('foo'));
    }

    public function testExistsWithBaseLoaderTwigGte2()
    {
        if (Environment::MAJOR_VERSION < 2) {
            $this->markTestSkipped("Twig >= 2 only");
        }

        $mockLoader = $this->createMock(LoaderInterface::class);

        $mockLoader->expects($this->once())->method('exists')->will($this->returnValue(true));

        $twitalLoader = new TwitalLoader($mockLoader, null, false);
        $this->assertTrue($twitalLoader->exists('foo'));
    }

    public function testNonExistsWithBaseLoaderTwigGte2()
    {
        if (Environment::MAJOR_VERSION < 2) {
            $this->markTestSkipped("Twig >= 2 only");
        }

        $mockLoader = $this->createMock(LoaderInterface::class);

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
}
