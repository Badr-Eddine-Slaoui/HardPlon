<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LanguageRequest;
use App\Models\Language;
use Laravel\Octane\Facades\Octane;

class LanguageController extends Controller
{

    public function index()
    {
        try {

            $perPage = request()->get('per_page', 5);

            [$languages, $archive_languages] = Octane::concurrently([
                fn() => Language::withoutTrashed()->paginate($perPage),
                fn() => Language::onlyTrashed()->paginate($perPage),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Successfully fetched languages.',
                'data' => compact('languages', 'archive_languages'),
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => "Something went wrong. Please try again.",
                'code' => $e->getCode(),
            ], 500);
        }
    }

    public function getAllLanguages()
    {
        try {
            $languages = Language::withoutTrashed()->get();

            return response()->json([
                'success' => true,
                'message' => 'Successfully fetched all languages.',
                'data' => $languages,
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => "Something went wrong. Please try again.",
                'code' => $e->getCode(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LanguageRequest $request)
    {
        try {
            $language = Language::create([
                'name' => $request->name,
                'docker_image' => $request->docker_image,
                'compile_command' => $request->compile_command,
                'run_command' => $request->run_command
            ]);

            if (!$language) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Failed to create language. Please try again.'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Language created successfully.',
                'data' => compact('language')
            ], 201);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => "Something went wrong. Please try again.",
                'code' => $e->getCode(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        try {
            $language = Language::withTrashed()->find($id);

            if (!$language) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Language not found.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Successfully fetched language.',
                'data' => compact('language'),
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => "Something went wrong. Please try again.",
                'code' => $e->getCode(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LanguageRequest $request, int $id)
    {
        try {
            $language = Language::withTrashed()->find($id);

            if (!$language) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Language not found.'
                ], 404);
            }

            $is_updated = $language->update([
                'name' => $request->name,
                'docker_image' => $request->docker_image,
                'compile_command' => $request->compile_command,
                'run_command' => $request->run_command
            ]);

            if (!$is_updated) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Failed to update language. Please try again.'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Language updated successfully.',
                'data' => compact('language')
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => "Something went wrong. Please try again.",
                'code' => $e->getCode(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        try {
            $language = Language::withTrashed()->find($id);

            if (!$language) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Language not found.'
                ], 404);
            }

            $is_deleted = $language->delete();

            if ($is_deleted) {
                return response()->json([
                    'success' => true,
                    'data' => compact('language'),
                    'message' => 'Successfully deleted language.'
                ], 200);
            }

            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to delete language. Please try again.'
            ], 400);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => "Something went wrong. Please try again.",
                'code' => $e->getCode(),
            ], 500);
        }
    }
    /**
     * Restore the specified resource from storage.
     */
    public function restore(int $id)
    {
        try {
            $language = Language::onlyTrashed()->find($id);

            if (!$language) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Language not found.'
                ], 404);
            }

            $is_restored = $language->restore();

            if ($is_restored) {
                return response()->json([
                    'success' => true,
                    'data' => compact('language'),
                    'message' => 'Successfully restored language.'
                ], 200);
            }

            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to restore language. Please try again.'
            ], 400);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => "Something went wrong. Please try again.",
                'code' => $e->getCode(),
            ], 500);
        }
    }
}
