<?php


namespace tests\FSBundle;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

class FileSearchCommandTest extends KernelTestCase
{
    /**
     * @var string
     */
    private $testDir;

    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * @var
     */
    private $files;

    public function setUp()
    {
        parent::setUp();
        static::bootKernel();
        $cacheDir = static::$kernel->getContainer()->getParameter('kernel.cache_dir');

        $this->testDir = $cacheDir . DIRECTORY_SEPARATOR . 'test';
        $this->fs = static::$kernel->getContainer()->get('filesystem');

        //assuming it always works
        $this->fs->mkdir($this->testDir);

        $this->prepareFiles();
    }

    /**
     * @dataProvider getInputs
     * @param $input
     * @param $resultFileIndexes
     */
    public function testExecute($input, $resultFileIndexes)
    {

        $input = array_merge($input, ['directories' => [$this->testDir]]);

        $command = static::$kernel->getContainer()->get('fs.command.file_search');
        $commandTester = new CommandTester($command);

        $commandTester->execute($input);
        $displayOutput = $commandTester->getDisplay(true);
        $expectedOutput = implode("\n", array_map(
                function ($index) { return array_keys($this->files)[$index]; },
                $resultFileIndexes
            )
        ) . "\n";
        $this->assertEquals($expectedOutput, $displayOutput, $displayOutput);
    }

    public function getInputs()
    {
        return [
            [
                ['--strategy' => 'naive', 'search' => 'abc'],
                [0],
            ],
            [
                ['--strategy' => 'efficient', 'search' => 'abc'],
                [0],
            ],
            [
                ['--strategy' => 'regexp', 'search' => '/abc/'],
                [0],
            ],
            [
                ['--strategy' => 'naive', '--recursive' => true, 'search' => 'abc'],
                [0, 2],
            ],
            [
                ['--strategy' => 'efficient', '--recursive' => true, 'search' => 'abc'],
                [0, 2],
            ],
            [
                ['--strategy' => 'regexp', '--recursive' => true, 'search' => '/abc/'],
                [0, 2],
            ],
        ];
    }

    private function prepareFiles()
    {
        $this->files = [
            $this->testDir . DIRECTORY_SEPARATOR . 'file1.txt' => 'abcasdf123',
            $this->testDir . DIRECTORY_SEPARATOR . 'file2.txt' => 'asdf123',
            $this->testDir . DIRECTORY_SEPARATOR . 'rec' . DIRECTORY_SEPARATOR .'file3.txt' => 'abc123',
            $this->testDir . DIRECTORY_SEPARATOR . 'rec' . DIRECTORY_SEPARATOR .'file4.txt' => 'asdf123',
        ];

        foreach ($this->files as $path => $contents) {
            $this->fs->dumpFile($path, $contents);
        }
    }

    protected function tearDown()
    {
//        $this->fs->remove($this->testDir);
        parent::tearDown();
    }


}