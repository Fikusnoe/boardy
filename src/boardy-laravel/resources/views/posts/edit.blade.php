@extends('layouts.app')

@section('title', 'Редактировать пост')

@section('content')
    <div class="card">
        <div class="card-header">
            <h2>Редактировать пост</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('posts.update', $post) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="title" class="form-label">Заголовок</label>
                    <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $post->title) }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="body" class="form-label">Текст поста</label>
                    <textarea name="body" id="body" rows="6" class="form-control @error('body') is-invalid @enderror" required>{{ old('body', $post->body) }}</textarea>
                    @error('body')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Сохранить</button>
                <a href="{{ route('posts.show', $post) }}" class="btn btn-secondary">Отмена</a>
            </form>
        </div>
    </div>
@endsection
