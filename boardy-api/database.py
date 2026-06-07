import aiomysql

DB_CONFIG = {
    'host': '127.0.0.1',
    'port': 3306,
    'user': 'boardy',         
    'password': '123',
    'db': 'boardy_api',              # ← ваша база данных
    'charset': 'utf8mb4',      # ← полный Unicode, включая эмодзи
}

async def get_db():
    return await aiomysql.connect(**DB_CONFIG)

async def db_query(sql, *args):
    conn = await get_db()
    async with conn.cursor(aiomysql.DictCursor) as cur:
        await cur.execute(sql, args)
        rows = await cur.fetchall()
    conn.close()
    return rows

async def db_query_one(sql, *args):
    conn = await get_db()
    async with conn.cursor(aiomysql.DictCursor) as cur:
        await cur.execute(sql, args)
        row = await cur.fetchone()
    conn.close()
    return row

async def db_insert(sql, *args):
    conn = await get_db()
    async with conn.cursor() as cur:
        await cur.execute(sql, args)
        await conn.commit()
        last_id = cur.lastrowid
    conn.close()
    return last_id

async def db_execute(sql, *args):
    conn = await get_db()
    async with conn.cursor() as cur:
        await cur.execute(sql, args)
        await conn.commit()
    conn.close()
