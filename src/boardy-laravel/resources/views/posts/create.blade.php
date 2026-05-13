@extends('layouts.app')

@section('title', 'Новый пост')

@section('content')
    <div class="card">
        <div class="card-header">
            <h2>Новый пост</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('posts.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="title" class="form-label">Заголовок</label>
                    <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="body" class="form-label">Текст поста</label>
                    <textarea name="body" id="body" rows="6" class="form-control @error('body') is-invalid @enderror" required>{{ old('body') }}</textarea>
                    @error('body')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Опубликовать</button>
                <a href="{{ route('posts.index') }}" class="btn btn-secondary">Отмена</a>
            </form>
        </div>
    </div>
@endsection

