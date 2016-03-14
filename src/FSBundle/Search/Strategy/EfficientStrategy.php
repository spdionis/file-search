<?php


namespace FSBundle\Search\Strategy;


use DirectoryIterator;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Finder\SplFileInfo;

class EfficientStrategy implements StrategyInterface
{
    public function getName()
    {
        return 'efficient';
    }

    public function searchInDir($dir, $search, $recursive)
    {
        $it = $this->getIterator($dir, $recursive);

        $result = [];
        foreach ($it as $fileinfo) {
            if ($fileinfo->isFile()) {
                if ($this->isMatch($fileinfo, $search)) {
                    $result[] = $fileinfo->getRealPath();
                }
            }
        }

        return $result;
    }

    /**
     * @param SplFileInfo|DirectoryIterator $fileinfo
     * @param $search
     * @return bool
     */
    private function isMatch($fileinfo, $search)
    {
        $fh = $fileinfo->openFile();

        $tmp = '';
        while (!$fh->eof()) {
            $buffer = $fh->fread(4096);
            if (strpos($tmp . $buffer, $search) !== false) {
                return true;
            }

            $tmp = substr($buffer, 1 + (-1 * strlen($search)));
        }

        return false;
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