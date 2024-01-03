<?php

namespace base\common\service;

use Exception;

class TestServiceBase
{
    public const NAME = null;

    public const DESC = null;

    public const RUN = 'run';

    public function getName()
    {
        if ($this::NAME === null) {
            throw new Exception('name is not set');
        }

        return $this::NAME;
    }

    public function getDesc()
    {
        if ($this::DESC === null) {
            throw new Exception('desc is not set');
        }

        return $this::DESC;
    }
}
