from fastapi import APIRouter, HTTPException
from pydantic import BaseModel
import aiomysql
from typing import Optional
from database import get_db
from auth import get_current_user
from fastapi import Depends

router = APIRouter()

class CommentCreate(BaseModel):
    body: str

class CommentUpdate(BaseModel):
    body: str

@router.get('/api/posts/{post_id}/comments')
async def get_children(post_id: int):
    conn = await get_db()
    async with conn.cursor(aiomysql.DictCursor) as cur:
        await cur.execute(
            'SELECT c.id, c.body, c.created_at, '
            'u.name AS author_name '      # ← имя из связанной таблицы
            'FROM comments c '
            'JOIN users u ON c.author_id = u.id '
            'WHERE c.post_id = %s '     # ← фильтр по родителю
            'ORDER BY c.created_at',
            (post_id,)
        )
        items = await cur.fetchall()
    conn.close()
    # datetime → строка (JSON не умеет datetime)
    for item in items:
        item['created_at'] = str(item['created_at'])
    return {'items': items, 'count': len(items)}

@router.post('/api/posts/{post_id}/comments', status_code=201)
async def create_comment(post_id: int, data: CommentCreate, user = Depends(get_current_user)):
    if not data.body.strip():     # ← валидация: не пустой
        raise HTTPException(status_code=422, detail='Текст пустой')
    conn = await get_db()
    async with conn.cursor() as cur:
        # Проверяем что родитель существует
        await cur.execute('SELECT id FROM posts WHERE id=%s', (post_id,))
        if not await cur.fetchone():
            conn.close()
            raise HTTPException(status_code=404, detail='Родитель не найден')
        # Создаём запись
        await cur.execute(
            'INSERT INTO comments (body, post_id, author_id) VALUES (%s,%s,%s)',
            (data.body, post_id, user['user_id'])
        )
        await conn.commit()
        new_id = cur.lastrowid
    conn.close()
    return {'id': new_id, 'body': data.body, 'status': 'created'}

@router.put('/api/comments/{comment_id}')
async def update_comment(comment_id: int, data: CommentUpdate, user = Depends(get_current_user)):
    if not data.body.strip():
        raise HTTPException(status_code=422, detail='Текст пустой')
    conn = await get_db()
    async with conn.cursor() as cur:
        await cur.execute(
            'UPDATE comments SET body=%s WHERE id=%s',
            (data.body, comment_id)
        )
        if cur.rowcount == 0:
            conn.close()
            raise HTTPException(status_code=404, detail='Не найден')
        await conn.commit()
    conn.close()
    return {'id': comment_id, 'body': data.body, 'status': 'updated'}

@router.delete('/api/comments/{comment_id}', status_code=204)
async def delete_comment(comment_id: int, user = Depends(get_current_user)):
    conn = await get_db()
    async with conn.cursor() as cur:
        await cur.execute('DELETE FROM comments WHERE id=%s', (comment_id,))
        if cur.rowcount == 0:
            conn.close()
            raise HTTPException(status_code=404, detail='Не найден')
        await conn.commit()
    conn.close()

