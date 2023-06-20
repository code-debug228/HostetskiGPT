<?php

namespace Modules\HostetskiGPT\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use App\Thread;
use Nwidart\Modules\Facades\Module;

class HostetskiGPTServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->hooks();
    }

    /**
     * Module hooks.
     */
    public function hooks()
    {
        // Add module's JS file to the application layout.
        \Eventy::addFilter('javascripts', function($javascripts) {
            array_push($javascripts, \Module::getPublicPath("hostetskigpt").'/js/module.js');
            return $javascripts;
        });

        // JavaScript in the bottom
        \Eventy::addAction('javascript', function() {
            $version = Module::find('hostetskigpt')->get('version');
            $copiedToClipboard = __("Copied to clipboard");
            $updateAvailable = __('Update available for module ');
            echo "const hostetskiGPTData = {" .
                    "'copiedToClipboard': '{$copiedToClipboard}'," .
                    "'updateAvailable': '{$updateAvailable}'," .
                    "'version': '{$version}'," .
                "};";
            echo 'hostetskigptInit();';
        });

        \Eventy::addAction('mailboxes.settings.menu', function($mailbox) {
            if (auth()->user()->isAdmin()) {
                echo \View::make('hostetskigpt::partials/settings_menu', ['mailbox' => $mailbox])->render();
            }
        }, 80);

        \Eventy::addAction('thread.menu', function ($thread) {
            if ($thread->type == Thread::TYPE_LINEITEM) {
                return;
            }
            ?>
            <li><a class="chatgpt-get" href="#" target="_blank" onclick='generateAnswer(event)' role="button"><?php echo __("Generate answer (GPT)")?></a></li>
            <?php
        }, 100);

        \Eventy::addAction('reply_form.after', function(){
            echo 'HIER!';
        }, 100);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerTranslations();
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__.'/../Config/config.php' => config_path('hostetskigpt.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'hostetskigpt'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/hostetskigpt');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/hostetskigpt';
        }, \Config::get('view.paths')), [$sourcePath]), 'hostetskigpt');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $this->loadJsonTranslationsFrom(__DIR__ .'/../Resources/lang');
    }

    /**
     * Register an additional directory of factories.
     * @source https://github.com/sebastiaanluca/laravel-resource-flow/blob/develop/src/Modules/ModuleServiceProvider.php#L66
     */
    public function registerFactories()
    {
        if (! app()->environment('production')) {
            app(Factory::class)->load(__DIR__ . '/../Database/factories');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
