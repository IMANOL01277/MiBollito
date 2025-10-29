// assets/js/main.js
// Reusable helper functions
async function postJSON(url, data) {
  const resp = await fetch(url, {
    method: 'POST',
    headers: {'Accept':'application/json'},
    body: data instanceof FormData ? data : JSON.stringify(data)
  });
  return resp.json();
}

function showAlert(container, type, message, timeout=3000) {
  const id = 'alert-' + Date.now();
  const el = document.createElement('div');
  el.id = id;
  el.className = `alert alert-${type} mt-2`;
  el.innerHTML = message;
  container.prepend(el);
  if (timeout) setTimeout(()=> el.remove(), timeout);
}
