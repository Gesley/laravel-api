<?php
/**
 * Created by PhpStorm.
 * User: Luis Fernando
 * Date: 07/03/2017
 * Time: 11:44
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Wsdl2PhpGenerator\Config;
use Wsdl2PhpGenerator\Generator;

class Wsdl extends Command
{
    protected
        $signature = 'wsdl:generate {url} {--path=} {--proxy=}',
        $description = 'Generate wsdl classes',
        $generator;

    public function __construct()
    {
        parent::__construct();
        $this->generator = new Generator();
    }

    public function handle()
    {
        $this->generator->generate(new Config(
            [
                'inputFile' => $this->argument('url'),
                'outputDir' => app()->path(). '/'. ($this->option('path') ?: 'Wsdl'),
                'namespaceName' => 'App\\Wsdl',
                'proxy' => 'http://proxyac:80'
            ]
        ));
        $this->info('');
    }
}