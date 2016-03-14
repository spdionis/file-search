<?php


namespace FSBundle\Search;

use FSBundle\Search\Strategy\StrategyInterface;
use Psr\Log\LoggerInterface;

class SearchHandler
{

    /**
     * @var StrategyInterface
     */
    private $strategy;

    public function __construct(StrategyInterface $strategy, LoggerInterface $logger)
    {
        $this->strategy = $strategy;
        $this->logger = $logger;
    }

    /**
     * @param array $directories
     * @param $search
     * @param bool $recursive
     * @return array
     */
    public function search(array $directories, $search, $recursive = false)
    {
        $matchingFiles = [];

        foreach ($directories as $dir) {
            if (!is_dir($dir)) {
                continue;
            }

            $matchingFiles = array_merge($matchingFiles, $this->strategy->searchInDir($dir, $search, $recursive));
        }

        return $matchingFiles;
    }


}