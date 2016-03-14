<?php


namespace FSBundle\Search\Strategy;


use DirectoryIterator;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class NaiveStrategy implements StrategyInterface
{
    public function searchInDir($dir, $search, $recursive = false)
    {
        $it = $this->getIterator($dir, $recursive);

        $result = [];
        foreach ($it as $fileinfo) {
            if ($fileinfo->isFile()) {
                $contents = file_get_contents($fileinfo->getRealPath());
                if (strpos($contents, $search) !== false) {
                    $result[] = $fileinfo->getRealPath();
                }
            }
        }

        return $result;
    }

    public function getName()
    {
        return 'naive';
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