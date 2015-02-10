<?php  namespace Jacopo\Library\Traits; 
use App;
/**
 * Trait ConnectionTrait
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
trait ConnectionTrait {
  public function getConnectionName()
  {
    return (App::environment() != 'testing') ? 'authentication' : 'testbench';
  }
} 