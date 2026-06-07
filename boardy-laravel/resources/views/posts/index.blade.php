@extends('layouts.app')

@section('content')
<div id="posts-feed" class="container mt-4">
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

<script>
@if(app()->environment('production'))
const wsUrl = 'ws://{{ config("app.fastapi_domain") }}/ws'
@else
const wsUrl = 'ws://localhost:8000/ws'
@endif

function connect() {
    const ws = new WebSocket(wsUrl)
    ws.onopen    = () => console.log('WS connected')
    ws.onmessage = (e) => {
        const msg = JSON.parse(e.data)
        if (msg.type === 'new_post') prependPost(msg.post)
    }
    ws.onclose = () => setTimeout(connect, 3000)
}

function prependPost(post) {
    const feed = document.getElementById('posts-feed')
    if (!feed) return

    const date = new Date(post.created_at)
    const formattedDate = `${date.getDate().toString().padStart(2, '0')}.${(date.getMonth()+1).toString().padStart(2, '0')}.${date.getFullYear()} ${date.getHours().toString().padStart(2, '0')}:${date.getMinutes().toString().padStart(2, '0')}`

    const card = document.createElement('div')
    card.className = 'card mb-3'
    card.innerHTML = `
        <div class="card-body">
            <h3>
                <a href="/posts/${post.id}" class="text-decoration-none">
                    ${escapeHtml(post.title)}
                </a>
            </h3>
            <p class="text-muted small">
                ${escapeHtml(post.author)} · ${formattedDate}
            </p>
            <p>${escapeHtml(post.body)}</p>
        </div>
    `

    const header = feed.querySelector('.d-flex')
    if (header) {
        header.insertAdjacentElement('afterend', card)
    } else {
        feed.prepend(card)
    }
}

function escapeHtml(str) {
    const d = document.createElement('div')
    d.textContent = str
    return d.innerHTML
}

connect()
</script>

