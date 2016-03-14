<?php


namespace FSBundle\Search\Strategy;


use DirectoryIterator;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class RegexpStrategy implements StrategyInterface
{
    public function getName()
    {
        return 'regexp';
    }
    public function searchInDir($dir, $search, $recursive = false)
    {
        $result = [];

        if (@preg_match($search, null) === false) { //invalid regexp
            throw new \InvalidArgumentException('Invalid regexp pattern.');
        }

        $it = $this->getIterator($dir, $recursive);
        foreach ($it as $fileinfo) {
            if ($fileinfo->isFile()) {
                $contents = file_get_contents($fileinfo->getRealPath());
                if (preg_match($search, $contents) === 1) {
                    $result[] = $fileinfo->getRealPath();
                }
            }
        }

        return $result;
    }

    /**
     * @param $directory
     * @param bool $recursive
     * @return DirectoryIterator|RecursiveIteratorIterator
     */
    private function getIterator($directory, $recursive = false)
    {
        return $recursive ? $this->getRecursiveDirectoryIterator($directory) : $this->getDirectoryIterator($directory);
    }

    private function getDirectoryIterator($directory)
    {
        return new DirectoryIterator($directory);
    }

    private function getRecursiveDirectoryIterator($directory)
    {
        return new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
    }

}