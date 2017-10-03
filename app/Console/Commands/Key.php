<?php
/**
 * Created by PhpStorm.
 * User: Luis Fernando
 * Date: 07/03/2017
 * Time: 11:44
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Key extends Command
{
    protected
        $signature = 'key:generate',
        $description = 'Generate key';

    public function handle()
    {
        $this->info(str_random(32));
    }
}