<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Services\CategoryServices\CategoryService;

class CategoryController extends Controller
{
    private Category $categoryModel;
    private CategoryService $categoryService;

    public function __construct(CategoryService $categoryService, Category $categoryModel)
    {
        $this->categoryModel = $categoryModel;
        $this->categoryService = $categoryService;
    }

    public function index()
    {
        return $this->categoryModel::paginate(10);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        $category = $this->categoryService->createCategory(
            $request->validated(),
            $this->categoryModel,
        );

        return response($category);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $category = $this->categoryService->getCategoryById($id, $this->categoryModel);
        if (!$category) {
            return response([
                "message" => "Not Found",
            ], 404);
        }
        return response($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, int $id)
    {
        $category = $this->categoryService->getCategoryById($id, $this->categoryModel);
        if (!$category) {
            return response([
                "message" => "Not Found",
            ], 404);
        }

        $this->categoryService->editCategory(
            $category,
            $request->validated(),
        );
        return response($category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $category = $this->categoryService->getCategoryById($id, $this->categoryModel);
        if (!$category) {
            return response([
                "message" => "Not Found",
            ], 404);
        }

        $this->categoryService->deleteCategory(
            $category,
        );
        return response([
            "message" => "success",
        ]);
    }
}
