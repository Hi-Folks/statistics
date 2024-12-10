<?php

use HiFolks\Statistics\NormalDist;

it(' init normal dist', function (): void {
    $nd = new NormalDist(1060, 195);
    expect(
        $nd->getMean(),
    )->toEqual(1060);
    expect(
        $nd->getSigma(),
    )->toEqual(195);

});
it('can calculate normal dist cdf', function (): void {
    $nd = new NormalDist(1060, 195);
    expect(
        round($nd->cdf(1200 + 0.5) - $nd->cdf(1100 - 0.5), 3),
    )->toEqual(0.184);
});

it('can calculate normal dist pdf', function (): void {
    $nd = new NormalDist(10, 2);
    expect(
        $nd->pdfRounded(12, 3),
    )->toEqual(0.121);
    expect(
        $nd->pdfRounded(12, 2),
    )->toEqual(0.12);
});
