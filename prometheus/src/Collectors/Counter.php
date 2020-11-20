<?php

namespace Mojam\Prometheus\Collectors;

use Mojam\Prometheus\Storage\Adapter;

class Counter extends Collector
{
    public const TYPE = 'counter';

    public function getType(): string
    {
        return self::TYPE;
    }

    public function inc(): void
    {
        $this->incBy(1);
    }

    public function incBy(int $value): void
    {
        $this->assertLabelsAreDefinedCorrectly();

        $this->storageAdapter->updateCounter(
            $this,
            $value,
            Adapter::COMMAND_INCREMENT_INTEGER
        );
    }
}
