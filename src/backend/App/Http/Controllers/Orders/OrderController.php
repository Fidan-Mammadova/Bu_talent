<?php
declare(strict_types=1);

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Orders\{Enums\OrderStatus,
    Requests\StoreOrderRequest,
    Requests\UpdateOrderRequest,
    Resources\OrderCollection,
    Resources\OrderResource,
    Services\OrderServiceInterface};
use App\Http\Responses\{ErrorApiResponse, ErrorValidationResponse, SuccessApiResponse};
use App\Support\Helpers\TransactionHelper;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *      name="Orders",
 *      description="API Endpoints of Orders Management System"
 * )
 */
class OrderController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly OrderServiceInterface $orderService
    )
    {
    }

    /**
     * Get list of orders
     *
     * @param Request $request
     * @return OrderCollection|ErrorApiResponse|SuccessApiResponse
     */


    /**
     * @OA\Get(
     *      path="/v1/orders",
     *      operationId="getOrdersList",
     *      tags={"Orders"},
     *      summary="Get list of orders",
     *      description="Returns a paginated list of orders",
     *      @OA\Parameter(
     *          name="per_page",
     *          in="query",
     *          description="Number of items per page",
     *          required=false,
     *          @OA\Schema(type="integer", default=15)
     *      ),
     *      @OA\Parameter(
     *          name="orderBrand[lk]",
     *          in="query",
     *          description="Search query [eq,lt,lte,gt,gte,ne,lk,ilk,nlk,inlk,bt,nbt,in,nin,json]",
     *          required=false,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *            @OA\JsonContent(
     *                type="object",
     *                @OA\Property(property="timestamp", type="string", format="date-time", example="2025-02-15T23:21:32+04:00"),
     *                @OA\Property(property="path", type="string", example="api/v1/orders"),
     *                @OA\Property(property="method", type="string", example="GET"),
     *                @OA\Property(property="error", type="string", nullable=true, example=null),
     *                @OA\Property(
     *                        property="result",
     *                        type="object",ref="#/components/schemas/OrderCollection"
     *                       )
     *                   )
     *            )
     *      ),
     *      @OA\Response(response=401, description="Unauthenticated"),
     *      @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function index(Request $request): OrderCollection|SuccessApiResponse|ErrorApiResponse
    {
        return TransactionHelper::handleWithTransaction(function () use ($request) {
//            $this->authorize('viewAny', Order::class);
            $orders = $this->orderService->getOrders(
                request: $request,
                includeTags: $request->boolean('include_tags'),
                perPage: $request->integer('per_page', 10)
            );
            return new OrderCollection($orders);
        });
    }

    /**
     * @return SuccessApiResponse|ErrorApiResponse
     */

    /**
     * @OA\Get(
     *      path="/v1/orders/statuses",
     *      operationId="getOrdersStatusesList",
     *      tags={"Orders"},
     *      summary="Get list of order statuses",
     *      description="Returns list of order statuses",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="timestamp", type="string", format="date-time", example="2025-02-15T23:21:32+04:00"),
     *              @OA\Property(property="path", type="string", example="api/v1/orders/types"),
     *              @OA\Property(property="method", type="string", example="GET"),
     *              @OA\Property(property="error", type="string", nullable=true, example=null),
     *              @OA\Property(
     *                  property="result",
     *                  type="array",
     *                  @OA\Items(
     *                      type="object",
     *                      @OA\Property(property="value", type="integer", example=1),
     *                      @OA\Property(property="label", type="string", example="Fərdi")
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(response=401,description="Unauthenticated"),
     *      @OA\Response(response=403,description="Forbidden")
     * )
     */

    public function getStatusList(): SuccessApiResponse|ErrorApiResponse
    {
        return TransactionHelper::handleWithTransaction(static function () {
//            $this->authorize('viewAny', Customer::class);
            return OrderStatus::toArray();
        });
    }
    /**
     * Create new order
     *
     * @param StoreOrderRequest $request
     * @return ErrorApiResponse|ErrorValidationResponse|SuccessApiResponse
     */
    /**
     * @OA\Post(
     *      path="/v1/orders",
     *      operationId="storeOrder",
     *      tags={"Orders"},
     *      summary="Create a new order",
     *      description="Stores a new order and returns its data",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/StoreOrderRequest")
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Order successfully created",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="timestamp", type="string", format="date-time", example="2025-02-15T23:21:32+04:00"),
     *              @OA\Property(property="path", type="string", example="api/v1/orders"),
     *              @OA\Property(property="method", type="string", example="POST"),
     *              @OA\Property(property="error", type="string", nullable=true, example=null),
     *              @OA\Property(
     *                      property="result",
     *                      type="object",
     *                      @OA\Property(property="message", type="string", example="Order successfully created"),
     *                      @OA\Property(property="data", type="object", ref="#/components/schemas/OrderResource")
     *                 )
     *          )
     *      ),
     *      @OA\Response(response=401, description="Unauthenticated"),
     *      @OA\Response(response=403, description="Forbidden"),
     *      @OA\Response(
     *           response=422,
     *           description="Unprocessable Entity",
     *           @OA\JsonContent(
     *               type="object",
     *               @OA\Property(property="timestamp", type="string", format="date-time", example="2025-02-15T23:21:32+04:00"),
     *               @OA\Property(property="path", type="string", example="api/v1/orders"),
     *               @OA\Property(property="method", type="string", example="POST"),
     *               @OA\Property(
     *                   property="error",
     *                   type="object",
     *                   @OA\AdditionalProperties(
     *                       type="array",
     *                       @OA\Items(type="string", example="Sifariş məlumatları qeyd edilməyib.")
     *                   )
     *               ),
     *               @OA\Property(property="result",type="object",example={})
     *           )
     *       )
     * )
     */
    public function store(StoreOrderRequest $request): SuccessApiResponse|ErrorApiResponse|ErrorValidationResponse
    {
        return TransactionHelper::handleWithTransaction(function () use ($request) {
//            $this->authorize('createAny', Order::class);
            $order = $this->orderService->createOrder($request->toDTO());

            return [
                'message' => 'Order successfully created',
                'data' => new OrderResource($order)
            ];
        }, 201);
    }

    /**
     * Get order by ID
     *
     * @param int $id
     * @return SuccessApiResponse|ErrorApiResponse
     */
    /**
     * @OA\Get(
     *      path="/v1/orders/{id}",
     *      operationId="getOrderById",
     *      tags={"Orders"},
     *      summary="Get a specific order",
     *      description="Returns order data",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="timestamp", type="string", format="date-time", example="2025-02-15T23:21:32+04:00"),
     *              @OA\Property(property="path", type="string", example="api/v1/orders/10"),
     *              @OA\Property(property="method", type="string", example="POST"),
     *              @OA\Property(property="error", type="string", nullable=true, example=null),
     *              @OA\Property(
     *                      property="result",
     *                      type="object",
     *                      @OA\Property(property="message", type="string", example="Order successfully show: 10"),
     *                      @OA\Property(property="data", type="object", ref="#/components/schemas/OrderResource")
     *                 )
     *          )
     *      ),
     *      @OA\Response(
     *           response=404,
     *           description="Order not found",
     *           @OA\JsonContent(
     *               type="object",
     *               @OA\Property(property="timestamp", type="string", format="date-time", example="2025-02-15T23:21:32+04:00"),
     *               @OA\Property(property="path", type="string", example="api/v1/orders/10"),
     *               @OA\Property(property="method", type="string", example="GET"),
     *               @OA\Property(
     *                   property="error",
     *                   type="object",
     *                   example="Resource not found: Order not found with ID: 10"
     *               ),
     *               @OA\Property(property="result",type="object",example={})
     *             )
     *         )
     * )
     */
    public function show(int $id): SuccessApiResponse|ErrorApiResponse
    {
        return TransactionHelper::handleWithTransaction(function () use ($id) {
            $order = $this->orderService->getOrderById($id);
            if (is_string($order)) {
                throw new ModelNotFoundException($order);
            }
//            $this->authorize('view', Order::class);
            return [
                'message' => 'Order successfully show: ' . $id,
                'data' => new OrderResource($order)
            ];
        });
    }


    /**
     * @OA\Patch(
     *      path="/v1/orders/{id}",
     *      operationId="updateOrderPatch",
     *      tags={"Orders"},
     *      summary="Update an existing order specific data",
     *      description="Updates an order's details",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/UpdateOrderRequest")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Order updated successfully",
     *           @OA\JsonContent(
     *               type="object",
     *               @OA\Property(property="timestamp", type="string", format="date-time", example="2025-02-15T23:21:32+04:00"),
     *               @OA\Property(property="path", type="string", example="api/v1/orders/10"),
     *               @OA\Property(property="method", type="string", example="PATCH"),
     *               @OA\Property(property="error", type="string", nullable=true, example=null),
     *               @OA\Property(
     *                       property="result",
     *                       type="object",
     *                       @OA\Property(property="message", type="string", example="Order successfully updated"),
     *                       @OA\Property(property="data", type="object", ref="#/components/schemas/OrderResource")
     *                  )
     *           )
     *      ),
     *       @OA\Response(response=401, description="Unauthenticated"),
     *       @OA\Response(response=403, description="Forbidden"),
     *       @OA\Response(
     *         response=404,
     *         description="Order not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="timestamp", type="string", format="date-time", example="2025-02-15T23:21:32+04:00"),
     *             @OA\Property(property="path", type="string", example="api/v1/orders/10"),
     *             @OA\Property(property="method", type="string", example="PATCH"),
     *             @OA\Property(
     *                 property="error",
     *                 type="object",
     *                 example="Resource not found: Order not found with ID: 10"
     *             ),
     *             @OA\Property(property="result",type="object",example={})
     *           )
     *       ),
     *       @OA\Response(
     *            response=422,
     *            description="Unprocessable Entity",
     *            @OA\JsonContent(
     *                type="object",
     *                @OA\Property(property="timestamp", type="string", format="date-time", example="2025-02-15T23:21:32+04:00"),
     *                @OA\Property(property="path", type="string", example="api/v1/orders/10"),
     *                @OA\Property(property="method", type="string", example="PATCH"),
     *                @OA\Property(
     *                    property="error",
     *                    type="object",
     *                    @OA\AdditionalProperties(
     *                        type="array",
     *                        @OA\Items(type="string", example="Sifariş məlumatları qeyd edilməyib.")
     *                    )
     *                ),
     *                @OA\Property(
     *                           property="result",
     *                           type="array",
     *                           @OA\Items(type="object")
     *               )
     *            )
     *        )
     * )
     */
    public function updatePatch(UpdateOrderRequest $request, int $id): SuccessApiResponse|ErrorApiResponse
    {
        return $this->update($request, $id);
    }
    /**
     * Update existing order
     *
     * @param UpdateOrderRequest $request
     * @param int $id
     * @return SuccessApiResponse|ErrorApiResponse
     */
    /**
     * @OA\Put(
     *      path="/v1/orders/{id}",
     *      operationId="updateOrderPut",
     *      tags={"Orders"},
     *      summary="Update an existing order",
     *      description="Updates an order's details",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/UpdateOrderRequest")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Order updated successfully",
     *            @OA\JsonContent(
     *                type="object",
     *                @OA\Property(property="timestamp", type="string", format="date-time", example="2025-02-15T23:21:32+04:00"),
     *                @OA\Property(property="path", type="string", example="api/v1/orders/10"),
     *                @OA\Property(property="method", type="string", example="PUT"),
     *                @OA\Property(property="error", type="string", nullable=true, example=null),
     *                @OA\Property(
     *                        property="result",
     *                        type="object",
     *                        @OA\Property(property="message", type="string", example="Order successfully updated"),
     *                        @OA\Property(property="data", type="object", ref="#/components/schemas/OrderResource")
     *                   )
     *            )
     *      ),
     *        @OA\Response(response=401, description="Unauthenticated"),
     *        @OA\Response(response=403, description="Forbidden"),
     *       @OA\Response(
     *            response=404,
     *            description="Order not found",
     *            @OA\JsonContent(
     *                type="object",
     *                @OA\Property(property="timestamp", type="string", format="date-time", example="2025-02-15T23:21:32+04:00"),
     *                @OA\Property(property="path", type="string", example="api/v1/orders/10"),
     *                @OA\Property(property="method", type="string", example="PUT"),
     *                @OA\Property(
     *                    property="error",
     *                    type="object",
     *                    example="Resource not found: Order not found with ID: 10"
     *                ),
     *                @OA\Property(property="result",type="object",example={})
     *              )
     *          ),
     *       @OA\Response(
     *            response=422,
     *            description="Unprocessable Entity",
     *            @OA\JsonContent(
     *                type="object",
     *                @OA\Property(property="timestamp", type="string", format="date-time", example="2025-02-15T23:21:32+04:00"),
     *                @OA\Property(property="path", type="string", example="api/v1/orders/10"),
     *                @OA\Property(property="method", type="string", example="PUT"),
     *                @OA\Property(
     *                    property="error",
     *                    type="object",
     *                    @OA\AdditionalProperties(
     *                        type="array",
     *                        @OA\Items(type="string", example="Sifariş məlumatları qeyd edilməyib.")
     *                    )
     *                ),
     *                @OA\Property(
     *                           property="result",
     *                           type="array",
     *                           @OA\Items(type="object")
     *               )
     *            )
     *        )
     * )
     */
    public function update(UpdateOrderRequest $request, int $id): SuccessApiResponse|ErrorApiResponse
    {
        return TransactionHelper::handleWithTransaction(function () use ($request, $id) {
            $order = $this->orderService->getOrderById($id);
            if (is_string($order)) {
                throw new ModelNotFoundException($order);
            }
//            $this->authorize('update', $order);
            $updatedOrder = $this->orderService->updateOrder($id, $request->toDTO());

            return [
                'message' => 'Order successfully updated',
                'data' => new OrderResource($updatedOrder)
            ];
        });
    }
    /**
     * Delete order
     *
     * @param int $id
     * @return SuccessApiResponse|ErrorApiResponse
     */
    /**
     * @OA\Delete(
     *      path="/v1/orders/{id}",
     *      operationId="deleteOrder",
     *      tags={"Orders"},
     *      summary="Delete an order",
     *      description="Deletes an order by ID",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="Order successfully deleted"
     *      ),
     *        @OA\Response(response=401, description="Unauthenticated"),
     *        @OA\Response(response=403, description="Forbidden"),
     *       @OA\Response(
     *            response=404,
     *            description="Order not found",
     *            @OA\JsonContent(
     *                type="object",
     *                @OA\Property(property="timestamp", type="string", format="date-time", example="2025-02-15T23:21:32+04:00"),
     *                @OA\Property(property="path", type="string", example="api/v1/orders/10"),
     *                @OA\Property(property="method", type="string", example="DELETE"),
     *                @OA\Property(
     *                    property="error",
     *                    type="object",
     *                    example="Resource not found: Order not found with ID: 10"
     *                ),
     *                @OA\Property(property="result",type="object",example={})
     *              )
     *          )
     * )
     */
    public function destroy(int $id): SuccessApiResponse|ErrorApiResponse
    {
        return TransactionHelper::handleWithTransaction(function () use ($id) {
            $order = $this->orderService->getOrderById($id);
            if (is_string($order)) {
                throw new ModelNotFoundException($order);
            }
//            $this->authorize('delete', $order);
            $this->orderService->deleteOrder($id);

            return [
                'message' => 'Order successfully deleted'
            ];
        }, 204);
    }

}