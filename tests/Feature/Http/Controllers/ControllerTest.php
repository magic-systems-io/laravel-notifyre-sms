<?php

use Illuminate\Http\Request;
use MagicSystemsIO\Notifyre\Http\Controllers\Controller;

it('paginates data using default perPage and page', function () {
    $data = range(1, 30);

    $tester = new class () extends Controller
    {
        public function run(Request $request, array $data)
        {
            return $this->paginate($request, $data);
        }
    };

    $request = Request::create('/test');

    $result = $tester->run($request, $data);

    expect($result)->toBeArray()
        ->and($result['items'])->toHaveCount(15)
        ->and($result['items'])->toEqual(range(1, 15))
        ->and($result['pagination']['firstPage'])->toBe(1)
        ->and($result['pagination']['currentPage'])->toBe(1)
        ->and($result['pagination']['lastPage'])->toBe(2)
        ->and($result['pagination']['perPage'])->toBe(15)
        ->and($result['pagination']['total'])->toBe(30)
        ->and($result['pagination']['prevPageUrl'])->toBeNull()
        ->and($result['pagination']['nextPageUrl'])->not->toBeNull();
});

it('paginates data using custom perPage and page query params', function () {
    $data = range(1, 25);

    $tester = new class () extends Controller
    {
        public function run(Request $request, array $data)
        {
            return $this->paginate($request, $data);
        }
    };

    $request = Request::create('/test', 'GET', ['perPage' => 10, 'page' => 3]);

    $result = $tester->run($request, $data);

    expect($result)->toBeArray()
        ->and($result['items'])->toHaveCount(5)
        ->and($result['items'])->toEqual(range(21, 25))
        ->and($result['pagination']['currentPage'])->toBe(3)
        ->and($result['pagination']['lastPage'])->toBe(3)
        ->and($result['pagination']['perPage'])->toBe(10)
        ->and($result['pagination']['total'])->toBe(25)
        ->and($result['pagination']['nextPageUrl'])->toBeNull()
        ->and($result['pagination']['prevPageUrl'])->not->toBeNull()
        ->and(strpos($result['pagination']['firstPageUrl'], '/test') !== false)->toBeTrue()
        ->and(strpos($result['pagination']['lastPageUrl'], 'page=3') !== false)->toBeTrue();
});
