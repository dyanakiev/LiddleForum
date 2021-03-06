<?php

namespace LiddleDev\LiddleForum\Controllers\Admin;

use Validator;
use Illuminate\Http\Request;
use LiddleDev\LiddleForum\Models\Category;

class CategoriesController extends BaseAdminController
{
    public function getCreate()
    {
        $categories = Category::orderBy('parent_id')->get();

        return view('liddleforum::admin.categories.create', [
            'categories' => $categories,
        ]);
    }

    public function postCreate(Request $request)
    {
        $categoryObject = new Category();

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:' . $categoryObject->getTable() . ',slug',
            'order' => 'integer',
            // TODO can't do this because selecting the placeholder fails validation when I want to set parent id to null
            //'parent_id' => 'exists:' . $categoryObject->getTable() . ',id',
        ]);

        if ($validator->fails()) {
            return redirect()->route('liddleforum.admin.categories.create')
                ->withErrors($validator, 'liddleforum')
                ->withInput();
        }

        // Check if parent category actually exists
        $parentId = $request->input('parent_id') ? $request->input('parent_id') : null;
        if ($parentId !== null && ! Category::where('id', '=', $parentId)->first()) {
            $request->session()->flash('liddleforum_error', 'Invalid parent category');
            return redirect()->route('liddleforum.admin.categories.create');
        }

        $category = Category::create([
            'name' => $request->input('name'),
            'slug' => $request->input('slug'),
            'order' => $request->input('order'),
            'parent_id' => $parentId,
        ]);

        $request->session()->flash('liddleforum_success', 'Your category has been created');
        return redirect()->route('liddleforum.admin.categories.create');
    }

    public function getEdit()
    {
        $categories = Category::orderBy('parent_id')->get();
        $baseCategories = Category::whereNull('parent_id')->get();

        return view('liddleforum::admin.categories.edit', [
            'categories' => $categories,
            'baseCategories' => $baseCategories,
        ]);
    }

    public function postEdit(Request $request)
    {
        $categoryObject = new Category();

        $rules = [];
        $enteredSlugs = []; // Used to make sure all inputted slugs differ
        foreach($request->input('categories') as $id => $values)
        {
            $rules['categories.' . $id . '.name'] = 'required';
            $rules['categories.' . $id . '.slug'] = 'required|unique:' . $categoryObject->getTable() . ',slug,' . $id;
            $rules['categories.' . $id . '.order'] = 'required|integer';
            $rules['categories.' . $id . '.parent_id'] = 'exists:' . $categoryObject->getTable() . ',id';

            if (isset($values['slug'])) {
                $enteredSlugs[] = $values['slug'];
            }
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->route('liddleforum.admin.categories.edit')
                ->withErrors($validator, 'liddleforum')
                ->withInput();
        }

        // Check for duplicate entered slugs as validation won't have picked it up
        if (count($enteredSlugs) !== count(array_unique($enteredSlugs))) {
            $request->session()->flash('liddleforum_error', 'Your entered slugs must be unique');
            return redirect()->route('liddleforum.admin.categories.edit')
                ->withInput();
        }

        foreach($request->input('categories') as $id => $values)
        {
            if ($category = Category::where('id', '=', $id)->first()) {
                $values = array_filter(array_intersect_key($values, array_flip(['name', 'slug', 'order', 'parent_id'])));
                $category->update($values);
            }
        }

        $request->session()->flash('liddleforum_success', 'Categories have been updated');

        return redirect()->route('liddleforum.admin.categories.edit');
    }

    public function getDelete()
    {
        $categories = Category::orderBy('parent_id')->get();

        return view('liddleforum::admin.categories.delete', [
            'categories' => $categories,
        ]);
    }

    public function deleteCategory(Request $request)
    {
        // Check if category actually exists
        $categoryId = $request->input('category_id') ? $request->input('category_id') : null;
        if ($categoryId === null || ! $category = Category::where('id', '=', $categoryId)->first()) {
            $request->session()->flash('liddleforum_error', 'Invalid category');
            return redirect()->route('liddleforum.admin.categories.delete');
        }

        $category->delete();

        $request->session()->flash('liddleforum_success', 'Category and its threads has been deleted');
        return redirect()->route('liddleforum.admin.categories.delete');
    }

}