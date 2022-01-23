<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class FormController extends Controller
{
    public function getform(){

        $code_form = Str::random(20);

        session(['code_form' => $code_form]);

        return view('welcome', [
            'code' => $code_form
        ]);

    }

    public function sendform(Request $request){

        $code_form = session('code_form');

        //Примитивная проверка, если поле name заполнено, то скорее всего бот + проверка на совпадение созданного кода в сессии
        if(isset($request->name) && $request->name || $request->code !== $code_form){
            return response()->json([
                'error' => 'Ошибка обработки формы'
            ], 401);
        }

        //Если пользователь не согласился с соглашением
        if(!$request->privacy){
            return response()->json([
                'error' => 'Необходимо принять условия соглашения'
            ], 401);
        }

        //Штатная валидация
        $this->validate($request, [
            'firstName' =>  'required|max:50',
            'lastName' =>   'required|max:50',
            'email' =>      'required|email',
            'phone' =>      'required|min:10|max:10',
            'message' =>    'max:100',
            'privacy' =>    'required|boolean'
        ]);

        //Если все этапы пройдены, то можно записать данные в базу или оптавить сообщения на почту или в телегу,
        //в общем, на усмотрение заказчика согласно бизнес логики
        return response()->json([
            'success' => 'Ваше сообщение успешно отправлено!'
        ], 200);

    }
}
