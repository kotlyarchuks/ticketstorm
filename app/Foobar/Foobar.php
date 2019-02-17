<?php
/**
 * Created by PhpStorm.
 * User: Денис
 * Date: 10.02.2019
 * Time: 16:20
 */

namespace Foobar;


use Illuminate\Session\Store;

class Foobar {

    /**
     * @var Store
     */
    private $session;

    /**
     * Foobar constructor.
     */
    public function __construct(Store $session)
    {
        echo "Denis";
        $this->session = $session;
    }
}