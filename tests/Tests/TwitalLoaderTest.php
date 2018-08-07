<?php
namespace Goetas\Twital\Tests;

use Goetas\Twital\SourceAdapter\XMLAdapter;
use Goetas\Twital\Tests\Twig\StringLoader;
use Goetas\Twital\TwitalLoader;

class TwitalLoaderTest extends \PHPUnit_Framework_TestCase
{
    protected $twital;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
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
        $loader = new StringLoader();

        $twitalLoader = new TwitalLoader($loader);

        $this->assertSame($loader, $twitalLoader->getLoader());

        $newLoader = new StringLoader();
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
        $twital = $this->getMock('Goetas\Twital\Twital');
        $twitalLoader = new TwitalLoader(new StringLoader(), $twital, false);
        $twitalLoader->addSourceAdapter('/.*\.xml$/', new XMLAdapter());

        $twital->expects($this->once())->method('compile');
        $twitalLoader->getSourceContext('aaa.xml');
    }

    public function testNonTwitalFile()
    {
        $twital = $this->getMock('Goetas\Twital\Twital');
        $twitalLoader = new TwitalLoader(new StringLoader(), $twital, false);
        $twitalLoader->addSourceAdapter('/.*\.xml$/', new XMLAdapter());

        $twital->expects($this->never())->method('compile');
        $twitalLoader->getSourceContext('aaa.txt');
    }

    public function testExistsWithBaseLoader()
    {
        if (\Twig_Environment::MAJOR_VERSION === 2) {
            $this->markTestSkipped("Twig 2.x has the Twig_LoaderInterface::exists method");
        }

        $mockLoader = $this->getMock('Twig_LoaderInterface');

        $mockLoader->expects($this->once())
        ->method('getSource')
        ->with($this->equalTo('foo'));

        $mockLoader->expects($this->never())
        ->method('exists');

        $twitalLoader = new TwitalLoader($mockLoader, null, false);
        $this->assertTrue($twitalLoader->exists('foo'));
    }

    public function testNonExistsWithBaseLoader()
    {
        if (\Twig_Environment::MAJOR_VERSION === 2) {
            $this->markTestSkipped("Twig 2.x has the Twig_LoaderInterface::exists method");
        }

        $mockLoader = $this->getMock('Twig_LoaderInterface');

        $mockLoader->expects($this->once())
            ->method('getSource')
            ->with($this->equalTo('foo'))
            ->will($this->throwException(new \Twig_Error_Loader("File not found")));

        $mockLoader->expects($this->never())
            ->method('exists');

        $twitalLoader = new TwitalLoader($mockLoader, null, false);
        $this->assertFalse($twitalLoader->exists('foo'));
    }

    public function testExistsWithBaseLoaderTwig2()
    {
        if (\Twig_Environment::MAJOR_VERSION !== 2) {
            $this->markTestSkipped("Twig 2.x only");
        }

        $mockLoader = $this->getMock('Twig_LoaderInterface');

        $mockLoader->expects($this->once())
            ->method('exists')
            ->will($this->returnValue(true));

        $twitalLoader = new TwitalLoader($mockLoader, null, false);
        $this->assertTrue($twitalLoader->exists('foo'));
    }

    public function testNonExistsWithBaseLoaderTwig2()
    {
        if (\Twig_Environment::MAJOR_VERSION !== 2) {
            $this->markTestSkipped("Twig 2.x only");
        }

        $mockLoader = $this->getMock('Twig_LoaderInterface');

        $mockLoader->expects($this->once())
            ->method('exists')
            ->will($this->returnValue(false));

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
