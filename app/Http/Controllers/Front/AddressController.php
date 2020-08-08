<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\User\User as UserModel;
use App\Helper\Base as Helper;
use App\Libs\Service\PositionService;


class AddressController extends BaseController
{

     /**
     * 收获地址
     *
     * @return string
     */
    public function index(Request $request)
    {
        $provices = PositionService::provices();
        $user = \Auth::user();
        $address_books = $user->address()->orderBy('id', 'desc')->get();
        $address_books = $address_books->toArray();
        $view = view('account.address.index',[
            'user' => $user,
            'title' => '收获地址',
            'address_books' => $address_books,
            'provices' => $provices
        ]);
        return $view;
    }
}