<?php  namespace Jacopo\Authentication\Tests\Unit;

class StateKeeper {
  protected static $data = [];

  public static function set($key, $value) {
    self::$data[$key] = $value;
  }

  public static function get($key) {
    return isset(self::$data[$key]) ? self::$data[$key] : null;
  }

  public static function clear() {
    static::$data = [];
  }
}