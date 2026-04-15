import aiomysql

DB_CONFIG = {
    'host': '127.0.0.1',
    'port': 3306,
    'user': 'boardy',         
    'password': '123Qwerty!',
    'db': 'boardy',              # ← ваша база данных
    'charset': 'utf8mb4',      # ← полный Unicode, включая эмодзи
}

async def get_db():
    return await aiomysql.connect(**DB_CONFIG)

