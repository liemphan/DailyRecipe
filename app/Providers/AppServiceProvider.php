<?php

namespace DailyRecipe\Providers;

use DailyRecipe\Auth\Access\LoginService;
use DailyRecipe\Auth\Access\SocialAuthService;
use DailyRecipe\Entities\BreadcrumbsViewComposer;
use DailyRecipe\Entities\Models\Recipe;
use DailyRecipe\Entities\Models\Recipemenu;
use DailyRecipe\Entities\Models\Chapter;
use DailyRecipe\Entities\Models\Page;
use DailyRecipe\Exceptions\WhoopsDailyRecipePrettyHandler;
use DailyRecipe\Settings\Setting;
use DailyRecipe\Settings\SettingService;
use DailyRecipe\Util\CspService;
use GuzzleHttp\Client;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Contracts\Factory as SocialiteFactory;
use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Whoops\Handler\HandlerInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Set root URL
        $appUrl = config('app.url');
        if ($appUrl) {
            $isHttps = (strpos($appUrl, 'https://') === 0);
            URL::forceRootUrl($appUrl);
            URL::forceScheme($isHttps ? 'https' : 'http');
        }

        // Custom blade view directives
        Blade::directive('icon', function ($expression) {
            return "<?php echo icon($expression); ?>";
        });

        // Allow longer string lengths after upgrade to utf8mb4
        Schema::defaultStringLength(191);

        // Set morph-map due to namespace changes
        Relation::morphMap([
            'DailyRecipe\\Recipemenu' => Recipemenu::class,
            'DailyRecipe\\Recipe' => Recipe::class,
            'DailyRecipe\\Chapter' => Chapter::class,
            'DailyRecipe\\Page' => Page::class,
        ]);

        // View Composers
        View::composer('entities.breadcrumbs', BreadcrumbsViewComposer::class);

        // Set paginator to use bootstrap-style pagination
        Paginator::useBootstrap();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(HandlerInterface::class, function ($app) {
            return $app->make(WhoopsDailyRecipePrettyHandler::class);
        });

        $this->app->singleton(SettingService::class, function ($app) {
            return new SettingService($app->make(Setting::class), $app->make(Repository::class));
        });

        $this->app->singleton(SocialAuthService::class, function ($app) {
            return new SocialAuthService($app->make(SocialiteFactory::class), $app->make(LoginService::class));
        });

        $this->app->singleton(CspService::class, function ($app) {
            return new CspService();
        });

        $this->app->bind(HttpClientInterface::class, function ($app) {
            return new Client([
                'timeout' => 3,
            ]);
        });
    }
}
