<?php

use HiFolks\Statistics\Math;

it('is odd (static)', function () {
    expect(Math::isOdd(1))->toBeTrue();
    expect(Math::isOdd(0))->toBeFalse();
    expect(Math::isOdd(-5))->toBeTrue();
    expect(Math::isOdd(-2))->toBeFalse();
});
