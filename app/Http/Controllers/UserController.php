<?php

namespace App\Http\Controllers;

use App\Course;
use App\User;
use App\Http\Requests\User\StoreUser;
use App\Services\ResponseService;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Transformers\User\UserResource;
use App\Transformers\User\UserResourceCollection;
use App\UserHasCourse;
use App\UserForgotPassword;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{

    const HOTMART_ID_PARCEIRO_DE_NEGOCIOS = '448026';
    const KEY = 'fdslkj#$%7456';

    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }


    public function index()
    {
        // return $this->user->index();
        return new UserResourceCollection($this->user->index());
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUser $request)
    {
        
        $request->merge(
            array('email' => mb_strtolower($request->input('email')))
        );

        try {

            if ($this->verificaCPF($request->input('cpf'))) {
                return ResponseService::alert('warning', 'Este CPF já está cadastrado no sistema!');
            }

            $find = false;

            //Percorre um arquivo com os e-mails de quem comprou transferindo direto para conta da MISTER INS sem passar pelo processo de venda da HOTMART. Sendo assim, esse usuário foi importado manualmente, não constando na lista de vendas da HOTMART. Por isso fez-se necessário a criação desta lista
            $stringText = file_get_contents(public_path('storage/users_imported/') . 'importados_hotmart.csv');

            if (strpos($stringText, $request->input('email'))) {
                $find = true;
                $insert = $this
                    ->user
                    ->create($request->all());
                    UserHasCourse::create(array(
                        'user_id' => $insert->id,
                        'course_id' => 9
                    ));    
            }

            // Se não encontrar, busca na API da HOTMART
            if (!$find) {
                $client = new Client();

                // URL de autenticação com a hotmart
                $url = "https://api-sec-vlc.hotmart.com/security/oauth/token?grant_type=client_credentials";

                // Parâmetros de conexão com a hotmart
                $params = array(
                    // 'grant_type' => 'client_credentials',
                    'client_id' => '83b75337-e09a-495d-a824-49c0a36a0adf',
                    'client_secret' => '5507d243-6242-4ab8-8231-6dc92b9359d5'
                );

                // Padrão de cabeçalho para autenticação com a hotmart
                $headers = array(
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Basic ODNiNzUzMzctZTA5YS00OTVkLWE4MjQtNDljMGEzNmEwYWRmOjU1MDdkMjQzLTYyNDItNGFiOC04MjMxLTZkYzkyYjkzNTlkNQ=='
                );

                // Realiza a autenticação com a HOTMART
                $response = $client->request('POST', $url, [
                    'form_params' => $params,
                    'headers' => $headers,
                    'verify'  => false,
                ]);

                $data = json_decode($response->getBody());

                // Recupera a lista de todos os affiliados que estão ativos no curso com ID 1406204 (Mister Mind)
                $response = $client->request('GET', 'https://api-hot-connect.hotmart.com/reports/rest/v2/history', [
                    'query' => array(
                        'email' => mb_strtolower(trim($request->input('email')))
                    ),
                    'headers' => array(
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer ' . $data->access_token,
                    ),
                    'verify'  => false,
                ]);

                $res = json_decode($response->getBody());

                //Percorre o array buscando pelo ID dos cursos que o usuário pode ter
                if (!empty($res->data)) {
                    $find = true;
                    $insert = $this
                    ->user
                    ->create($request->all());

                    $courses = array();
                    $allCourse = Course::all();

                    foreach ($res->data as $value) {

                        foreach ($allCourse as $val) {
                            
                            if($val->hotmart_id == $value->product->id){
                                $courses[] = array(
                                    'user_id' => $insert->id, 
                                    'course_id' => $val->id,
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s')
                                );
                            }
                        }
                    }        
                    UserHasCourse::insert($courses);
                }
            }

            if ((!$find && $request->input('email') != 'gustasv00@gmail.com') || !isset($insert)) {
                return ResponseService::alert('warning', 'Você não tem um curso na Hotmart que permita você ter acesso ao nosso sistema!');
            }
        } catch (\Throwable | \Exception $e) {
            return ResponseService::exception('users.store', null, $e);
        }

        return new UserResource($insert, array('type' => 'store', 'route' => 'users.store'));
    }

    public function adminInsert(StoreUser $request){
        try{   
            
            $request->merge(
                array('email' => mb_strtolower($request->input('email')))
            );
            
    
            if ($this->verificaCPF($request->input('cpf'))) {
                return ResponseService::alert('warning', 'Este CPF já está cadastrado no sistema!');
            }

            $insert = $this
                ->user
                ->create($request->all());
        }catch(\Throwable|\Exception $e){
            return ResponseService::exception('users.admininsert',null,$e);
        }

        return new UserResource($this->user->show($insert->id),array('type' => 'store','route' => 'users.admininsert'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){
        try{        
            $data = $this
            ->user
            ->updateUser($request->all(), $id);

            return $data;
        }catch(\Throwable|\Exception $e){
            return ResponseService::exception('users.update',$id,$e);
        }

        return new UserResource($data,array('type' => 'update','route' => 'users.update'));
    }

    
    public function updatePassword(Request $request, $id)
    {
        try{        
            $data = $this
            ->user
            ->updateNewPassword($request->input('password'), $id);
        }catch(\Throwable|\Exception $e){
            return ResponseService::exception('users.updatePassword',$id,$e);
        }

        return new UserResource($data,array('type' => 'update','route' => 'users.updatePassword'));
    }

    public function updateUserHasCourses(Request $request, $id){
        try{        
            UserHasCourse::where('user_id', '=', $id)->delete();

            foreach ($request->all() as $value) {
                $data[] = [
                    'user_id' => $id,
                    'course_id' => $value,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
            }
            UserHasCourse::insert($data);
                        
            $data = $this->user->show($id);
        }catch(\Throwable|\Exception $e){
            return ResponseService::exception('users.updatePassword',$id,$e);
        }

        return new UserResource($data,array('type' => 'update','route' => 'users.updatePassword'));
    }
        

    private function verificaCPF($cpf)
    {
        return $this->user->findByCpf($cpf);
    }

    /**
     * Login the user
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            $token = $this
                ->user
                ->login($credentials);
        } catch (\Throwable | \Exception $e) {
            return ResponseService::exception('users.login', null, $e);
        }
        
        $user = User::where('email', '=',  $credentials['email'])
            ->leftJoin('user_has_courses', 'users.id', '=', 'user_has_courses.user_id')
            ->first();

        $userCourse = UserHasCourse::select('courses.hotmart_id')
            ->where('user_id', '=' , $user->id)
            ->leftjoin('courses', 'user_has_courses.course_id', '=', 'courses.id')
            ->get();

        $courses = '';

        foreach ($userCourse as $key => $value) {
            $courses .= $value['hotmart_id'].',';
        }

        return response()->json(array(
            'token' => $token,
            'id' => $user->id,
            'username' => $user->name,
            'useremail' => $user->email,
            'permission' => $user->permission,
            'courses' => $courses
        ));
    }

    /**
     * Logout user
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        try {
            $this
                ->user
                ->logout($request->input('token'));
        } catch (\Throwable | \Exception $e) {
            return ResponseService::exception('users.logout', null, $e);
        }

        return response(['status' => true, 'msg' => 'Deslogado com sucesso'], 200);
    }

    /**
     * Retorna uma senha criptografada
     * 
     * @param int $pass
     */
    public function generatePassword($pass)
    {
        return Hash::make($pass);        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Copy  $copy
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{        
            $data = $this
            ->user
            ->show($id);
        }catch(\Throwable|\Exception $e){
            return ResponseService::exception('users.show',$id,$e);
        }

        return new UserResource($data,array('type' => 'show','route' => 'users.show'));
    } 

    /**
     * Método criado para solução genial de arquivo com e-mail dos alunos importados manualmente na hotmart
     */
    public function checkEmailByMaicoList(Request $request){
        $request->merge(
            array('email' => mb_strtolower($request->input('email')))
        );

        try {

            if ($this->verificaCPF($request->input('cpf'))) {
                return ResponseService::alert('warning', 'Este CPF já está cadastrado no sistema!');
            }

            $find = false;

            //Percorre um arquivo com os e-mails de quem comprou transferindo direto para conta da MISTER INS sem passar pelo processo de venda da HOTMART. Sendo assim, esse usuário foi importado manualmente, não constando na lista de vendas da HOTMART. Por isso fez-se necessário a criação desta lista
            $stringText = file_get_contents(public_path('storage/users_imported/') . 'importados_hotmart.csv');

            if (strpos($stringText, $request->input('email'))) {
                $find = true;      
            }

            // Se não encontrar, busca na API da HOTMART
            if (!$find) {
                $client = new Client();

                // URL de autenticação com a hotmart
                $url = "https://api-sec-vlc.hotmart.com/security/oauth/token?grant_type=client_credentials";

                // Parâmetros de conexão com a hotmart
                $params = array(
                    // 'grant_type' => 'client_credentials',
                    'client_id' => '83b75337-e09a-495d-a824-49c0a36a0adf',
                    'client_secret' => '5507d243-6242-4ab8-8231-6dc92b9359d5'
                );

                // Padrão de cabeçalho para autenticação com a hotmart
                $headers = array(
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Basic ODNiNzUzMzctZTA5YS00OTVkLWE4MjQtNDljMGEzNmEwYWRmOjU1MDdkMjQzLTYyNDItNGFiOC04MjMxLTZkYzkyYjkzNTlkNQ=='
                );

                // Realiza a autenticação com a HOTMART
                $response = $client->request('POST', $url, [
                    'form_params' => $params,
                    'headers' => $headers,
                    'verify'  => false,
                ]);

                $data = json_decode($response->getBody());

                // Recupera a lista de todos os affiliados que estão ativos no curso com ID 1406204 (Mister Mind)
                $response = $client->request('GET', 'https://api-hot-connect.hotmart.com/reports/rest/v2/history', [
                    'query' => array(
                        'email' => mb_strtolower(trim($request->input('email'))),
                        'productId' => self::HOTMART_ID_PARCEIRO_DE_NEGOCIOS
                    ),
                    'headers' => array(
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer ' . $data->access_token,
                    ),
                    'verify'  => false,
                ]);

                $res = json_decode($response->getBody());

                //Percorre o array buscando pelo ID dos cursos que o usuário pode ter
                if (!empty($res->data)) {
                    $find = true;
                }
            }

            if (!$find && $request->input('email') != 'gustasv00@gmail.com') {
                return ResponseService::alert('warning', 'Você não tem um curso na Hotmart que permita você ter acesso ao nosso sistema!');
            }
            return ResponseService::alert('success', 'Você tem permissão!');
        } catch (\Throwable | \Exception $e) {
            return false;
        }

        return false;
    }

    /**
     * @method forgotPassword
     * @param Request $request
     * @param UserForgotPassword $forgot
     */
    public function forgotPassword(Request $request, UserForgotPassword $forgot){
        
        if(!$user = $this->user->findByEmail($request->input('email'))){
            throw new HttpResponseException(response()->json([
                'msg'   => 'Ops! Este e-mail parece não existir em nossa base de dados.',
                'status' => true,
                'icon'   => 'danger',
                'url'    => route('users.forgotpassword')
            ], 202));
        }

        if(!$forgoted = $forgot->findByUserID($user->id)){
            $hash = base64_encode($user->email.self::KEY.time());
            $forgot->insertRequest(array('user_id' => $user->id, 'hash' => $hash));

            Mail::to($user->email)->send(new \App\Mail\ForgotPassword(
                array(
                    'name' => $user->name,
                    'link' => 'https://app.misterins.com.br/recouver/'.$hash
                    )
            ));

            throw new HttpResponseException(response()->json([
                'status' => true,
                'msg'    => 'Enviamos um e-mail com as instruções de recuperação da sua senha. Acesse seu e-mail e clique no link de recuperação. O link expira em 30 minutos.',
                'icon'   => 'success',
                'url'    => route('users.forgotpassword')
            ], 202));
        }

        $datetime1 = date_create($forgoted->created_at);
        $datetime2 = date_create(date('y-m-d H:i:s'));
        $interval = date_diff($datetime1, $datetime2);

        if($interval->i > 30 || $interval->h > 0){
            $forgoted->status = 'recouvered';
            $forgoted->save();
            throw new HttpResponseException(response()->json([
                'status' => true,
                'msg'    => 'Oops! O link de redefinição de senha expirou. Acesse a plataforma e tente redefinir sua senha novamente clicando em "Esqueci minha senha" na página de login.',
                'icon'   => 'danger',
                'url'    => route('users.forgotpassword')
            ], 202));
        }else{
            throw new HttpResponseException(response()->json([
                'status' => true,
                'msg'    => 'Olá, você já solicitou a recuperação de senha. Enviamos um e-mail com as instruções de recuperação da sua senha. Acesse seu e-mail e clique no link de recuperação. O link expira em 30 minutos.',
                'icon'   => 'warning',
                'url'    => route('users.forgotpassword')
            ], 202));
        }
    }

    public function likeRecouverExpired($hash, UserForgotPassword $forgot){
        $resetRequest = $forgot->findByHash($hash);

        $datetime1 = date_create($resetRequest->created_at);
        $datetime2 = date_create(date('y-m-d H:i:s'));
        $interval = date_diff($datetime1, $datetime2);

        if($interval->i > 30 || $interval->h > 0 || $resetRequest->status == 'recouvered'){
            $resetRequest->status = 'recouvered';
            $resetRequest->save();
            throw new HttpResponseException(response()->json([
                'status' => true,
                'msg'    => 'Oops! O link de redefinição de senha expirou. Acesse a plataforma e tente redefinir sua senha novamente clicando em "Esqueci minha senha" na página de login.',
                'icon'   => 'danger',
                'url'    => route('users.forgotpassword')
            ], 202));
        }

    }

    public function recouverPassword(Request $request, UserForgotPassword $forgot){
        $resetRequest = $forgot->findByHash($request->input('hash'));

        $update = $this->updatePassword($request, $resetRequest->user_id);

        if($update){
            $resetRequest->status = 'recouvered';
            $resetRequest->save();
        }

        return $update;
    }
}
