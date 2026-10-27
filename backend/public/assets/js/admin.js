// Filament's image editor can set up its cropper while the editor dialog is still opening, so the cropper
// measures a zero-size box and falls back to 200×100. When the cropper is ready, or its box changes size,
// and the two disagree, a window resize makes the cropper re-measure (Cropper.js is responsive to it).
(() => {
  const BOX = '.fi-fo-file-upload-editor-image-ctn';
  const sync = box => requestAnimationFrame(() => {
    const cropper = box.querySelector('.cropper-container');
    if (cropper && box.clientWidth > 0 && Math.abs(cropper.offsetWidth - box.clientWidth) > 2) {
      window.dispatchEvent(new Event('resize'));
    }
  });

  // Cropper.js fires a (non-bubbling) "ready" event on the image once it has loaded
  document.addEventListener('ready', e => {
    const box = e.target.closest?.(BOX);
    if (box) sync(box);
  }, true);

  const resized = new ResizeObserver(entries => entries.forEach(entry => sync(entry.target)));
  const watch = () => document.querySelectorAll(BOX).forEach(box => resized.observe(box));
  watch();
  new MutationObserver(watch).observe(document.body, { childList: true, subtree: true });
})();
