<?php
namespace App\Http\Controllers\Tasks\Services;

use App\Http\Controllers\Tasks\DTOs\CustomerDTO;
use App\Http\Controllers\Tasks\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface TaskServiceInterface
{
    /**
     * @param Request $request
     * @param bool $includeTags
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getTasks(Request $request, bool $includeTags, int $perPage): LengthAwarePaginator;

    /**
     * @param CustomerDTO $taskDTO
     * @return Customer
     */
    public function createTask(CustomerDTO $taskDTO): Customer;

    /**
     * @param int $id
     * @return Customer
     */
    public function getTaskById(int $id): Customer;

    /**
     * @param int $id
     * @param CustomerDTO $taskDTO
     * @return Customer
     */
    public function updateTask(int $id, CustomerDTO $taskDTO): Customer;

    /**
     * @param int $id
     * @return void
     */
    public function deleteTask(int $id): void;

    /**
     * @param Request $request
     * @param bool $includeTags
     * @param int|null $userId
     * @return array
     */
    public function getTasksReport(Request $request, bool $includeTags, ?int $userId = null): array;
}