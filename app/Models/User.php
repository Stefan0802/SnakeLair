<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Http\Requests\UpdAvatarRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;

class User extends Authenticatable
{

    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'firstName',
        'lastName',
        'email',
        'avatar',
        'role',
        'password',
    ];


    protected $hidden = [
        'password',
    ];


    public function isAdmin()
    {
        if (Auth::user()->role == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function comment()
    {
        return $this->hasMany(Comment::class);
    }

    public function post()
    {
        return $this->hasMany(Post::class);
    }

    public function sentMessages()
    {
        return $this->hasMany(Chat::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Chat::class, 'receiver_id');
    }

    public function sentFriend()
    {
        return $this->hasMany(Friend::class, 'user_id');
    }

    public function receivedFriend()
    {
        return $this->hasMany(Friend::class, 'friend_id');
    }

    public static function register($request): JsonResponse
    {
        $validated = $request->validated();

        $user = self::create([
            'firstName' => $validated['firstName'],
            'lastName' => $validated['lastName'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $data['token'] = $user->createToken($user->email)->plainTextToken;

        $response = [
            'status' => 'success',
            'message' => 'Пользователь создан',
            'data' => $data,
            'user' => $user,
        ];

        return response()->json($response, 201);
    }

    // Метод для авторизации
    public static function login($request): JsonResponse
    {
        $user = self::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Неверные данные'
            ], 401);
        }

        $token = $user->createToken($request->email)->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Пользователь успешно авторизован',
            'data' => [
                'token' => $token,
                'user' => $user,
            ],
        ], 200);
        exit();
    }

    // Метод для выхода
//    public function logout(Request $request)
//    {
//        return User::logout($request);
//    }
    public static function logout($request): JsonResponse
    {
        if (!$request->user()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Пользователь не авторизован'
            ], 401);
        }

        $request->user()->tokens()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Пользователь успешно вышел'
        ], 200);
    }

    public static function profile($request, $id = null): JsonResponse
    {
        $loggedInUser  = $request->user();

        $userId = $id ?? $loggedInUser ->id;

        // Убедитесь, что вы загружаете правильные отношения
        $user = User::with(['sentFriend', 'receivedFriend', 'post'])->find($userId);

        if ($user) {
            $response = [
                'status' => 'success',
                'user' => $user,
            ];
            return response()->json($response, 200);
        }
        return response()->json(['status' => 'error', 'message' => 'Пользователь не найден'], 404);
    }

    public static function editPassword($request): JsonResponse
    {
        $loggedInUser = Auth::user();

        $user = User::where('email', $loggedInUser->email)->first();

        if ($user) {
            if ($request->has('password')) {
                $user->password = Hash::make($request->password);
            }
        }
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Пароль изменен',
            'user' => $user
        ]);
    }
    public static function editorProfile($request): JsonResponse
    {
        $loggedInUser = Auth::user();

        $user = User::where('email', $loggedInUser->email)->first();

        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Данные изменены',
            'user' => $user
        ], 200);
    }
    public static function uploadAvatar( $request): JsonResponse
    {
        $loggedInUser = Auth::user();

        $user = User::where('email', $loggedInUser->email)->first();

        if ($user) {
            $avatar = time() . '.' . $request->avatar->getClientOriginalExtension();

            $user->avatar = $request->avatar->move(public_path('uploads/images'),$avatar);

            $user->save();

            $response = ['status' => 'success'];

            return response()->json($response, 200);
        }
        $response = ['status' => 'error', 'message' => 'Пользователь не найден'];
        return response()->json($response, 404);
    }

    public static function getUsers( $request): JsonResponse
    {
        $loggedInUser  = $request->user();

        if (!$loggedInUser ->isAdmin()) {
            return response()->json(['status' => 'access denied', 'message' => 'Недостаточно прав'], 403);
        }

        // Получаем всех пользователей
        $users = User::all()->map(function ($user) {
            return [
                'id' => $user->id,
                'firstName' => $user->firstName,
                'lastName' => $user->lastName,
                'email' => $user->email,
                'role' => $user->role,
            ];
        });

        return response()->json([
            'status' => 'success',
            'users' => $users,
        ], 200);
    }

    public static function editUser( $request, $id): JsonResponse
    {
        $loggedInUser  = $request->user();

        if (!$loggedInUser ->isAdmin()) {
            return response()->json(['status' => 'access denied', 'message' => 'Недостаточно прав'], 403);
        }

        $user = User::find($id);

        if ($request->has('firstName')) {
            $user->firstName = $request->firstName;
        }
        if ($request->has('lastName')) {
            $user->lastName = $request->lastName;
        }
        if ($request->has('email')) {
            $user->email = $request->email;
        }
        if ($request->has('role')) {
            $user->role = $request->role;
        }

        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Данные изменены',
            'user' => $user
        ], 200);
    }
    public static function delUser($request, $id): JsonResponse
    {
        $loggedInUser  = $request->user();

        if (!$loggedInUser ->isAdmin()) {
            return response()->json(['status' => 'access denied', 'message' => 'Недостаточно прав'], 403);
        }

        $user = User::find($id);

        $user -> delete();

        return response()->json([
            'status'=>'success',
            'message'=>'пользователь удален'
        ]);
    }
}

