@if ($paginator->hasPages())
<nav class="pager" role="navigation" aria-label="Pagination">
    @if ($paginator->onFirstPage())
        <span class="pager-disabled">← Précédent</span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" rel="prev">← Précédent</a>
    @endif
    <span>Page {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}</span>
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" rel="next">Suivant →</a>
    @else
        <span class="pager-disabled">Suivant →</span>
    @endif
</nav>
@endif
