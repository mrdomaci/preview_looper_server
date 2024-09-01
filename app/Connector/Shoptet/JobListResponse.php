<?php

declare(strict_types=1);

namespace App\Connector\Shoptet;

class JobListResponse
{
    /**
     * @param array<int, JobResponse> $jobs
     */
    public function __construct(
        private int $totalCount,
        private int $page,
        private int $pageCount,
        private int $itemsOnPage,
        private int $itemsPerPage,
        private array $jobs = []
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
    /**
     * @return array<int, JobResponse>
     */
    public function getJobs(): array
    {
        return $this->jobs;
    }
    /**
     * @param array<int, JobResponse> $jobs
     */
    public function setJobs(array $jobs): void
    {
        $this->jobs = $jobs;
    }
    public function addJob(JobResponse $job): void
    {
        $this->jobs[] = $job;
    }
}
