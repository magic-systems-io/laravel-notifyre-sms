<?php

namespace MagicSystemsIO\Notifyre\Tests;

pest()->extend(TestCase::class)
    ->in('Feature');


expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});


function something()
{
    // ..
}
