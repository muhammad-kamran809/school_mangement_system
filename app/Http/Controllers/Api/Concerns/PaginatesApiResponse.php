<?php

namespace App\Http\Controllers\Api\Concerns;

use Illuminate\Contracts\Pagination\LengthAwarePaginator as LengthAwarePaginatorContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

trait PaginatesApiResponse
{
    /**
     * Paginate query or return all records based on request parameters.
     */
    protected function paginateResponse(
        Builder|Relation|QueryBuilder|Collection $query,
        Request $request,
        int $defaultPerPage = 10,
        ?string $message = null,
        array $extraData = []
    ): JsonResponse {
        $shouldPaginate = ! $request->boolean('all')
            && ! in_array(strtolower((string) $request->query('paginate')), ['false', '0', 'no'], true)
            && strtolower((string) $request->query('per_page')) !== 'all';

        if (! $shouldPaginate) {
            $items = $query instanceof Collection ? $query->values() : $query->get();

            $response = array_merge([
                'success' => true,
            ], $extraData);

            if ($message !== null) {
                $response['message'] = $message;
            }

            $response['data'] = $items;
            $response['pagination'] = null;
            $response['meta'] = null;
            $response['total'] = $items->count();

            return response()->json($response);
        }

        $perPageInput = $request->input('per_page')
            ?? $request->input('perPage')
            ?? $request->input('limit')
            ?? $defaultPerPage;

        $perPage = (int) $perPageInput;
        if ($perPage <= 0) {
            $perPage = $defaultPerPage;
        }
        $perPage = min($perPage, 100);

        if ($query instanceof Collection) {
            $currentPage = max(1, (int) $request->input('page', 1));
            $total = $query->count();
            $slice = $query->forPage($currentPage, $perPage)->values();

            $paginator = new LengthAwarePaginator(
                $slice,
                $total,
                $perPage,
                $currentPage,
                [
                    'path' => $request->url(),
                    'query' => $request->query(),
                ]
            );
        } else {
            /** @var LengthAwarePaginatorContract $paginator */
            $paginator = $query->paginate($perPage)->withQueryString();
        }

        $paginationMeta = [
            'total' => $paginator->total(),
            'count' => $paginator->count(),
            'per_page' => $paginator->perPage(),
            'current_page' => $paginator->currentPage(),
            'total_pages' => $paginator->lastPage(),
            'last_page' => $paginator->lastPage(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
            'has_more_pages' => $paginator->hasMorePages(),
            'prev_page_url' => $paginator->previousPageUrl(),
            'next_page_url' => $paginator->nextPageUrl(),
        ];

        $response = array_merge([
            'success' => true,
        ], $extraData);

        if ($message !== null) {
            $response['message'] = $message;
        }

        $response['data'] = $paginator->items();
        $response['pagination'] = $paginationMeta;
        $response['meta'] = $paginationMeta;
        $response['current_page'] = $paginator->currentPage();
        $response['last_page'] = $paginator->lastPage();
        $response['per_page'] = $paginator->perPage();
        $response['total'] = $paginator->total();

        return response()->json($response);
    }
}
