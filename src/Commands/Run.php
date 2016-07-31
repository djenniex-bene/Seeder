<?php

namespace Groovey\Seeder\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Filesystem\Filesystem;

class Run extends Command
{
    private $app;
    public function __construct($app)
    {
        parent::__construct();
        $this->app = $app;
    }

    protected function configure()
    {
        $this
            ->setName('seed:run')
            ->setDescription('Runs the seeder.')
            ->addArgument(
                'class',
                InputArgument::REQUIRED,
                'Runs the seeder class task.'
            )
            ->addArgument(
                'total',
                InputArgument::OPTIONAL,
                'Total number of records to be seeded otherwise default value is 1.'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $class    = $input->getArgument('class');
        $total    = $input->getArgument('total');
        $fs       = new Filesystem();
        $filename = getcwd()."/database/seeds/{$class}.php";

        if (!$fs->exists($filename)) {
            $output->writeln('<error>The seeder class does not exits.</error>');

            return;
        }

        include_once __DIR__.'/../Factory.php';
        include_once __DIR__.'/../Seeder.php';
        include_once $filename;

        $instance = new $class();

        $instance->inject($output, $this->app, coalesce($total, 1));
        $instance->init();
        $instance->run();
    }
}
