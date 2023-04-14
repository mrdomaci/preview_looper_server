<?php
declare(strict_types=1);

namespace App\Connector;

class Paginator
{
    public function __construct(
        private int $totalCount,
        private int $page,
        private int $pageCount,
        private int $itemsOnPage,
        private int $itemsPerPage,
    ) {
    }

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getPageCount(): int
    {
        return $this->pageCount;
    }

    public function getItemsOnPage(): int
    {
        return $this->itemsOnPage;
    }

    public function getItemsPerPage(): int
    {
        return $this->itemsPerPage;
    }
}