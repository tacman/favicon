<?php

declare(strict_types=1);

namespace Genkgo\Favicon;

final class AggregatePackage implements PackageAppendInterface
{
    /**
     * @param iterable<int|string, PackageAppendInterface> $generators
     */
    public function __construct(private readonly iterable $generators)
    {
    }

    /**
     * @return \Generator<string, string>
     */
    public function package(): \Generator
    {
        foreach ($this->generators as $generator) {
            yield from $generator->package();
        }
    }
}