<?php

namespace hiqdev\hiart\tests;

class Mock {
    public $name;
    public $args;
    public $result;

    public function __construct($result)
    {
        $this->result = $result;
    }

    public function __call($name, $args)
    {
        $this->name = $name;
        $this->args = $args;
        return $this->result;
    }
}
