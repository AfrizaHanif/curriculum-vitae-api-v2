<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;

trait HandlesResourceFiltering
{
    /**
     * Apply optional filters from query parameters and return the result collection or paginator.
     *
     * @param  string|array  $filterParam  The query parameter key (string) or an array of filter definitions.
     * @param  string|null  $filterColumn  The database column to filter (when $filterParam is a string).
     * @param  int|null  $perPage  The number of items per page. If provided or requested via URL, returns a paginator.
     */
    protected function getFilteredResults(Request $request, Builder|Relation|null $query, string|array $filterParam = '', ?string $filterColumn = null, ?int $perPage = null): mixed
    {
        if (! $query) {
            return collect();
        }

        if (is_array($filterParam)) {
            $this->applyMultipleFilters($request, $query, $filterParam);
        } elseif ($filterParam !== '' && ($value = $request->query($filterParam))) {
            $query->where($filterColumn ?? $filterParam, $value);
        }

        // Return unpaginated results if requested via query string (?all=true or ?paginate=false)
        if ($request->boolean('all') || ($request->has('paginate') && ! $request->boolean('paginate'))) {
            return $query->get();
        }

        // Client per_page query parameter overrides default $perPage
        $requestedPerPage = $request->query('per_page');
        $effectivePerPage = $requestedPerPage !== null ? (int) $requestedPerPage : $perPage;

        // Return paginated results if perPage is provided or if pagination is requested via query string
        if ($effectivePerPage !== null || $request->has('page')) {
            return $query->paginate($effectivePerPage ?? 15);
        }

        return $query->get();
    }

    /**
     * Apply an array of filters to the query.
     */
    protected function applyMultipleFilters(Request $request, Builder|Relation $query, array $filters): void
    {
        foreach ($filters as $param => $config) {
            $paramName = is_numeric($param) ? $config : $param;
            $value = $request->query($paramName);

            if ($value === null || $value === '') {
                continue;
            }

            $column = is_array($config) ? ($config['column'] ?? $paramName) : (is_numeric($param) ? $paramName : $config);
            $type = is_array($config) ? ($config['type'] ?? 'exact') : 'exact';

            if ($type === 'json' || $type === 'json_contains') {
                $values = is_array($value) ? $value : array_filter(array_map('trim', explode(',', (string) $value)));

                if (! empty($values)) {
                    $query->where(function ($sub) use ($column, $values) {
                        foreach ($values as $item) {
                            $sub->orWhere(function ($inner) use ($column, $item) {
                                $inner->whereJsonContains($column, $item)
                                    ->orWhereJsonContains($column, strtolower($item))
                                    ->orWhereJsonContains($column, ucfirst(strtolower($item)))
                                    ->orWhereJsonContains($column, strtoupper($item));
                            });
                        }
                    });
                }
            } else {
                $query->where($column, $value);
            }
        }
    }
}
