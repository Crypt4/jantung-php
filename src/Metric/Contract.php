<?php

namespace Crypt4\Jantung\Metric;

interface Contract
{
    public function metrics(): array;

    public function toArray(): array;
}
