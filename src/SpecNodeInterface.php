<?php

declare(strict_types=1);

namespace Lucite\ApiSpec;

interface SpecNodeInterface
{
    public function finalize(): array;
}
