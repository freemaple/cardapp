<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use App\Libs\Service\ProductDispalyService;
use Validator;
use Auth;

class WishController extends BaseController
{
	
     /**
     * 钱包
     *
     * @return void
    */
    public function getList(Request $request)
    {
        $form = $request->all();

        $user = Auth::user();

        $pageSize = config('paginate.wish.list', 100);

        $user_id = $user->id;

        $products = ProductDispalyService::getWishProduct($user_id, $pageSize);

        $view = view('account.wish.block.list')->with('products', $products);

        $result = [];

        $result['code'] = 'Success';

        $result['data'] = $products;

        $result['view'] = $view->render();

        return json_encode($result);

    }
}
