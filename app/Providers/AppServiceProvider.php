<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Ciconia\Ciconia;
use Ciconia\Extension\Gfm;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->share('categories', config('site.postCategories'));
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Ciconia::class, function() {
            $markdown = new Ciconia();
            $markdown->addExtension(new Gfm\FencedCodeBlockExtension());
            $markdown->addExtension(new Gfm\TaskListExtension());
            $markdown->addExtension(new Gfm\InlineStyleExtension());
            $markdown->addExtension(new Gfm\WhiteSpaceExtension());
            $markdown->addExtension(new Gfm\TableExtension());
            $markdown->addExtension(new Gfm\UrlAutoLinkExtension());
            return $markdown;
        });
    }
}
