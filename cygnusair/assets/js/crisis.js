/* Comunicado de emergencia. Los datos llegan desde el panel (Apariencia → Comunicado y contacto)
   en window.CYGNUS_COMUNICADO. Si está activo: se abre al entrar, queda una franja fija arriba
   y la web pasa a modo sereno (la clase is-calm la pone ya WordPress en <html>). */
(() => {
  const C = window.CYGNUS_COMUNICADO;
  if (!C || !C.activo || !String(C.texto || '').trim()) return;
  const $ = s => document.querySelector(s);
  const box = $('#crisis'), strip = $('#crisis-strip');
  if (!box || !strip) return;

  document.documentElement.classList.add('is-crisis', 'is-calm');
  const paras = String(C.texto).trim().split(/\n\s*\n/);
  $('#crisis-title').textContent = C.titulo || 'Comunicado oficial';
  $('#crisis-date').textContent = C.fecha || '';
  paras.forEach(t => { const p = document.createElement('p'); p.textContent = t.trim(); $('#crisis-body').append(p); });
  if (C.telefono) { $('#crisis-phone b').textContent = C.telefono; $('#crisis-phone').hidden = false; }
  $('#crisis-strip-txt').textContent = paras[0];
  strip.hidden = false;

  // Se muestra al abrir la web; si se cierra no vuelve a salir en esa visita, salvo que cambie el texto.
  const key = 'comunicado-' + [...String(C.texto)].reduce((h, c) => (h * 31 + c.charCodeAt(0)) | 0, 7);
  let seen = false;
  try { seen = sessionStorage.getItem(key) === '1'; } catch (e) {}
  const open = () => { box.hidden = false; $('#crisis-close').focus({ preventScroll: true }); };
  const close = () => { box.hidden = true; try { sessionStorage.setItem(key, '1'); } catch (e) {} $('#crisis-open').focus({ preventScroll: true }); };
  $('#crisis-open').addEventListener('click', open);
  $('#crisis-close').addEventListener('click', close);
  box.addEventListener('click', e => { if (e.target === box) close(); });
  addEventListener('keydown', e => { if (e.key === 'Escape' && !box.hidden) close(); });
  if (!seen) open();
})();
