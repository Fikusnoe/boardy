<?php $__env->startSection('content'); ?>
<div id="posts-feed" class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Все посты</h1>
        <?php if(auth()->guard()->check()): ?>
            <a href="<?php echo e(route('posts.create')); ?>" class="btn btn-primary">Новый пост</a>
        <?php endif; ?>
    </div>

    <?php $__empty_1 = true; $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="card mb-3">
            <div class="card-body">
                <h3>
                    <a href="<?php echo e(route('posts.show', $post)); ?>" class="text-decoration-none">
                        <?php echo e($post->title); ?>

                    </a>
                </h3>
                <p class="text-muted small">
                    <?php echo e($post->author->name); ?> · <?php echo e($post->created_at->format('d.m.Y H:i')); ?>

                </p>
                <p><?php echo e(Str::limit($post->body, 300)); ?></p>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <p>Постов пока нет.</p>
    <?php endif; ?>

    <div class="mt-4">
        <?php echo e($posts->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<script>
<?php if(app()->environment('production')): ?>
const wsUrl = 'wss://api.<?php echo e(config("app.fastapi_domain")); ?>/ws'
<?php else: ?>
const wsUrl = 'ws://localhost:8000/ws'
<?php endif; ?>

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


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/boardy/resources/views/posts/index.blade.php ENDPATH**/ ?>