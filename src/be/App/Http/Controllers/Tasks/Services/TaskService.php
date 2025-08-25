<?php
declare(strict_types = 1);
namespace App\Http\Controllers\Tasks\Services;

use App\Http\Controllers\Tasks\{
    DTOs\CustomerDTO,
    Filters\CustomerFilters,
    Repositories\CustomerRepositoryInterface,
    Models\Customer
};
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class TaskService implements TaskServiceInterface
{
    /**
     * @param CustomerRepositoryInterface $taskRepository
     */
    public function __construct(
        protected CustomerRepositoryInterface $taskRepository
    ){}

    /**
     * Get filtered tasks with pagination
     *
     * @param Request $request
     * @param bool $includeTags
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getTasks(Request $request, bool $includeTags, int $perPage): LengthAwarePaginator
    {
        $filter = new CustomerFilters();
        $queryItems = $filter->transform($request);

        return $this->taskRepository->getFilteredTasks(
            queryItems: $queryItems,
            includeTags: $includeTags,
            perPage: $perPage
        );
    }

    /**
     * @param CustomerDTO $taskDTO
     * @return Customer
     */
    public function createTask(CustomerDTO $taskDTO): Customer
    {
        return DB::transaction(function () use ($taskDTO) {
            return $this->taskRepository->create($taskDTO);
        });
    }

    /**
     * @param int $id
     * @return Customer
     */
    public function getTaskById(int $id): Customer
    {
        return $this->taskRepository->findOrFail($id);
    }

    /**
     * @param int $id
     * @param CustomerDTO $taskDTO
     * @return Customer
     */
    public function updateTask(int $id, CustomerDTO $taskDTO): Customer
    {
        return DB::transaction(function () use ($id,$taskDTO) {
            return $this->taskRepository->update($id, $taskDTO);
        });
    }

    /**
     * @param int $id
     * @return void
     */
    public function deleteTask(int $id): void
    {
        DB::transaction(function () use ($id) {
            $this->taskRepository->delete($id);
        });
    }

    /**
     * @param Request $request
     * @param bool $includeTags
     * @param int|null $userId
     * @return array
     */
    public function getTasksReport(Request $request, bool $includeTags, ?int $userId = null): array
    {
        $filter = new CustomerFilters();
        $queryItems = $filter->transform($request);

        return $this->taskRepository->getTasksReport(
            queryItems: $queryItems,
            includeTags: $includeTags,
            userId: $userId
        );
    }
}