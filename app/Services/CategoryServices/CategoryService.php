<?php

namespace App\Services\CategoryServices;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{

    public function createCategory(array $data, Category $categoryModel): Category
    {
        return $categoryModel::create($this->categoryData($data));
    }


    public function editCategory(Category $category, array $data): void
    {
        $category->update($this->categoryData($data));
    }


    public function deleteCategory(Category $category): void
    {
        $category->delete();
    }


    public function getCategoryById(int $id, Category $categoryModel)
    {
        return $categoryModel::where('id', $id)->first();
    }

    public function getCategories(Category $categoryModel): Collection
    {
        return $categoryModel::all();
    }

    public function categoryData($data): array
    {
        return [
            'name' => $data['name'],
            'user_id' => $data['user_id'],
        ];
    }
}
