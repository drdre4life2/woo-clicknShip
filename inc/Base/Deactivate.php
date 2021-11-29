<?php

/**
 * @package Thefirst
 */
namespace Inc\Base;

class Deactivate
{
    public function deactivate(){
        flush_rewrite_rules();
    }
}