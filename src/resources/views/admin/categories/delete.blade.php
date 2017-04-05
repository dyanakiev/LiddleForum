@extends('liddleforum::layout')

@section('liddleforum_content_inner')

    <h3>Delete Category</h3>
    <p>
        Delete an existing category or subcategory.
        <strong>This will delete all subcategories and their threads belonging to the deleted category!</strong>
        <strong>This cannot be undone!</strong>
    </p>

    @if(count($categories))
        <form method="POST" action="{{ route('liddleforum.admin.categories.delete') }}">
            {!! csrf_field() !!}
            {!! method_field('DELETE') !!}
            <div class="form-group">
                <label for="category">Category</label>
                <select id="category" name="category" class="form-control">
                    <option value="">- Please Select -</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->getDropdownName(true) }}</option>
                    @endforeach
                </select>
            </div>

            <button class="btn btn-danger">Delete Category</button>
        </form>
    @else
        <div class="alert alert-info">
            <i class="fa fa-fw fa-info-circle"></i> You do not have any categories yet
        </div>
    @endif

@endsection