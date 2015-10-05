<?php

// play ground
//

Route::get('/', 'HomeController@index');
Route::get('about', 'HomeController@about');

/*********** FILTER ***********/
Route::filter('office', function () {
    // $path = Request::path();
    // if ($path != 'office/login') {
    //     if (!SysUser::chkLogin()) {
    //         $re = Tools::reFalse(10003, '请先登录');
    //         return Response::json($re);
    //     }
    // }
});
Route::when('office/*', 'office');
/*********** FILTER ***********/

/*********** API ***********/


/* V1 */
Route::group(['domain' => Config::get('app.subdomain.api')], function () {

    /* APP CONFIG START*/
    Route::get('app/config', 'AppController@getConfig');
    /* APP CONFIG END*/

    /* ME START*/
    Route::get('v0/user/me', 'MeController@me');
    Route::get('v0/user/me/posts', 'MeController@myPosts');
    Route::get('v0/user/me/reply', 'MeController@myReply');
    Route::get('v0/user/me/praise', 'MeController@myPraise');
    Route::get('v0/user/me/resetpass', 'MeController@resetPass');
    Route::get('v0/user/me/followers', 'MeController@myFollowers');
    Route::get('v0/user/me/followings', 'MeController@myFollowings');
    /* ME END*/

    /* ME PROFILE START*/
    Route::get('v0/user/me/profile/check', 'MeController@profileCheck');
    Route::post('v0/user/me/profile/detail', 'MeController@postDetail');
    Route::post('v0/user/me/profile/contact', 'MeController@postContact');
    Route::post('v0/user/me/profile/card', 'MeController@postCard');
    Route::get('v0/user/me/profile/detail', 'MeController@getDetail');
    Route::get('v0/user/me/profile/contact', 'MeController@getContact');
    Route::get('v0/user/me/profile/card', 'MeController@getCard');
    Route::put('v0/user/me/profile/base', 'MeController@putUserBase');
    Route::get('v0/user/me/profile/base', 'MeController@getUserBase');
    /* ME PROFILE END*/

    /* ME MARKET START*/
    Route::post('v0/user/me/booth', 'MeController@postBooth');
    Route::get('v0/user/me/booth', 'MeController@listBooth');
    Route::get('v0/user/me/booth/{id}', 'MeController@booth');
    Route::put('v0/user/me/booth/{id}/desc', 'MeController@putBoothDesc');
    Route::put('v0/user/me/booth/{id}/status', 'MeController@putBoothStatus');
    Route::get('v0/user/me/booth/{id}/status', 'MeController@getBoothStatus');
    Route::post('v0/user/me/product', 'MeController@postProduct');
    Route::get('v0/user/me/product', 'MeController@getProducts');
    Route::get('v0/user/me/product/{id}', 'MeController@getProduct');
    Route::put('v0/user/me/product/{id}', 'MeController@updateProduct');
    Route::get('v0/user/me/product/{id}/on', 'MeController@productOn');
    Route::post('v0/user/me/product/sort', 'MeController@updateProductSort');
    Route::post('v0/user/me/product/discount', 'MeController@updateProductDiscount');
    Route::get('v0/user/me/orders', 'MeController@listOrders');
    Route::get('v0/user/me/order/{id}', 'MeController@getOrder');
    Route::get('v0/user/me/orders/count', 'MeController@countOrders');
    Route::get('v0/user/me/sells', 'MeController@listSellOrders');
    Route::get('v0/user/me/sells/count', 'MeController@countSellOrders');
    Route::post('v0/user/me/order/deliver', 'MeController@deliverOrder');
    Route::post('v0/user/me/order/confirm', 'MeController@confirmOrder');
    Route::get('v0/user/me/praise/promo', 'MeController@listPraisePromo');
    Route::get('v0/user/me/following/booth', 'MeController@listFollowingBooth');
    Route::get('v0/user/me/wallet', 'MeController@showWallet');
    Route::post('v0/user/me/wallet/draw', 'MeController@postWalletDraw');
    Route::get('v0/user/me/wallet/draw', 'MeController@listWalletDraw');
    Route::get('v0/user/me/wallet/draw/{id}', 'MeController@getWalletDraw');
    Route::post('v0/user/me/payment/wechat', 'MeController@postPaymentWechat');
    Route::post('v0/user/me/payment/alipay', 'MeController@postPaymentAlipay');
    Route::post('v0/user/me/payment/bank', 'MeController@postPaymentBank');
    Route::get('v0/user/me/financial/report', 'MeController@financialReport');
    Route::post('v0/user/me/financial/report/confirm', 'MeController@confirmFinancialReport');
    /* ME MARKET END*/

    /* ME FRIEND START*/
    Route::get('v0/user/me/friend', 'MeFriendController@index');
    Route::get('v0/user/me/friend/remove', 'MeFriendController@remove');
    Route::get('v0/user/me/friend/confirm', 'MeFriendController@confirm');
    Route::post('v0/user/me/friend/invite', 'MeFriendController@invite');
    Route::get('v0/user/me/friend/invite', 'MeFriendController@indexInvite');
    Route::get('v0/user/me/friend/invite/remove', 'MeFriendController@removeInvite');
    Route::get('v0/user/me/friend/check', 'MeFriendController@check');
    /* ME FRIEND END*/

    /* USER START*/
    Route::get('v0/user/search', 'UserController@search');
    Route::get('v0/user/{id}/follow', 'UserController@follow');
    Route::get('v0/user/{id}/followers', 'UserController@followers');
    Route::get('v0/user/{id}/followings', 'UserController@followings');
    Route::resource('v0/user', 'UserController');
    /* USER END*/

    /* POST START*/
    Route::resource('v0/post', 'PostController');
    Route::get('v0/post/{id}/praise', 'PostController@praise');
    Route::delete('v0/post/reply/{id}', 'PostController@disableReply');
    /* POST END*/

    /* TRADE START*/
    Route::resource('v0/trade', 'TradeController');
    /* TRADE END*/

    /* MAKER START*/
    Route::get('v0/market/hot', 'MarketController@index');
    Route::get('v0/market/convenient', 'MarketController@convenient');
    Route::get('v0/market/maker', 'MarketController@maker');
    Route::get('v0/market/booth', 'MarketController@listBooth');
    Route::get('v0/market/booth/{id}', 'MarketController@getBooth');
    Route::post('v0/market/booth/{id}/follow', 'MarketController@postBoothFollow');
    Route::get('v0/market/booth/{id}/follow', 'MarketController@listBoothFollow');
    Route::get('v0/market/product', 'MarketController@listProduct');
    Route::get('v0/market/product/{id}', 'MarketController@getProduct');
    Route::post('v0/market/product/{id}/reply', 'MarketController@postProductReply');
    Route::post('v0/market/promotion/{id}/praise', 'MarketController@postPromoPraise');
    Route::get('v0/market/cart', 'MarketController@listCarts');
    Route::post('v0/market/cart', 'MarketController@postCart');
    Route::put('v0/market/cart/{id}', 'MarketController@putCart');
    Route::delete('v0/market/cart/{id}', 'MarketController@delCart');
    Route::post('v0/market/order', 'MarketController@postOrder');
    Route::post('v0/market/pay/wechat', 'PayController@wechatPayPreOrder');
    /* MAKER END*/

    /* ACTIVITIES START*/
    Route::get('v0/activity/{id}/follow', 'ActivitiesController@follow');
    Route::post('v0/activity/{id}/sign', 'ActivitiesController@sign');
    Route::get('v0/activity/{id}/signers', 'ActivitiesController@signers');
    Route::get('v0/activity/{id}/signer/{sid}/confirm', 'ActivitiesController@confirmSignedUser');
    Route::resource('v0/activity', 'ActivitiesController');
    /* ACTIVITIES END*/

    /* VERIFY START*/
    Route::get('v0/verify/code', 'VerificationController@getVCode');
    /* VERIFY END*/

    /* DATA DICTIONARY START*/
    Route::get('v0/dic/school', 'Api\DicController@getSchools');
    Route::get('v0/dic/city', 'Api\DicController@getCities');
    Route::get('v0/dic/bank', 'Api\DicController@getBanks');
    /* DATA DICTIONARY END*/

    /* LBS START*/
    Route::get('v0/lbs/nearby/user', 'LocationController@getNearbyUsers');
    Route::get('v0/lbs/nearby/store', 'LocationController@getNearbyStores');
    Route::get('v0/lbs/nearby/act', 'LocationController@getNearbyActivities');
    Route::post('v0/lbs/user/{id}', 'LocationController@updateUser');
    Route::post('v0/lbs/booth/{id}', 'LocationController@updateBooth');
    /* LBS END*/

    /* AD START*/
    Route::get('v0/ad/index/top', 'AdController@listIndexTop');
    /* AD END*/

});

/*********** BACK ***********/

/* SYS USER START*/
Route::get('office/sys/user', 'OfficeSysUserController@listUsers');
Route::post('office/sys/user', 'OfficeSysUserController@postUser');
Route::put('office/sys/user/{id}', 'OfficeSysUserController@putUser');
Route::delete('office/sys/user/{id}', 'OfficeSysUserController@delUser');
Route::put('office/sys/user/{id}/enable', 'OfficeSysUserController@enableUser');

Route::get('office/sys/user/{id}/menu', 'OfficeMenuController@listUserMenu');
Route::get('office/sys/user/{id}/role', 'OfficeSysUserController@listUserRole');
Route::post('office/sys/user/{id}/role', 'OfficeSysUserController@addUserRole');
Route::delete('office/sys/user/{id}/role', 'OfficeSysUserController@delUserRole');

Route::get('office/sys/role/{id}/menu', 'OfficeMenuController@listRoleMenu');
Route::post('office/sys/role/{id}/menu', 'OfficeMenuController@addRoleMenu');
Route::delete('office/sys/role/{id}/menu', 'OfficeMenuController@delRoleMenu');

Route::get('office/sys/role', 'OfficeMenuController@listRole');
Route::post('office/sys/role', 'OfficeMenuController@postRole');
Route::put('office/sys/role/{id}', 'OfficeMenuController@putRole');
Route::delete('office/sys/role/{id}', 'OfficeMenuController@delRole');
Route::get('office/sys/menu', 'OfficeMenuController@listMenu');
Route::post('office/sys/menu', 'OfficeMenuController@postMenu');
Route::put('office/sys/menu/{id}', 'OfficeMenuController@putMenu');
Route::delete('office/sys/menu/{id}', 'OfficeMenuController@delMenu');
/* SYS USER END*/

/* OFFICE START*/
Route::post('office/login', 'OfficeController@login');
Route::get('office/logout', 'OfficeController@logout');
Route::get('office/menu', 'OfficeController@getMenu');
/* OFFICE END*/

/* WEB USER START*/
Route::get('office/user/profile', 'OfficeWebUserController@listUserProfiles');
Route::get('office/user/profile/{id}', 'OfficeWebUserController@getUserProfile');
Route::put('office/user/profile/{id}/censor/detail', 'OfficeWebUserController@censorUserProfileDetail');
Route::put('office/user/profile/{id}/censor/contact', 'OfficeWebUserController@censorUserProfileContact');
Route::put('office/user/profile/{id}/censor/bank', 'OfficeWebUserController@censorUserProfileBank');
/* WEB USER END*/

/* BOOTH START*/
Route::get('office/booth', 'OfficeBoothController@listBooths');
Route::put('office/booth/{id}/censor', 'OfficeBoothController@censorBooth');
Route::get('office/booth/{id}/loans', 'OfficeBoothController@listLoans');
/* BOOTH END*/

/* FUND START*/
Route::get('office/fund/{id}/loan', 'OfficeFundController@listRepayments');
Route::get('office/fund/{id}/interview', 'OfficeFundController@interviewFund');
Route::get('office/fund/repaied', 'OfficeFundController@listRepaiedFund');
// Route::get('office/fund/');
/* FUND END*/

/* LOAN START*/
Route::get('office/loan/{id}/alloc', 'OfficeFundController@allocateRepayment');
Route::get('office/loan/{id}/retrive', 'OfficeFundController@retriveLoan');
/* LOAN END*/

/* DRAW START*/
Route::get('office/draw', 'OfficeDrawContoller@listDraw');
Route::get('office/draw/{id}', 'OfficeDrawContoller@getDraw');
Route::put('office/draw/{id}/confirm', 'OfficeDrawContoller@confirmDraw');
/* DRAW END*/

/*********** PROTAL **********/

/* WECHAT START*/
Route::get('wechat/hengda/user', 'WechatController@getHengdaUsers');
/* WECHAT END*/

/* PAYMENT START*/
Route::post('pay/alipay/watchdog', 'PayController@callbackAlipay');
Route::post('pay/wechat/watchdog', 'PayController@callbackWechat');
/* PAYMENT END*/
