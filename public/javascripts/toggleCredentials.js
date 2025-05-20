
  document.addEventListener('click', async function(e) {
  if (e.target.matches('.toggle-password')) {
  const btn = e.target;
  const id  = btn.dataset.id;
  const span = btn.closest('.card-body').querySelector('.password-mask');

  if (span.textContent.includes('•')) {
  try {
    const res = await fetch(`/credentials/${id}/reveal`, {
      headers: { 'Accept': 'application/json' }
    });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const data = await res.json();

  span.textContent = data.password;
  btn.textContent = 'Cacher';
} catch (err) {
  alert("Impossible de récupérer le mot de passe : " + err.message);
}
} else {
  span.textContent = '••••••••';
  btn.textContent = 'Afficher';
}
}
});

