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

it(' load normal dist from samples', function (): void {
    // NormalDist.from_samples([2.5, 3.1, 2.1, 2.4, 2.7, 3.5])
    // NormalDist(mu=2.716666666666667, sigma=0.5076087732365021)
    $samples = [2.5, 3.1, 2.1, 2.4, 2.7, 3.5];
    $normalDist = NormalDist::fromSamples($samples);

    expect(
        $normalDist->getMeanRounded(5),
    )->toEqual(2.71667);
    expect(
        $normalDist->getSigmaRounded(5),
    )->toEqual(0.50761);
});


it(' add to Normal Dist', function (): void {
    $birth_weights = NormalDist::fromSamples([2.5, 3.1, 2.1, 2.4, 2.7, 3.5]);
    $drug_effects = new NormalDist(0.4, 0.15);
    $combined = $birth_weights->add($drug_effects);
    expect(
        $combined->getMeanRounded(1),
    )->toEqual(3.1);
    expect(
        $combined->getSigmaRounded(1),
    )->toEqual(0.5);

    expect(
        $birth_weights->getMeanRounded(5),
    )->toEqual(2.71667);
    expect(
        $birth_weights->getSigmaRounded(5),
    )->toEqual(0.50761);


});
