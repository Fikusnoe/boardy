@extends('layouts.app')

@section('title', $post->title)

@section('content')
    <article class="card">
        <div class="card-body">
            <h1>{{ $post->title }}</h1>
            <p class="text-muted small">
                Автор: {{ $post->author->name }} · {{ $post->created_at->format('d.m.Y H:i') }}
            </p>
            <p class="mt-3">{{ $post->body }}</p>

            @can('update', $post)
                <div class="mt-3">
                    <a href="{{ route('posts.edit', $post) }}" class="btn btn-sm btn-warning">Редактировать</a>
                </div>
            @endcan

            @can('delete', $post)
                <form method="POST" action="{{ route('posts.destroy', $post) }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Удалить пост?')">Удалить</button>
                </form>
            @endcan
        </div>
    </article>

    <h3 class="mt-4">Комментарии</h3>
    @forelse ($post->comments as $comment)
        <div class="card mb-2">
            <div class="card-body">
                <strong>{{ $comment->author->name }}</strong>
                <p class="mb-0 mt-1">{{ $comment->body }}</p>
                <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
            </div>
        </div>
    @empty
        <p>Комментариев пока нет.</p>
    @endforelse

    @auth
        <div class="card mt-3">
            <div class="card-body">
                <h5>Добавить комментарий</h5>
                <form method="POST" action="{{ route('comments.store') }}">
                    @csrf
                    <input type="hidden" name="post_id" value="{{ $post->id }}">
                    <div class="mb-3">
                        <textarea name="body" class="form-control @error('body') is-invalid @enderror" rows="3" placeholder="Ваш комментарий..."></textarea>
                        @error('body')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Отправить</button>
                </form>
            </div>
        </div>
    @endauth
@endsection
