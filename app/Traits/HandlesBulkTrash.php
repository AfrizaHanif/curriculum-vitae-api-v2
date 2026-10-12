<?php

namespace App\Traits;

use App\Models\Profile;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

trait HandlesBulkTrash
{
    /**
     * Restore all soft-deleted resources for the authenticated user's profile.
     */
    public function restoreAll(Request $request): JsonResponse
    {
        $userProfile = $request->user()?->profile;

        if (! $userProfile) {
            return response()->json(['message' => 'Profile not found.'], 404);
        }

        $query = $this->getTrashedItemsQueryForProfile($userProfile);

        if ($request->has('ids') && is_array($request->input('ids'))) {
            $query->whereIn('id', $request->input('ids'));
        }

        $items = $query->get();
        $count = $items->count();

        $items->each(function ($item): void {
            $item->restore();
        });

        return response()->json([
            'message' => "Successfully restored {$count} soft-deleted record(s).",
            'count' => $count,
        ]);
    }

    /**
     * Permanently delete all soft-deleted resources for the authenticated user's profile.
     */
    public function forceDeleteAll(Request $request): JsonResponse
    {
        $userProfile = $request->user()?->profile;

        if (! $userProfile) {
            return response()->json(['message' => 'Profile not found.'], 404);
        }

        $query = $this->getTrashedItemsQueryForProfile($userProfile);

        if ($request->has('ids') && is_array($request->input('ids'))) {
            $query->whereIn('id', $request->input('ids'));
        }

        $items = $query->get();
        $count = $items->count();

        $items->each(function ($item): void {
            $item->forceDelete();
        });

        return response()->json([
            'message' => "Successfully permanently deleted {$count} soft-deleted record(s).",
            'count' => $count,
        ]);
    }

    /**
     * Get the query for soft-deleted items belonging to the user's profile.
     */
    protected function getTrashedItemsQueryForProfile(Profile $userProfile): Builder|Relation
    {
        $relation = $this->getBulkTrashRelationName();

        return $userProfile->{$relation}()->onlyTrashed();
    }

    /**
     * Get the relation name on Profile for bulk trash operations.
     */
    protected function getBulkTrashRelationName(): string
    {
        $baseName = str_replace('Controller', '', class_basename(static::class));

        return Str::camel(Str::plural($baseName));
    }
}

