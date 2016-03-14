<?php


namespace FSBundle\Search\Strategy;


interface StrategyInterface
{

    /**
     * @return string
     */
    public function getName();

    /**
     * @param $dir
     * @param $search
     * @param $recursive
     * @return array
     */
    public function searchInDir($dir, $search, $recursive);
}