@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Все посты</h1>
        @auth
            <a href="{{ route('posts.create') }}" class="btn btn-primary">Новый пост</a>
        @endauth
    </div>

    @forelse ($posts as $post)
        <div class="card mb-3">
            <div class="card-body">
                <h3>
                    <a href="{{ route('posts.show', $post) }}" class="text-decoration-none">
                        {{ $post->title }}
                    </a>
                </h3>
                <p class="text-muted small">
                    {{ $post->author->name }} · {{ $post->created_at->format('d.m.Y H:i') }}
                </p>
                <p>{{ Str::limit($post->body, 300) }}</p>
            </div>
        </div>
    @empty
        <p>Постов пока нет.</p>
    @endforelse

    <div class="mt-4">
        {{ $posts->links() }}
    </div>
</div>
@endsection
