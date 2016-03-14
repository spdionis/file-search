<?php

namespace FSBundle\Command;


use FSBundle\Adapter\AdapterInterface;
use FSBundle\Search\SearchHandler;
use FSBundle\Search\Strategy\StrategyInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Over-engineered file search command
 */
class FileSearchCommand extends Command
{
    /**
     * @var array|StrategyInterface[]
     */
    private $strategies = [];

    protected function configure()
    {
        $this
            ->setName('fs:file-search')
            ->setDescription('Search file by contents. By default arguments are considered directories.')
            ->addOption('strategy', '-s', InputOption::VALUE_OPTIONAL, "search strategy, possible are: 'naive', 'efficient', 'regexp', default is 'regexp'", 'regexp')
            ->addOption('recursive', '-r', InputOption::VALUE_NONE, 'traverse directories recursively')
            ->addArgument('search', InputArgument::REQUIRED, 'searched string')
            ->addArgument('directories', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'directories for search');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $strategy = $this->getStrategy($input->getOption('strategy'));
        $searchHandler = new SearchHandler($strategy, new ConsoleLogger($output));

        $result = $searchHandler->search($input->getArgument('directories'), $input->getArgument('search'), $input->getOption('recursive'));

        foreach ($result as $filePath) {
            $output->writeln($filePath);
        }
    }

    private function getStrategy($strategyName)
    {
        if (!array_key_exists($strategyName, $this->strategies)) {
            throw new \InvalidArgumentException("Unknown strategy '$strategyName'.");
        }


        return $this->strategies[$strategyName];
    }

    public function addStrategy(StrategyInterface $strategy)
    {
        $this->strategies[$strategy->getName()] = $strategy;
    }
}