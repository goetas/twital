<?php
namespace Goetas\Twital\Tests;

use Goetas\Twital\SourceAdapter\HTML5Adapter;

class Html5CoreNodesTest extends CoreNodesTest
{
    public function getDataFormTemplates()
    {
        $all = array_merge(glob(__DIR__."/templates/*.htm"), glob(__DIR__."/templates/*.xml"));
        $data = array();
        foreach ($all as $file) {
            $source = file_get_contents($file);

            if (is_file(substr($file, 0, -4).".html.twig")) {
                $expectedFile = substr($file, 0, -4).".html.twig";
            } else {
                $expectedFile = substr($file, 0, -4).".twig";
            }
            $expected = file_get_contents($expectedFile);

            $data[] = array(
                $source,
                $expected,
            );
        }

        return $data;
    }

    protected function getSourceAdapter()
    {
        return new HTML5Adapter();
    }

    protected function cleanup($str)
    {
        $str = str_ireplace('<!DOCTYPE html>', '<!DOCTYPE html>', $str);
        return parent::cleanup($str);
    }
}
