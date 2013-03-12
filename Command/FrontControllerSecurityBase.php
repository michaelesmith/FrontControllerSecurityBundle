<?php

namespace MS\Bundle\FrontControllerSecurityBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Finder\Finder;

abstract class FrontControllerSecurityBase extends Command
{
    protected function getCurrentSecurity($filename)
    {
        return json_decode(file_get_contents($filename));
    }

    protected function putSecurityFile($filename, $ranges)
    {
        $ranges = array_merge($ranges);

        file_put_contents($filename, json_encode($ranges, defined('JSON_PRETTY_PRINT') ? JSON_PRETTY_PRINT : null));
    }

    protected function getSecurityFilename($filename)
    {
        return $filename ?: $this->guessSecurityFile();
    }

    protected function guessSecurityFile()
    {
        $finder = new Finder();
        $finder->files()->ignoreDotFiles(false)->depth('== 0')->name('/^\..*\.security\.json$/')->in(getcwd().'/')->in(getcwd().'/web/');

        if(count($finder) > 1){
            throw new \InvalidArgumentException('Could not guess security.json file. Multiple files found. ' . implode(', ', iterator_to_array($finder->getIterator())));
        }elseif(count($finder) < 1){
            throw new \InvalidArgumentException('Could not guess security.json file. No files found.');
        }

        return $finder->getIterator()->current()->getRealpath();
    }
}
