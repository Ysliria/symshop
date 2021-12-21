<?php

namespace App\Taxes;

class Detector
{
    protected $seuil;

    public function __construct(float $seuil)
    {
        $this->seuil = $seuil;
    }

    public function detect(float $price): bool
    {
        return $price > $this->seuil;
    }
}
