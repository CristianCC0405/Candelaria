function toggleMenu(){
  const nav = document.querySelector('nav');
  nav.classList.toggle('active');
}



function toggleDetalles(el) {
  el.classList.toggle("activo");
}

function cambiarFoto(btn, dir) {
  event.stopPropagation(); // evita colapsar el contenedor al hacer clic
  const galeria = btn.parentElement;
  const imgPrincipal = galeria.querySelector("img");
  const lista = Array.from(galeria.querySelectorAll(".fotos img"));
  let actual = lista.findIndex(f => f.src === imgPrincipal.src);
  if (actual === -1) actual = 0;

  let siguiente = actual + dir;
  if (siguiente < 0) siguiente = lista.length - 1;
  if (siguiente >= lista.length) siguiente = 0;

  imgPrincipal.src = lista[siguiente].src;
}
