<?php

namespace Backtheweb\Linguo\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;

class IndexController extends Controller {

    /**
     * Display developer status based on the number of Github Repos
     *
     * @return Response
     */
    public function index()
    {
        return view('linguo::index', [

        ]);
    }
}