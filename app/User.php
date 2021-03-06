<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use JWTAuth;
use Carbon\Carbon;
use Tymon\JWTAuth\Exceptions\JWTException;

class User extends Authenticatable implements JWTSubject
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'cpf', 'facebook', 'instagram','password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function index(){
        return $this->orderBy('name')->get();
    }

    public function show($id){
        $show = $this->find($id);
 
        if (!$show) {
            throw new \Exception('Nada Encontrado', -404);
        }

        return $show;
    }

    public function create($fields)
    {   
        return parent::create([
            'name' => $fields['name'],
            'email' => $fields['email'] ,
            'cpf' => $fields['cpf'] ,
            'facebook' => $fields['facebook'] ,
            'instagram' => $fields['instagram'] ,
            'password' => Hash::make($fields['password']),
        ]);
    }

    public function updateUser($fields, $id)
    {
        unset($fields['password']);

        $user = $this->show($id);
        $user->update($fields);

        return $user;
    }

    public function updateNewPassword($password, $id)
    {

        $user = $this->show($id);
        $user->update(array('password' => Hash::make($password)));

        return $user;
    }

    public function findByCpf($cpf){
        return $this->where('cpf', '=', $cpf)->first();
    }

    public function findByEmail($email){
        return $this->where('email', '=', $email)->first();
    }

    public function login($credentials){
        if (!$token = JWTAuth::attempt($credentials, ['exp' => Carbon::now()->addDays(1)->timestamp])) {
            throw new \Exception('Credencias incorretas, verifique-as e tente novamente.', -401);
        }
        return $token;
    }

    public function logout($token){
        if (!JWTAuth::invalidate($token)) {
            throw new \Exception('Erro. Tente novamente.', -404);
        }
    }

    public function validateToken($token){
        return JWTAuth::user();
        if (!JWTAuth::invalidate($token)) {
            throw new \Exception('Erro. Tente novamente.', -404);
        }
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function tasklist(){
        return $this->hasMany('App\TaskList', 'user_id', 'id');
    }

    public function tasks(){
        return $this->hasMany('App\Tasks');
    }

    public function lives(){
        return $this->hasMany('App\Live');
    }

    public function liveComments(){
        return $this->hasMany('App\LiveComment');
    }
}
