<?php

namespace MagicSystemsIO\Notifyre\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class Controller
{
    /**
     * Display a listing of the resource, paginated.
     */
    protected function paginate(Request $request, array $data): array
    {
        $perPage = $request->query('perPage', 15);
        $page = $request->query('page', 1);
        $collection = collect($data);

        $paginator = new LengthAwarePaginator(
            $collection->forPage($page, $perPage),
            $collection->count(),
            $perPage,
            $page,
            ['path' => $request->url()]
        );

        return [
            'items' => array_values($paginator->items()),
            'pagination' => [
                'firstPage' => 1,
                'currentPage' => $paginator->currentPage(),
                'lastPage' => $paginator->lastPage(),
                'firstPageUrl' => $paginator->url(1),
                'lastPageUrl' => $paginator->url($paginator->lastPage()),
                'perPage' => $paginator->perPage(),
                'nextPageUrl' => $paginator->nextPageUrl(),
                'prevPageUrl' => $paginator->previousPageUrl(),
                'total' => $paginator->total(),
                'hasMorePages' => $paginator->hasPages(),
            ],
        ];
    }
}
