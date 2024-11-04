@props([
    'page' => 1,
    'lastPage' => 1,
])

@if ($lastPage > 1)
    <nav>
        <ul class="pagination">
            {{-- Previous Page Link --}}
            @if ($page > 1)
                <li class="page-item">
                    <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $page - 1]) }}" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link" aria-hidden="true">&laquo;</span>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @for ($i = 1; $i <= $lastPage; $i++)
                @if ($lastPage < 25)
                    @if ($i == $page)
                        <li class="page-item active">
                            <span class="page-link">{{ $i }}</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $i]) }}">{{ $i }}</a>
                        </li>
                    @endif
                @else
                    {{-- Show pagination with ellipsis for large page counts --}}
                    @if ($i == $page)
                        <li class="page-item active">
                            <span class="page-link">{{ $i }}</span>
                        </li>
                    @elseif (in_array($i, [1, 2, 3]) || in_array($i, [$lastPage, $lastPage-1, $lastPage-2]) || abs($i - $page) <= 3)
                        <li class="page-item">
                            <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $i]) }}">{{ $i }}</a>
                        </li>
                    @elseif ($i == $page - 4 || $i == $page + 4)
                        <li class="page-item">
                            <span class="page-link">...</span>
                        </li>
                    @endif
                @endif
            @endfor

            {{-- Next Page Link --}}
            @if ($page < $lastPage)
                <li class="page-item">
                    <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $page + 1]) }}" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link" aria-hidden="true">&raquo;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
