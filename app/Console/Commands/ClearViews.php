<?php

namespace DailyRecipe\Console\Commands;

use DailyRecipe\Actions\View;
use Illuminate\Console\Command;

class ClearViews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dailyrecipe:clear-views';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all view-counts for all entities';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        View::clearAll();
        $this->comment('Views cleared');
    }
}
