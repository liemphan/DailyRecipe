<?php

namespace DailyRecipe\Console\Commands;

use DailyRecipe\Entities\Models\RecipeMenu;
use DailyRecipe\Entities\Repos\RecipemenuRepo;
use Illuminate\Console\Command;

class CopyMenuPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dailyrecipe:copy-menu-permissions
                            {--a|all : Perform for all menus in the system}
                            {--s|slug= : The slug for a menu to target}
                            ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy menu permissions to all child recipes';

    /**
     * @var RecipemenuRepo
     */
    protected $recipemenuRepo;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(RecipemenuRepo $repo)
    {
        $this->recipemenuRepo = $repo;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $menuSlug = $this->option('slug');
        $cascadeAll = $this->option('all');
        $menus = null;

        if (!$cascadeAll && !$menuSlug) {
            $this->error('Either a --slug or --all option must be provided.');

            return;
        }

        if ($cascadeAll) {
            $continue = $this->confirm(
                'Permission settings for all menus will be cascaded. ' .
                'Recipes assigned to multiple menus will receive only the permissions of it\'s last processed menu. ' .
                'Are you sure you want to proceed?'
            );

            if (!$continue && !$this->hasOption('no-interaction')) {
                return;
            }

            $menus = RecipeMenu::query()->get(['id', 'restricted']);
        }

        if ($menuSlug) {
            $menus = RecipeMenu::query()->where('slug', '=', $menuSlug)->get(['id', 'restricted']);
            if ($menus->count() === 0) {
                $this->info('No menus found with the given slug.');
            }
        }

        foreach ($menus as $menu) {
            $this->recipemenuRepo->copyDownPermissions($menu, false);
            $this->info('Copied permissions for menu [' . $menu->id . ']');
        }

        $this->info('Permissions copied for ' . $menus->count() . ' menus.');
    }
}
