<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;


class DeployDev extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deploy:dev';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deploy To Dev';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Artisan::call('optimize');
        $this->info('Optimization complete.');

        Artisan::call('config:cache');
        $this->info('Configuration cache cleared and rebuilt.');

        Artisan::call('route:cache');
        $this->info('Route cache cleared and rebuilt.');

        Artisan::call('view:cache');
        $this->info('View cache cleared and rebuilt.');

        Artisan::call('event:cache');
        $this->info('Event cache cleared and rebuilt.');
    }
}
