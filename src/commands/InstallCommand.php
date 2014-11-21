<?php

use Illuminate\Console\Command;
use Jacopo\Authentication\Seeds\DbSeeder;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class InstallCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'authentication:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Laravel Authentication ACL package.';

    protected $call_wrapper;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct($call_wrapper = null, $db_seeder = null)
    {
        $this->call_wrapper = $call_wrapper ? $call_wrapper : new CallWrapper($this);

        $this->db_seeder = $db_seeder ? $db_seeder : new DbSeeder();
        
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $this->call_wrapper->call('migrate', ['--package' => 'jacopo/laravel-authentication-acl', '--database' => "authentication" ] );

        $this->db_seeder->run();

        $this->call_wrapper->call('asset:publish');

        $this->info('## Laravel Authentication ACL Installed successfully ##');
    }

}
