<?php
/**
 * Created by PhpStorm.
 * User: Денис
 * Date: 10.02.2019
 * Time: 16:49
 */

namespace App\Http\Controllers;


class TestController {
    public function index(){
        $books = ['book1', 'book2'];

        return view('test', compact('books'));
    }
}