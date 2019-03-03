@extends('layouts.master')


@section('title', 'Rezept '.$recipe->name.' bearbeiten')


@section('content-class', 'recipe form')
@section('content')

    {!! Form::open(['url' => 'recipes/edit/'.$recipe->id, 'files' => true]) !!}

        {!! FormHelper::groups(['cluster', 'input']) !!}
            {!! Form::label('Name', NULL, ['class' => 'required']) !!}
            {!! Form::text('name', $recipe->name, ['maxlength' => 200, 'required', 'autofocus']) !!}
        {!! FormHelper::close(2) !!}


        {!! FormHelper::group('cluster') !!}
            <div>
                {!! Form::label('Kochbuch', NULL, ['class' => 'required']) !!}
                {!! Form::text('cookbook', $cookbooks[$recipe->cookbook_id], [
                    'maxlength' => 200,
                    'class' => 'text-input',
                    'autocomplete' => 'off',
                    'required']) !!}

                {!! FormHelper::jsDropdown($cookbooks) !!}
            </div>

            <div>
                {!! Form::label('Autor') !!}
                {!! Form::text('author', (isset($authors) ? $authors[$recipe->author_id] : NULL), [
                    'maxlength' => 200,
                    'class' => 'text-input',
                    'autocomplete' => 'off']) !!}

                @if (isset($authors))
                    {!! FormHelper::jsDropdown($authors) !!}
                @endif
            </div>
        {!! FormHelper::close() !!}


        @php
            $selectedCategories = [];
            foreach ($recipe->categories as $category) {
                array_push($selectedCategories, $category->id);
            }
        @endphp

        {!! FormHelper::group('cluster') !!}
            <div>
                {!! Form::label('Kategorien') !!}
                {!! Form::select('categories[]', $categories, $selectedCategories, [
                    'multiple',
                    'size' => 7]) !!}
            </div>

            <div>
                {!! Form::label('Portionen') !!}
                {!! Form::number('yield_amount', $recipe->yield_amount, ['max' => 999, 'size' => 3]) !!}
            </div>

            <div>
                {!! Form::label('Portionen maximal') !!}
                {!! Form::number('yield_amount_max', $recipe->yield_amount_max, ['max' => 999, 'size' => 3]) !!}
            </div>

            <div>
                {!! Form::label('Zubereitungszeit') !!}
                {!! Form::time('preparation_time', date('H:i', strtotime($recipe->preparation_time))) !!}
            </div>
        {!! FormHelper::close() !!}


        {!! FormHelper::group('cluster') !!}
            {!! Form::label('Zubereitung', NULL, ['class' => 'required']) !!}
            {!! Form::textarea('instructions', $recipe->instructions, ['required']) !!}
        {!! FormHelper::close() !!}


        {!! FormHelper::group('cluster') !!}
            <div class="checkbox">
                {!! Form::label('Altes Foto löschen (falls vorhanden)?') !!}
                {!! Form::checkbox('delete_photo') !!}
            </div>

            <div>
                {!! Form::label('Altes Foto überschreiben (max 2MB)') !!}
                {!! Form::file('photo') !!}
                {!! Form::hidden('MAX_FILE_SIZE', '2097152') !!}
            </div>

            <div>
                {!! Form::submit('Änderungen speichern') !!}
            </div>
        {!! FormHelper::close() !!}

    {!! Form::close() !!}

@stop
