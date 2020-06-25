<?php

/**
 * NexoPOS Controller
 * @since  1.0
**/

namespace App\Http\Controllers\Dashboard;
use Illuminate\Support\Facades\View;

// use Tendoo\Core\Services\Page;

class ExpensesCategoriesController extends Controller
{
    /**
     * Index Controller Page
     * @return  view
     * @since  1.0
    **/
    public function index()
    {
        Page::setTitle( __( 'Unammed Page' ) );
        return View::make( 'NexoPOS::index' );
    }
}

