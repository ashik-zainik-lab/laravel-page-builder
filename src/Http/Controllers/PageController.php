<?php

namespace Coderstm\PageBuilder\Http\Controllers;

use Coderstm\PageBuilder\Http\Resources\PageResource;
use Coderstm\PageBuilder\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PageController extends Controller
{
    public function index(Request $request)
    {
        $page = Page::query();

        if ($request->filled('filter')) {
            $page->where(function ($query) use ($request) {
                $query->where('title', 'like', "%{$request->filter}%");
            });
        }

        if ($request->boolean('option')) {
            $page->select('id', 'title');
        }

        if ($request->boolean('active')) {
            $page->onlyActive();
        }

        if ($request->boolean('deleted')) {
            $page->onlyTrashed();
        }

        $page = $page->orderBy($request->sortBy ?? 'created_at', $request->direction ?? 'desc')
            ->paginate($request->rowsPerPage ?? 15);

        return PageResource::collection($page);
    }

    public function store(Request $request)
    {
        $rules = [
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'template' => 'nullable|string|max:100',
            'is_active' => 'nullable|boolean',
        ];

        $request->validate($rules);

        $page = Page::create($request->only([
            'title',
            'slug',
            'meta_title',
            'meta_keywords',
            'meta_description',
            'template',
            'is_active',
        ]));

        return response()->json([
            'data' => $page,
            'message' => __('Page has been created successfully!'),
        ], 200);
    }

    public function show($page)
    {
        $page = Page::withTrashed()->findOrFail($page);

        return response()->json($page, 200);
    }

    public function update(Request $request, Page $page)
    {
        $rules = [
            'title' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|nullable|string|max:255',
            'meta_title' => 'sometimes|nullable|string|max:255',
            'meta_keywords' => 'sometimes|nullable|string|max:255',
            'meta_description' => 'sometimes|nullable|string|max:255',
            'template' => 'sometimes|nullable|string|max:100',
            'is_active' => 'sometimes|nullable|boolean',
        ];

        $request->validate($rules);

        $page->update($request->only([
            'title',
            'slug',
            'meta_title',
            'meta_keywords',
            'meta_description',
            'template',
            'is_active',
        ]));

        return response()->json([
            'data' => $page->fresh(),
            'message' => __('Page has been updated successfully!'),
        ], 200);
    }

    public function destroy(Page $page)
    {
        $page->delete();

        return response()->json([
            'message' => __('Page has been deleted successfully!'),
        ], 200);
    }

    public function changeActive(Request $request, Page $page)
    {
        $page->update(['is_active' => ! $page->is_active]);

        return response()->json([
            'message' => $page->is_active ? __('Page has been marked as active successfully!') : __('Page has been marked as deactivated successfully!'),
        ], 200);
    }
}
