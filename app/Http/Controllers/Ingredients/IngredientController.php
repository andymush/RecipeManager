<?php

namespace App\Http\Controllers\Ingredients;

use App\Models\Recipes\Recipe;
use App\Http\Controllers\Controller;
use App\Models\Ingredients\Ingredient;
use App\Http\Requests\Ingredients\Ingredient\Store;
use App\Http\Requests\Ingredients\Ingredient\Update;

class IngredientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Models\Recipes\Recipe  $recipe
     * @return \Illuminate\Http\Response
     */
    public function index(Recipe $recipe)
    {
        $this->authorize([Ingredient::class, $recipe]);
        return $recipe->ingredients()->originalOnly()->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Store $request, Recipe $recipe)
    {
        $this->authorize([Ingredient::class, $recipe]);
        $validated = $request->validated();
        $ingredient = $recipe->ingredients()->create($validated);
        if (isset($validated['ingredient_attributes'])) {
            $ingredient->ingredientAttributes()->sync($validated['ingredient_attributes']);
        }
        return $this->responseCreated('ingredients.show', $ingredient->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Ingredients\Ingredient  $ingredient
     * @return \Illuminate\Http\Response
     */
    public function show(Ingredient $ingredient)
    {
        $this->authorize($ingredient);
        return $ingredient;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Ingredients\Ingredient  $ingredient
     * @return \Illuminate\Http\Response
     */
    public function update(Update $request, Ingredient $ingredient)
    {
        $this->authorize($ingredient);
        $validated = $request->validated();
        if (isset($validated['ingredient_attributes'])) {
            $ingredient->ingredientAttributes()->sync($validated['ingredient_attributes']);
        }
        $ingredient->update($request->validated());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Ingredients\Ingredient  $ingredient
     * @return \Illuminate\Http\Response
     */
    public function destroy(Ingredient $ingredient)
    {
        $this->authorize($ingredient);
        $ingredient->delete();
    }

    /**
     * Restore the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore(int $id)
    {
        $ingredient = Ingredient::onlyTrashed()->findOrFail($id);
        $this->authorize($ingredient);
        $ingredient->restore();
    }
}
