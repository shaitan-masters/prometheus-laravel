<?php

namespace Mojam\Prometheus;

interface Adapter
{
    public const COMMAND_INCREMENT_INTEGER = 1;
    public const COMMAND_INCREMENT_FLOAT = 2;
    public const COMMAND_SET = 3;

    public function updateCounter(Counter $counter, int $value, int $command);

    public function collect(): array;
}
