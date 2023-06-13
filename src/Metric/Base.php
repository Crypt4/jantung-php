<?php

namespace Crypt4\Jantung\Metric;

use Crypt4\Jantung\Support\Arr;

abstract class Base implements Contract
{
    public function metrics(): array
    {
        return [];
    }

    public function toArray(): array
    {
        return Arr::undot(
            $this->metrics()
        );
    }
}
