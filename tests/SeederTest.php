<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Command\Command;
use Groovey\Seeder\Commands\About;
use Groovey\Seeder\Commands\Init;
use Groovey\Seeder\Commands\Run;

class SeederTest extends PHPUnit_Framework_TestCase
{
    public function connect()
    {
        $capsule = new Capsule();

        $capsule->addConnection([
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => 'test',
            'username'  => 'root',
            'password'  => '',
            'charset'   => 'utf8',
            'collation' => 'utf8_general_ci',
            'prefix'    => '',
        ], 'default');

        $capsule->bootEloquent();
        $capsule->setAsGlobal();

        return $capsule;
    }

    public function testAbout()
    {
        $tester = new Tester();
        $tester->command(new About(), 'seed:about');
        $this->assertRegExp('/Groovey/', $tester->getDisplay());
    }

    public function testInit()
    {
        $container['db'] = $this->connect();

        $tester = new Tester();
        $tester->command(new Init($container), 'seed:init');
        $this->assertRegExp('/Sucessfully/', $tester->getDisplay());
    }

    public function testRun()
    {
        Database::create();

        $container['db'] = $this->connect();

        $app = new Application();
        $app->add(new Run($container));
        $command = $app->find('seed:run');
        $tester = new CommandTester($command);

        $tester->execute([
                'command' => $command->getName(),
                'class'   => 'Users',
            ]);

        Database::drop();
    }
}
