<?php

namespace HiFolks\Statistics\Enums;

enum KdeKernel: string
{
    case Normal = 'normal';
    case Gauss = 'gauss';
    case Logistic = 'logistic';
    case Sigmoid = 'sigmoid';
    case Rectangular = 'rectangular';
    case Uniform = 'uniform';
    case Triangular = 'triangular';
    case Parabolic = 'parabolic';
    case Epanechnikov = 'epanechnikov';
    case Quartic = 'quartic';
    case Biweight = 'biweight';
    case Triweight = 'triweight';
    case Cosine = 'cosine';

    public function resolve(): self
    {
        return match ($this) {
            self::Gauss => self::Normal,
            self::Uniform => self::Rectangular,
            self::Epanechnikov => self::Parabolic,
            self::Biweight => self::Quartic,
            default => $this,
        };
    }
}
