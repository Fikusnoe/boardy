const API = 'https://api.fgsfds.ai-info.ru';    // ← ваш домен
const POST_ID = 1;                      // ← ID родителя

async function loadItems() {
    const res = await fetch(`${API}/api/posts/${ POST_ID }/comments`);
    const data = await res.json();
    document.getElementById('list').innerHTML = data.items.map(item => `
        <div>
            <strong>${esc(item.author_name)}</strong>
            <p>${esc(item.body)}</p>
        </div>
    `).join('');
}

loadItems();

document.getElementById('btn').addEventListener('click', async () => {
    const body = document.getElementById('body').value.trim();
    if (!body) return;                    // ← не отправляем пустой
    await fetch(`${API}/api/posts/${POST_ID}/comments`, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({body: body})  // ← поля из вашей модели
    });
    document.getElementById('body').value = '';  // ← очистить
    loadItems();                           // ← обновить список
});

function esc(str) {
    const div = document.createElement('div');
    div.textContent = str;     // ← экранирует < > & "
    return div.innerHTML;      // ← безопасная строка
}


