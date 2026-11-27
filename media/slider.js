document.querySelectorAll('.dharma-slider').forEach(root => {
  const slides = [...root.querySelectorAll('.dharma-slider__slide')];
  if (slides.length < 2) return;

  const controls = root.querySelector('.dharma-slider__controls');
  const dots = [...controls.querySelectorAll('[data-dot]')];
  const delay = Number(root.dataset.interval);
  let index = 0;
  let timer;
  let pendingSlide = null;

  function show(next) {
    const target = (next + slides.length) % slides.length;
    if (root.querySelector('[data-leaving]')) {
      pendingSlide = next;
      return;
    }
    if (target === index) return;

    root.style.setProperty('--dharma-slider-slide-direction', next > index ? '1' : '-1');
    slides[index].setAttribute('data-leaving', '');
    slides[index].removeAttribute('data-active');
    slides[index].inert = true;
    dots[index].removeAttribute('aria-current');

    index = target;
    slides[index].setAttribute('data-active', '');
    slides[index].inert = false;
    dots[index].setAttribute('aria-current', 'true');
  }

  function stop() {
    window.clearInterval(timer);
  }

  function start() {
    stop();
    if (delay && !root.matches(':hover, :focus-within')) {
      timer = window.setInterval(() => show(index + 1), delay);
    }
  }

  controls.querySelector('[data-prev]').addEventListener('click', () => show(index - 1));
  controls.querySelector('[data-next]').addEventListener('click', () => show(index + 1));
  dots.forEach((dot, number) => dot.addEventListener('click', () => show(number)));

  root.addEventListener('animationend', event => {
    if (event.animationName !== 'dharma-slide-out') return;
    event.target.removeAttribute('data-leaving');
    if (pendingSlide !== null) {
      const next = pendingSlide;
      pendingSlide = null;
      show(next);
    }
  });

  root.addEventListener('mouseenter', stop);
  root.addEventListener('mouseleave', start);
  root.addEventListener('focusin', stop);
  root.addEventListener('focusout', event => {
    if (!root.contains(event.relatedTarget)) window.requestAnimationFrame(start);
  });

  root.classList.add('dharma-slider--ready');
  slides.forEach(slide => { slide.hidden = false; });
  controls.hidden = false;
  start();
});
