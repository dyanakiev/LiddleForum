@extends('liddleforum::layout')

@section('liddleforum_content_inner')

	@if($category->parent_id)
		<a href="{{ route('liddleforum.threads.create', ['category' => $category->slug]) }}" class="btn btn-primary btn-sm pull-right">Create Thread</a>
	@endif
	<p class="thread-title">
		<a href="{{ route('liddleforum.index') }}">Home</a> &gt;
		@foreach($category->getCategoryChain() as $parentCategory)
			<a href="{{ route('liddleforum.categories.view', ['category' => $parentCategory->slug]) }}">{{ $parentCategory->name }}</a> &gt;
		@endforeach
		{{ $category->name }}
	</p>

	@if($category->subcategories()->count())
		<div class="category-list">
			<div class="panel panel-default">
				<div class="panel-heading">
					<span class="panel-title">Subcategories</span>
				</div>
				<table class="table table-striped table-bordered subcategory-table">
					<thead>
					<tr>
						<th>Name</th>
						<th width="10%" class="hidden-xs">Threads</th>
						<th width="10%" class="hidden-xs">Posts</th>
						<th width="30%" class="hidden-xs hidden-sm">Last Post</th>
					</tr>
					</thead>
					<tbody>
					@foreach($category->subcategories as $subcategory)
						<tr>
							<td>
								<p class="subcategory-title">
									<a href="{{ route('liddleforum.categories.view', ['category' => $subcategory->slug]) }}">{{ $subcategory->name }}</a>
								</p>
								<p class="subcategory-description">{{ $subcategory->description }}</p>
							</td>
							<td class="text-center hidden-xs">{{ $subcategory->threads()->count() }}</td>
							<td class="text-center hidden-xs">{{ $subcategory->posts()->count() }}</td>
							<?php $mostRecentPost = $subcategory->getMostRecentPost(); ?>
							<td class="text-right hidden-xs hidden-sm">
								@if ($mostRecentPost)
									<p>
										<a href="{{ route('liddleforum.threads.view', ['thread_slug' => $mostRecentPost->thread->slug]) }}">
											{{ str_limit($mostRecentPost->thread->title, 40) }}
										</a>
									</p>
									<p>
										<small>
											by {{ $mostRecentPost->user->{config('liddleforum.user.name_column')} }}
											- {{ \LiddleDev\LiddleForum\Helpers\GeneralHelper::getTimeAgo($mostRecentPost->created_at) }}
										</small>
									</p>
								@else
									<p>-</p>
								@endif
							</td>
						</tr>
					@endforeach
					</tbody>
				</table>
			</div>
		</div>
	@endif

	@if($category->parent_id)
		<div class="thread-list">
			<div class="panel panel-default">
				<div class="panel-heading">
					<span class="panel-title">Threads</span>
				</div>
				@if($category->threads()->count())
					<table class="table table-striped table-bordered thread-table">
						<thead>
						<tr>
							<th>Thread</th>
							<th width="10%" class="hidden-xs">Posts</th>
							<th width="30%" class="hidden-xs hidden-sm">Last Reply</th>
						</tr>
						</thead>
						<tbody>
						@foreach($category->threads as $thread)
							<tr>
								<td>
									<p class="pull-right">
										<small>
											by <strong>{{ $thread->author->{config('liddleforum.user.name_column')} }}</strong>
											- {{ \LiddleDev\LiddleForum\Helpers\GeneralHelper::getTimeAgo($thread->created_at) }}
										</small>
									</p>
									<p>
										<a href="{{ route('liddleforum.threads.view', ['thread_slug' => $thread->slug]) }}">{{ $thread->title }}</a>
									</p>
								</td>
								<td class="text-center hidden-xs">{{ $thread->posts()->count() }}</td>
								<?php $mostRecentPost = $thread->getMostRecentPost(); ?>
								<td class="text-right hidden-xs hidden-sm">
									@if ($mostRecentPost)
										<p>
											by <strong>{{ $mostRecentPost->user->{config('liddleforum.user.name_column')} }}</strong>
											<small> - {{ \LiddleDev\LiddleForum\Helpers\GeneralHelper::getTimeAgo($mostRecentPost->created_at) }}</small>
										</p>
									@else
										<p>-</p>
									@endif
								</td>
							</tr>
						@endforeach
						</tbody>
					</table>
				@else
					<div class="panel-body">
						<p>There are no threads yet in this category</p>
					</div>
				@endif
			</div>
		</div>
	@endif

@endsection