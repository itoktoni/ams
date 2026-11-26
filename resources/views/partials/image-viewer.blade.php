{{-- Lightbox popup + download untuk foto. Dipakai dengan onclick="openImageViewer(url, judul)". --}}
@once
<div id="image-viewer-modal" class="fixed inset-0 z-[9999] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/70" onclick="closeImageViewer()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl max-w-3xl w-full max-h-[90vh] flex flex-col overflow-hidden">
        <div class="flex items-center justify-between gap-2 px-4 py-3 border-b border-outline-variant">
            <p id="image-viewer-title" class="text-sm font-bold text-on-surface truncate">Foto</p>
            <button type="button" onclick="closeImageViewer()" class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-surface-container text-on-surface-variant" aria-label="Tutup">
                <span class="material-symbols-outlined text-xl">close</span>
            </button>
        </div>
        <div class="p-4 overflow-auto flex items-center justify-center bg-black/5 min-h-[200px]">
            <img id="image-viewer-img" src="" alt="Foto" class="max-w-full max-h-[65vh] object-contain rounded-lg">
        </div>
        <div class="flex items-center justify-end gap-2 px-4 py-3 border-t border-outline-variant">
            <a id="image-viewer-download" href="" download class="inline-flex items-center gap-1.5 h-10 px-5 rounded-xl bg-primary text-white text-sm font-semibold hover:bg-primary/90">
                <span class="material-symbols-outlined text-lg">download</span> Download
            </a>
            <button type="button" onclick="closeImageViewer()" class="inline-flex items-center h-10 px-5 rounded-xl border border-outline-variant text-sm font-semibold text-on-surface-variant hover:bg-surface-container">
                Tutup
            </button>
        </div>
    </div>
</div>
<script>
function openImageViewer(src, title) {
    var modal = document.getElementById('image-viewer-modal');
    var img = document.getElementById('image-viewer-img');
    var caption = document.getElementById('image-viewer-title');
    var dl = document.getElementById('image-viewer-download');
    if (!modal || !img) return;
    img.src = src;
    if (caption) caption.textContent = title || 'Foto';
    if (dl) {
        dl.href = src;
        dl.setAttribute('download', (title || 'foto').replace(/[^\w\- ]+/g, '') + '.jpg');
    }
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
}
function closeImageViewer() {
    var modal = document.getElementById('image-viewer-modal');
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeImageViewer();
});
</script>
@endonce
