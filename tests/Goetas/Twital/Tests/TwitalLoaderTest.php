<?php
namespace Goetas\Twital\Tests;

use Goetas\Twital\TwitalLoader;
use Goetas\Twital\Twital;
use Goetas\Twital\SourceAdapter\XMLAdapter;

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

    protected function getRequiredAdapters()
    {
        return array(
            'Goetas\Twital\SourceAdapter\HTML5Adapter',
            'Goetas\Twital\SourceAdapter\XMLAdapter',
            'Goetas\Twital\SourceAdapter\XHTMLAdapter'
        );
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
        $loader = new \Twig_Loader_String();

        $twitalLoader = new TwitalLoader($loader);

        $this->assertSame($loader, $twitalLoader->getLoader());

        $newLoader = new \Twig_Loader_String();
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
            $filteredAdapters = array_filter($adapters, function ($adapter) use($class)
            {
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
        $twitalLoader = new TwitalLoader(new \Twig_Loader_String(), $twital, false);
        $twitalLoader->addSourceAdapter('/.*\.xml$/', new XMLAdapter());

        $twital->expects($this->once())->method('compile');
        $twitalLoader->getSource('aaa.xml');
    }

    public function testNonTwitalFile()
    {
        $twital = $this->getMock('Goetas\Twital\Twital');
        $twitalLoader = new TwitalLoader(new \Twig_Loader_String(), $twital, false);
        $twitalLoader->addSourceAdapter('/.*\.xml$/', new XMLAdapter());

        $twital->expects($this->never())->method('compile');
        $twitalLoader->getSource('aaa.txt');
    }
}


