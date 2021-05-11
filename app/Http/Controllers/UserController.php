<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Requests\User\StoreUser;
use App\Services\ResponseService;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Transformers\User\UserResource;

class UserController extends Controller
{
    private $user;
    private $product_o_socio;

    public function __construct(User $user){
        $this->user = $user;
        $this->product_o_socio = "1442311";
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUser $request)
    {
        try{ 
            $client = new Client();

            // URL de autenticação com a hotmart
            $url = "https://api-sec-vlc.hotmart.com/security/oauth/token?grant_type=client_credentials";

            // Parâmetros de conexão com a hotmart
            $params = array(                      
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
            $response = $client->request('GET', 'https://api-hot-connect.hotmart.com/reports/rest/v2/history',[
                'query' => array(
                    'productId' => "1442311"
                ),
                'headers' => array(
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer '.$data->access_token,
                ),
                'verify'  => false,
            ]);

            $response = json_decode($response->getBody()); 
            $find = false;

            // Laço para verificar se o email que está tentando se cadastrar existe na hotmart
            foreach($response->data as $value){
                if($value->buyer->email == $request->input('email')){
                    $user = $this
                    ->user
                    ->create($request->all());
                    $find = true;
                    break;
                }
            }

            if(!$find){
                return ResponseService::alert('warning','Você não tem o curso na Hotmart que permite você ter acesso à nosso sistema!'); 
            }
            
        }catch(\Throwable|\Exception $e){
            return ResponseService::exception('users.store',null,$e);
        }

        return new UserResource($user,array('type' => 'store','route' => 'users.store'));
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
        } catch (\Throwable|\Exception $e) {
            return ResponseService::exception('users.login',null,$e);
        }

        $user = User::where('email', '=',  $credentials['email'])->first();

        return response()->json(array('token' => $token, 'username' => $user->name, 'permission' => $user->permission));
    }

    /**
     * Logout user
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request) {
        try {
            $this
            ->user
            ->logout($request->input('token'));
        } catch (\Throwable|\Exception $e) {
            return ResponseService::exception('users.logout',null,$e);
        }

        return response(['status' => true,'msg' => 'Deslogado com sucesso'], 200);
    }

}
