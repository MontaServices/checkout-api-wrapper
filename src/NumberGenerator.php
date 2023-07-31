<?php

namespace Monta\MontaProcessing;

class NumberGenerator
{
    public function Generate(int $min, int $max): int
    {
        return rand($min, $max);
    }
}