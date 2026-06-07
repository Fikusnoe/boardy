import asyncio
import json
from contextlib import asynccontextmanager
from datetime import datetime
import aiomysql
import redis.asyncio as aioredis
from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from routers import comments, ws

DB_CONFIG = {
    'host': 'mysql',
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
    try:
        r = aioredis.Redis(host='redis', port=6379, db=0, decode_responses=True)
        await r.ping()
        print("Подключение к Redis успешно установлено", flush=True)

        async with r.pubsub() as pubsub:
            await pubsub.subscribe('new_post', 'user.renamed')
            print("Подписка на каналы оформлена", flush=True)

            async for message in pubsub.listen():
                if message['type'] != 'message':
                    continue

                channel = message['channel']
                if isinstance(channel, bytes):
                    channel = channel.decode('utf-8')
                
                data = json.loads(message['data'])

                if channel == 'new_post':
                    print(f"Получен new_post: {data.get('title')}", flush=True)
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
    except Exception as e:
        print(f"Ошибка в redis_subscriber: {e}", flush=True)
        import traceback
        traceback.print_exc()

@asynccontextmanager
async def lifespan(app: FastAPI):
    task = asyncio.create_task(redis_subscriber())
    yield
    task.cancel()


app = FastAPI(title='Boardy API', version='0.5.0', lifespan=lifespan)

app.add_middleware(
    CORSMiddleware,
    allow_origins=['https://fgsfds.ai-info.ru', 'http://192.168.159.100'],
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
