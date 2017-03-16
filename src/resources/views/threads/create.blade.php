@extends('liddleforum::layout')

@section('liddleforum_content_inner')

    <h3>Create Thread</h3>

    <form method="POST" action="{{ route('liddleforum.threads.create') }}">
        {!! csrf_field() !!}

        <div class="form-group">
            <label for="category">Category</label>
            <select id="category" name="category" class="form-control">
                <?php $currentCategory = null; ?>
                @foreach($categories as $category)
                    <?php if ( ! $currentCategory || $currentCategory->id !== $category->parent->id) : ?>
                        @if($currentCategory)
                            </optgroup>
                        @endif
                        <?php $currentCategory = $category->parent; ?>
                        <optgroup label="{{ $currentCategory->name }}">
                    <?php endif ?>
                    <option value="{{ $category->slug }}" @if(Request::get('category') === $category->slug) selected @endif>{{ $category->name }}</option>
                @endforeach
                </optgroup>
            </select>
        </div>

        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" class="form-control" id="title" name="title" placeholder="Title" @if(Request::get('title')) value="{{ Request::get('title') }}" @endif>
        </div>

        <div class="form-group">
            <label for="body">Message</label>
            <textarea id="body" name="body" class="form-control" placeholder="Enter your thread message here..."></textarea>
        </div>

        <button class="btn btn-primary">Create Thread</button>

    </form>
@endsection