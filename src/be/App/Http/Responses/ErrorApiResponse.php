<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\MessageBag;
use Override;
use Throwable;

class ErrorApiResponse extends ApiErrorResponse
{

    /**
     * @param mixed $dataOrMessage
     * @param int|null $code
     */
    public static function make(mixed $dataOrMessage = [], ?int $code = null): static
    {
        if ($code === 422 && $dataOrMessage instanceof MessageBag) {
            $dataOrMessage = ['errors' => $dataOrMessage->toArray()];
        } elseif ($code === 422 && is_array($dataOrMessage)) {
            $dataOrMessage = ['errors' => $dataOrMessage];
        } elseif (is_string($dataOrMessage)) {
            $dataOrMessage = ['error' => $dataOrMessage];
        }

        return parent::make($dataOrMessage, $code);
    }

    /**
     * @param $request
     * @return JsonResponse
     */
    public function toResponse($request): JsonResponse
    {
        $response = parent::toResponse($request);
        $data = (array)$response->getData();

        if (is_array($this->dataOrMessage) && isset($this->dataOrMessage['error'])) {
            $data['error'] = $this->dataOrMessage['error'];
            $data['result'] = [];
        }

        $response->setData($data);
        return $response;
    }


    /**
     * Format error message based on input type
     *
     * @param mixed $error
     * @return string|array
     */
    protected function formatErrorMessage(mixed $error): string|array
    {
        if ($error instanceof Throwable) {
            return $error->getMessage();
        }

        if (is_array($error)) {
            return $error['errors']??$error['error'];
        }

        if (is_object($error) && method_exists($error, '__toString')) {
            return (string)$error;
        }

        return (string)$error ?: $this->defaultErrorMessage();
    }

    /**
     * @return string
     */
    #[Override] protected function defaultErrorMessage(): string
    {
        return 'No user found with this email address.';
    }

    /**
     * @return int
     */
    #[Override] protected function defaultResponseCode(): int
    {
        return 500;
    }
}