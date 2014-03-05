<?php
namespace Goetas\Twital;

interface Compiler
{
    public function compile($source, $name);

    public function getNodes();
    public function getAttributes();
    public function getSourceAdapters();
}
