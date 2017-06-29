<?php

namespace winwin\mbupay;

use Dotenv\Dotenv;

class TestCase extends \PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        date_default_timezone_set('Asia/Shanghai');
        if (file_exists(__DIR__.'/.env')) {
            (new Dotenv(__DIR__))->load();
        }
    }
}
