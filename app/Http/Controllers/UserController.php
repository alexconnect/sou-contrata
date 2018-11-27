<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;
use App\User;
use function Sodium\add;
use Validator;
use Illuminate\Support\Facades\Crypt;
use App\Candidate;

class UserController extends Controller {

    protected $url;

    public function __construct(UrlGenerator $url) {
        $this->url = $url;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->session()->flush();

        $user = new User();

        $user->id = $request->id;
        $user->name = $request->name;
        $user->login = $request->login;
        $user->password = Crypt::encrypt($request->password);
        $user->email = $request->email;

        $user->save();
        $userSession = [
            'id' => $user->id,
            'user' => $user->name,
            'email' => $user->email,
        ];

        $request->session()->put('user', $userSession);

        return redirect('/personal-data');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        //
    }

    /**
     * Método restponsável pelo acesso ao perfil do candidato cadastro.
     */

    public function login(Request $request)
    {
        $request->session()->flush();

         $login = User::where('password', '=', Crypt::encrypt($request->password))
            ->orWhere('email', $request->email)
            ->first();

        if($login && isset($login->email)) {
            $userSession = [
                'id' => $login->id,
                'user' => $login->name,
                'email' => $login->email,
            ];
            $request->session()->put('user', $userSession);
            $candidados = Candidate::where('user_id', $login->id)->get();

            if(empty($candidados->id)) {
                return redirect('/personal-data');
            } else {
                return redirect('/vacancy');
            }

        } else {
            return redirect('/login');
        }

    }

    public function logoff(Request $request) 
    {
        $request->session()->flush();
        return redirect('/login');
    }
}
