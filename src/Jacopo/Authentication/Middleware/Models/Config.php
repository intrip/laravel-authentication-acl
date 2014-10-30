<?php namespace Jacopo\Authentication\Middleware\Models;

use Illuminate\Database\Eloquent\Model;

class Config extends Model{
    protected $table = 'test_config';

    protected $fillable = ['key','value'];

    public $timestamps = false;
} 