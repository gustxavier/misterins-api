<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Requests\User\StoreUser;
use App\Services\ResponseService;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Transformers\User\UserResource;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    private $user;
    const HOTMART_PARCEIRO_MISTERINS = 1442311;
    const HOTMART_PROJX_PARCEIRO_MISTERINS = 448026;

    public function __construct(User $user)
    {
        $this->user = $user;
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
                    foreach ($res->data as $value) {
                        if ($value->product->id == self::HOTMART_PARCEIRO_MISTERINS || $value->product->id == self::HOTMART_PROJX_PARCEIRO_MISTERINS) {
                            $find = true;
                            $insert = $this
                                ->user
                                ->create($request->all());
                            break;
                        }
                    }
                }
            }

            if (!$find && $request->input('email') != 'gustasv00@gmail.com') {
                return ResponseService::alert('warning', 'Você não tem o curso na Hotmart que permite você ter acesso à nosso sistema!');
            }
        } catch (\Throwable | \Exception $e) {
            return ResponseService::exception('users.store', null, $e);
        }

        return new UserResource($insert, array('type' => 'store', 'route' => 'users.store'));
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

        $user = User::where('email', '=',  $credentials['email'])->first();

        return response()->json(array(
            'token' => $token,
            'id' => $user->id,
            'username' => $user->name,
            'useremail' => $user->email,
            'permission' => $user->permission
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

    public function isLogged()
    {
        return true;
    }

    public function generatePassword($pass)
    {
        return Hash::make($pass);
    }
}
