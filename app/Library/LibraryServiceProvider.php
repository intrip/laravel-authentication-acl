<?php namespace LaravelAcl\Library;

use Illuminate\Support\ServiceProvider;
use LaravelAcl\Library\Email\SwiftMailer;
use LaravelAcl\Library\Form\FormModel;

class LibraryServiceProvider extends ServiceProvider {

  /**
   * Indicates if loading of the provider is deferred.
   *
   * @var bool
   */
  protected $defer = false;

  /**
   * Bootstrap the application events.
   *
   * @return void
   */
  public function boot()
  {
    $this->bindMailer();
    $this->bindFormModel();
  }

  protected function bindMailer()
  {
    $this->app->bind('jmailer', function ()
    {
      return new SwiftMailer;
    });
  }

  protected function bindFormModel()
  {
    $this->app->bind('form_model', function ($app) {
      return new FormModel();
    });
  }

  /**
   * Register the service provider.
   *
   * @return void
   */
  public function register()
  {
    //
  }

  /**
   * Get the services provided by the provider.
   *
   * @return array
   */
  public function provides()
  {
    return array();
  }

}
