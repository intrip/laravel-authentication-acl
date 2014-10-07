<?php  namespace Jacopo\Authentication\Tests\Unit;

class StateKeeperTest extends \PHPUnit_Framework_TestCase {

  /**
   * @test
   **/
  public function canSetData() {
    $value = 'value';
    $key = 'key';
    StateKeeper::set($key, $value);
  }

  /**
   * @test
   **/
  public function itReturnNullDataIfDoesntExists() {
    $value = StateKeeper::get('wrong_key');
    $this->assertNull($value);
  }

  /**
   * @test
   **/
  public function itGetHisData() {
    $set_value = 'value';
    $key = 'key';
    StateKeeper::set($key, $set_value);

    $get_value = StateKeeper::get($key);
    $this->assertEquals($get_value, $set_value);
  }

  /**
   * @test
   **/
  public function canClearHisData() {
    $set_value = 'value';
    $key = 'key';
    StateKeeper::set($key, $set_value);

    StateKeeper::clear();
    $get_value = StateKeeper::get($key);
    $this->assertNull($get_value);
  }
}
 