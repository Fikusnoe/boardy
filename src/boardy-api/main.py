import asyncio
import json
from contextlib import asynccontextmanager
from datetime import datetime
import aiomysql
import aioredis
from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from routers import comments, ws

DB_CONFIG = {
    'host': '127.0.0.1',
    'port': 3306,
    'user': 'boardy',
    'password': '123',
    'db': 'boardy_api',
    'charset': 'utf8mb4',
}


async def get_db():
    return await aiomysql.connect(**DB_CONFIG)


async def db_execute(query: str, *args):
    conn = await get_db()
    async with conn.cursor() as cur:
        await cur.execute(query, args)
        await conn.commit()
    conn.close()


async def redis_subscriber():
    print("Redis начал работу", flush=True)
    redis = await aioredis.from_url('redis://127.0.0.1:6379')
    pubsub = redis.pubsub()
    await pubsub.subscribe('new_post', 'user.renamed')

    async for message in pubsub.listen():
        if message['type'] != 'message':
            continue

        channel = message['channel'].decode()
        data = json.loads(message['data'])

        if channel == 'new_post':
            await ws.manager.broadcast({'type': 'new_post', 'post': data})

        elif channel == 'user.renamed':
            await db_execute(
                'UPDATE comments SET author_name=%s WHERE author_id=%s',
                data['new_name'],
                data['id'],
            )
            await ws.manager.broadcast(
                {
                    'type': 'user_renamed',
                    'user_id': data['id'],
                    'new_name': data['new_name'],
                }
            )


@asynccontextmanager
async def lifespan(app: FastAPI):
    task = asyncio.create_task(redis_subscriber())
    yield
    task.cancel()


app = FastAPI(title='Boardy API', version='0.5.0', lifespan=lifespan)

app.add_middleware(
    CORSMiddleware,
    allow_origins=['https://fgsfds.ai-info.ru'],
    allow_credentials=True,
    allow_methods=['*'],
    allow_headers=['*'],
)

app.include_router(comments.router)
app.include_router(ws.router)


@app.get('/api/status')
async def status():
    return {'status': 'ok', 'time': str(datetime.now())}


@app.get('/api/messages')
async def get_messages():
    conn = await get_db()
    async with conn.cursor(aiomysql.DictCursor) as cur:
        await cur.execute(
            'SELECT posts.body AS message, users.name, '
            'posts.created_at FROM posts '
            'JOIN users ON posts.author_id = users.id '
            'ORDER BY posts.created_at DESC'
        )
        messages = await cur.fetchall()
    conn.close()

    for m in messages:
        m['created_at'] = str(m['created_at'])
    return {'messages': messages, 'count': len(messages)}


@app.get('/api/users')
async def get_users():
    conn = await get_db()
    async with conn.cursor(aiomysql.DictCursor) as cur:
        await cur.execute('SELECT id, name, email, created_at FROM users')
        users = await cur.fetchall()
    conn.close()

    for u in users:
        u['created_at'] = str(u['created_at'])
    return {'users': users, 'count': len(users)}
