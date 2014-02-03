<?php
namespace Goetas\Twital;

interface Extension
{

    public function getAttributes();

    public function getNodes();

    public function getPostFilters();

    public function getPreFilters();

    public function getLoaders();

    public function getDumpers();
}
