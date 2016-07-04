<?php
namespace Nodes\Api\Scaffolding;

use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use Nodes\Api\Scaffolding\Console\Commands\ResetPassword;
use Nodes\Api\Scaffolding\Console\Commands\Scaffolding;

/**
 * Class ServiceProvider
 *
 * @package Nodes\Api\Scaffolding
 */
class ServiceProvider extends IlluminateServiceProvider
{
    /**
     * Artisan commands
     *
     * @var array
     */
    protected $commands = [
        ResetPassword::class,
        Scaffolding::class
    ];

    /**
     * Register the service provider
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return void
     */
    public function register()
    {
        // Register Artisan commands
        $this->commands($this->commands);
    }
}