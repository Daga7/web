const API = '/api/items';

async function fetchItems(q = ''){
  const url = new URL(API, location.origin);
  if (q) url.searchParams.set('q', q);
  const res = await fetch(url);
  if (!res.ok) throw new Error('Error al obtener items');
  return res.json();
}

function showAlert(msg, type='success'){
  const p = document.getElementById('alert-placeholder');
  p.innerHTML = `<div class="alert alert-${type} alert-dismissible" role="alert">${msg}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button></div>`;
}

function renderItems(items){
  const container = document.getElementById('items-list');
  container.innerHTML = '';
  if (!items.length) return container.innerHTML = '<p class="text-muted">No hay items.</p>';
  items.forEach(i => {
    const card = document.createElement('div');
    card.className = 'card mb-3 p-3';
    card.innerHTML = `<div class="d-flex justify-content-between align-items-start">
      <div>
        <h5>${escapeHtml(i.name)}</h5>
        <p class="mb-0">${escapeHtml(i.description || '')}</p>
      </div>
      <div>
        <button class="btn btn-sm btn-primary me-2" data-id="${i._id}" data-action="edit">Editar</button>
        <button class="btn btn-sm btn-danger" data-id="${i._id}" data-action="delete">Eliminar</button>
      </div>
    </div>`;
    container.appendChild(card);
  });
}

function escapeHtml(str){
  if (!str) return '';
  return String(str).replace(/[&"'<>\`]/g, s => ({'&':'&amp;','"':'&quot;',"'":'&#39;','<':'&lt;','>':'&gt;','`':'&#96;'})[s]);
}

// Listado inicial
async function load(q=''){
  try{
    const data = await fetchItems(q);
    renderItems(data.data || []);
  }catch(e){ showAlert(e.message, 'danger'); }
}

// Form
const form = document.getElementById('item-form');
form.addEventListener('submit', async e => {
  e.preventDefault();
  const id = document.getElementById('item-id').value;
  const name = document.getElementById('name').value.trim();
  const description = document.getElementById('description').value.trim();
  if (name.length < 2) { showAlert('Nombre muy corto', 'warning'); return; }
  const payload = { name, description };
  try{
    if (id) {
      const res = await fetch(`${API}/${id}`, { method: 'PUT', headers: {'Content-Type':'application/json'}, body: JSON.stringify(payload)});
      if (!res.ok) throw new Error('Error actualizando');
      showAlert('Actualizado');
      document.getElementById('cancel-edit').style.display='none';
      document.getElementById('item-id').value='';
    } else {
      const res = await fetch(API, { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(payload)});
      if (!res.ok) throw new Error('Error creando');
      showAlert('Creado');
    }
    form.reset();
    load();
  }catch(err){ showAlert(err.message, 'danger'); }
});

// Delegación botones editar/eliminar
document.getElementById('items-list').addEventListener('click', async (e)=>{
  const btn = e.target.closest('button'); if(!btn) return;
  const id = btn.dataset.id; const action = btn.dataset.action;
  if (action === 'edit'){
    const res = await fetch(`${API}/${id}`);
    if (!res.ok) { showAlert('Error cargando item', 'danger'); return; }
    const data = await res.json();
    document.getElementById('item-id').value = data._id;
    document.getElementById('name').value = data.name;
    document.getElementById('description').value = data.description || '';
    document.getElementById('cancel-edit').style.display = 'inline-block';
  }
  if (action === 'delete'){
    if (!confirm('Confirmar eliminación')) return;
    const res = await fetch(`${API}/${id}`, { method: 'DELETE' });
    if (!res.ok) showAlert('Error eliminando', 'danger');
    else { showAlert('Eliminado'); load(); }
  }
});

// Cancelar edición
document.getElementById('cancel-edit').addEventListener('click', () =>{
  document.getElementById('item-id').value='';
  document.getElementById('cancel-edit').style.display='none';
  document.getElementById('item-form').reset();
});

// Buscador
let timeout;
document.getElementById('search').addEventListener('input', (e)=>{
  clearTimeout(timeout);
  timeout = setTimeout(()=> load(e.target.value.trim()), 300);
});

// Carga inicial
load();
