<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class NameDataTransformer implements DataTransformerInterface
{
    public function transform($chaineMajuscules)
    {
        return ucwords(strtolower($chaineMajuscules));
    }

    public function reverseTransform($chaineClassique)
    {
        return strtoupper($chaineClassique);
    }
}
