<?php

namespace App\Http\Controllers;

use \App\Cookbook;
use \App\Author;
use \App\Category;
use \App\Recipe;
use Request;
use App\Http\Requests\CreateRecipe;
use Auth;

class RecipeController extends Controller
{

    public static function boot() {
        parent::boot();
        static::deleted(function($recipe) {
            $recipe->categories()->detach();
            $recipe->ingredientDetails()->delete();
            $recipe->ratings()->delete();
        });
    } 

    public function show(Recipe $recipe) {
        Recipe::setDetails($recipe);
        return view('recipes.index', compact('recipe'));
    }

    public function createForm() {
        $cookbooks[NULL] = '-- SELECT --';
        foreach (Cookbook::orderBy('name')->get() as $cookbook) {
            $cookbooks[$cookbook->id] = $cookbook->name;
        }

        $authors[NULL] = '-- SELECT --';
        foreach (Author::orderBy('name')->get() as $author) {
            $authors[$author->id] = $author->name;
        }

        foreach (Category::orderBy('name')->get() as $category) {
            $categories[$category->id] = $category->name;
        }

        return view('recipes.create', compact('authors', 'cookbooks', 'categories'));
    }

    public function create(CreateRecipe $request) {
        $input = $request->all();
        $input['user_id'] = Auth::user()->id;
        $recipe = Recipe::create($input);
        if ($recipe->id) {
            return redirect('recipes/'.$recipe->id);
        }
    }

    public function delete(Recipe $recipe) {
        if (Auth::user()->id == $recipe->user_id) {
            if ($recipe->delete()) {
                \Toast::success('Rezept erfolgreich gelöscht.');
                return redirect('/');
            } else {
                abort(500);
            }
        } else {
            \Toast::error('Du hast kein Recht dieses Rezept zu löschen.');
            return redirect('/recipes/'.$recipe->id);
        }
    }
}
