<?php

test('ensures no debugging')
    ->expect(['dd', 'dump', 'echo', 'print_r'])
    ->not->toBeUsed();

test('to be final')
    ->expect('HiFolks\Statistics')
    ->classes()
    ->not->toBeFinal();

test('make')
    ->expect('HiFolks\Statistics\Statistics')
    ->toHaveMethod('make');

test('constructor')
    ->expect('HiFolks\Statistics\Statistics')
    ->toHaveConstructor();

/*
test('strict')
    ->expect('HiFolks\Statistics')
    ->toUseStrictTypes();
*/
