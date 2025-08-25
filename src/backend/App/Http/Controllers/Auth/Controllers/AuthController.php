<?php

namespace App\Http\Controllers\Auth\Controllers;

use App\Http\Controllers\Auth\{Requests\LoginRequest,
    Requests\RegisterRequest,
    Requests\UpdateMeRequest,
    Resources\AuthResource,
    Services\AuthService,
    Services\OtpService};
use App\Http\Controllers\Controller;
use App\Http\Controllers\User\Models\User;
use App\Http\Responses\{ErrorApiResponse, ErrorUnauthenticatedResponse, SuccessApiResponse};
use App\Support\Helpers\TransactionHelper;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * @OA\Tag(
 *      name="Auth",
 *      description="API Endpoints of Auth Management System"
 * )
 */

class AuthController extends Controller
{

    /**
     * @param AuthService $authService
     * @param OtpService $otpService
     */
    public function __construct(
        private readonly AuthService $authService,
        private readonly OtpService $otpService
    ){}

    /**
     * Register a new user
     * 
     * @OA\Post(
     *     path="/v1/auth/register",
     *     summary="Register a new user",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password", "password_confirmation"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="user", type="object"),
     *             @OA\Property(property="authorization", type="object",
     *                 @OA\Property(property="access_token", type="string"),
     *                 @OA\Property(property="token_type", type="string", example="Bearer"),
     *                 @OA\Property(property="expires_in", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function register(RegisterRequest $request): SuccessApiResponse|ErrorApiResponse
    {
        return TransactionHelper::handleWithTransaction(function () use ($request) {
            $user = $this->authService->register($request->validated());
            $token = auth('api')->login($user);
            return [
                "user" => new AuthResource($user),
                "authorization" => [
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                    'expires_in' => auth('api')->factory()->getTTL() * 60
                ]
            ];
        });
    }

    /**
     * Login user and get JWT token
     * 
     * @OA\Post(
     *     path="/v1/auth/login",
     *     summary="Login user and get JWT token",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="access_token", type="string"),
     *             @OA\Property(property="token_type", type="string", example="Bearer"),
     *             @OA\Property(property="expires_in", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */
    public function login(LoginRequest $request): SuccessApiResponse|ErrorUnauthenticatedResponse|ErrorApiResponse
    {
        try {
            $credentials = $request->only('email', 'password');

            // Debug: Check if user exists
            $user = User::where('email', $credentials['email'])->first();
            
            if (!$user) {
                return ErrorApiResponse::make(['error' => 'No user found with this email address.'], 404);
            }

            // Debug: Check password
            if (!Hash::check($credentials['password'], $user->password)) {
                return ErrorApiResponse::make(['error' => 'Incorrect password.'], 401);
            }

            // Attempt to authenticate
            if (!$token = auth('api')->attempt($credentials)) {
                return ErrorApiResponse::make(['error' => 'Authentication failed.'], 401);
            }

            return $this->respondWithToken($token);

        } catch (Exception $ex) {
            return ErrorApiResponse::make(['error' => $ex->getMessage()]);
        }
    }

    /**
     * Get authenticated user details
     * 
     * @OA\Get(
     *     path="/v1/auth/me",
     *     summary="Get authenticated user details",
     *     tags={"Auth"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="User details retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function me(): SuccessApiResponse|ErrorApiResponse
    {
        return TransactionHelper::handleWithTransaction(static function () {
            $user = auth('api')->user();
            if (!$user) {
                return ErrorApiResponse::make('Unauthorized', 401);
            }
            return new AuthResource($user);
        });
    }

    /**
     * Update authenticated user profile
     * 
     * @OA\Put(
     *     path="/v1/auth/me",
     *     summary="Update authenticated user profile",
     *     tags={"Auth"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profile updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="user", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function updateMe(UpdateMeRequest $request): SuccessApiResponse|ErrorApiResponse
    {
        return TransactionHelper::handleWithTransaction(callback: function () use ($request) {
            $user = $this->authService->updateUser(
                    user: auth('api')->user(),
                    data: $request->validated()
                );
            return ["user" =>new AuthResource($user)];
        });
    }

    /**
     * Logout user (Invalidate the token)
     * 
     * @OA\Post(
     *     path="/v1/auth/logout",
     *     summary="Logout user (Invalidate the token)",
     *     tags={"Auth"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successfully logged out"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function logout(): SuccessApiResponse|ErrorApiResponse
    {
        return TransactionHelper::handleWithTransaction(static function () {
            auth('api')->logout();
            return 'Successfully logged out';
        });
    }

    /**
     * Refresh JWT token
     * 
     * @OA\Post(
     *     path="/v1/auth/refresh",
     *     summary="Refresh JWT token",
     *     tags={"Auth"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Token refreshed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="access_token", type="string"),
     *             @OA\Property(property="token_type", type="string", example="Bearer"),
     *             @OA\Property(property="expires_in", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function refresh(): SuccessApiResponse|ErrorApiResponse
    {
        return TransactionHelper::handleWithTransaction(callback: function () {
            // Get a new token based on the current one
            $newToken = auth('api')->refresh();

            // Only logout after getting the new token
            auth('api')->logout();

            return $this->respondWithToken($newToken);
        });
    }

    /**
     * Generate OTP for user
     * 
     * @OA\Post(
     *     path="/v1/auth/generate-otp",
     *     summary="Generate OTP for user",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP generated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="OTP has been generated successfully"),
     *             @OA\Property(property="otp", type="string", example="123456")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
    public function generateOtp(Request $request): SuccessApiResponse|ErrorApiResponse
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        try {
            $otpRecord = $this->otpService->generateOtp($request->email);
            return SuccessApiResponse::make([
                'message' => 'OTP has been generated successfully',
                'otp' => $otpRecord->otp // Include OTP in response for testing
            ]);
        } catch (Exception $e) {
            return ErrorApiResponse::make($e->getMessage());
        }
    }

    /**
     * Verify OTP for user
     * 
     * @OA\Post(
     *     path="/v1/auth/verify-otp",
     *     summary="Verify OTP for user",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "otp"},
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="otp", type="string", example="123456")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP verified successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="OTP verified successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid or expired OTP"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function verifyOtp(Request $request): SuccessApiResponse|ErrorApiResponse
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6'
        ]);

        try {
            $isValid = $this->otpService->validateOtp($request->email, $request->otp);
            
            if (!$isValid) {
                return ErrorApiResponse::make('Invalid or expired OTP', 400);
            }

            return SuccessApiResponse::make('OTP verified successfully');
        } catch (Exception $e) {
            return ErrorApiResponse::make($e->getMessage());
        }
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return SuccessApiResponse
     */
    protected function respondWithToken(string $token): SuccessApiResponse
    {
        return SuccessApiResponse::make([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ]);
    }
}
