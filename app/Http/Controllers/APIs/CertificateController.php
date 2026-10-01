<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCertificateRequest;
use App\Http\Requests\UpdateCertificateRequest;
use App\Http\Resources\CertificateResource;
use App\Models\Certificate;
use App\Traits\HandlesFileUploads;
use App\Traits\HandlesResourceFiltering;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CertificateController extends Controller implements HasMiddleware
{
    use AuthorizesRequests, HandlesFileUploads, HandlesResourceFiltering;

    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = $request->user()?->profile?->certificates()->getQuery() ?? Certificate::query();

        match ($request->query('trashed')) {
            'with' => $query->withTrashed(),
            'only' => $query->onlyTrashed(),
            default => null,
        };

        $certificates = $this->getFilteredResults($request, $query, '', null, 15);

        return CertificateResource::collection($certificates);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCertificateRequest $request): JsonResponse|CertificateResource
    {
        // Get user profile
        $userProfile = $request->user()?->profile;

        // Check user profile
        if (! $userProfile) {
            return response()->json(['message' => 'Profile not found.'], 404);
        }

        // Get validated data
        $validated = $request->validated();

        // Upload file if exist
        if ($request->hasFile('file')) {
            $ext = strtolower($request->file('file')->extension());
            $folder = ($ext === 'pdf') ? Certificate::STORAGE_PDF_PATH : Certificate::STORAGE_IMAGE_PATH;
            $prefix = ($ext === 'pdf') ? 'DOC' : 'IMG';
            $customName = $prefix.'_'.time();

            $validated['file'] = $this->uploadFile($request, 'file', $folder, null, 'public', $customName);
        }

        // Create certificate
        $certificate = $userProfile->certificates()->create($validated);

        // Return as JSON
        return new CertificateResource($certificate);
    }

    /**
     * Display the specified resource.
     */
    public function show(Certificate $certificate): CertificateResource
    {
        return new CertificateResource($certificate);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCertificateRequest $request, Certificate $certificate): CertificateResource
    {
        // Get validated data
        $data = $request->validated();

        // Upload file if exist
        if ($request->hasFile('file')) {
            $idNum = preg_replace('/[^0-9]/', '', (string) $certificate->id);
            $ext = strtolower($request->file('file')->extension());
            $folder = ($ext === 'pdf') ? Certificate::STORAGE_PDF_PATH : Certificate::STORAGE_IMAGE_PATH;
            $customName = ($ext === 'pdf') ? 'DOC-'.$idNum.'_'.time() : 'IMG-'.$idNum.'_'.time();

            $data['file'] = $this->uploadFile(
                $request,
                'file',
                $folder,
                $certificate->file,
                'public',
                $customName
            );
        }

        // Update certificate
        $certificate->update($data);

        // Return as JSON
        return new CertificateResource($certificate);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Certificate $certificate): Response
    {
        $this->authorize('delete', $certificate);

        $request->boolean('force') ? $certificate->forceDelete() : $certificate->delete();

        return response()->noContent();
    }

    /**
     * Restore the specified soft-deleted resource.
     */
    public function restore(string $id): CertificateResource
    {
        // Find deleted certificate by ID
        $certificate = Certificate::onlyTrashed()->findOrFail($id);

        // Authorize user
        $this->authorize('restore', $certificate);

        // Restore certificate
        $certificate->restore();

        // Return as JSON
        return new CertificateResource($certificate);
    }
}
