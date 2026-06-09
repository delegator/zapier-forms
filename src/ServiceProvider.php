<?php

namespace Delegator\StatamicZapier;

use Statamic\Stache\Stache;
use Statamic\Facades\CP\Nav;
use Statamic\Facades\Permission;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $listen = [
        'Statamic\Events\FormSubmitted' => [
            'Delegator\StatamicZapier\Listeners\PushToWebhook',
        ],
    ];

    protected $routes = [
        'cp' => __DIR__ . '/../routes/cp.php',
    ];

    public function boot()
    {
        parent::boot();

        // load publishables
        $this->bootPublishables();

        // load navigation
        $this->bootNavigation();

        // permissions
        Permission::group('statamic-zapier', 'Statamic Zapier', function () {
            Permission::register('configure form zapier webhooks')->label('Configure Webhooks');
        });
    }

    public function bootPublishables(): static
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/statamic/zapier.php' => config_path('/statamic/zapier.php'),
            ], 'config');
        }

        return $this;
    }

    private function bootNavigation(): void
    {
        Nav::extend(function ($nav) {
            $nav->tools('Statamic Zapier')
                ->can('configure form zapier webhooks')
                ->route('statamic-zapier.index')
                ->icon('hierarchy-hub-integration-connection');
        });
    }
}
