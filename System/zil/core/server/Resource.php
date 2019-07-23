<?php
/**
 *  Author: Akindutire, Ayomide Samuel
 *  Created: 10-July-2019
 */

namespace zil\core\server;

use zil\core\tracer\ErrorTracer;

class Resource{

    public $as_view = false;
    public $guards  = [];
    public $denials = [];
    public $allowed  = [];
    public $data     =  [];
    public $trial   = 0;
    public $context = '';
    public $guardTouched = false;

    /**
     * @param string $resource
     */
    public function __construct( string $context ) {
        $this->context = trim($context);
    }


    /**
     * Guard Route Against Unauthorized user via a guard class
     *
     * @param null|string $guardClass
     * @return Resource
     */
    public function guard(?string ...$guardClass) : Resource {

        try {

            $this->guardTouched = true;

            foreach ($guardClass as $Guard){
                array_push($this->guards, $Guard);
            }

            return $this;

        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }

    }

    /**
     * Deny Request for only specific IPs or all IPs
     *
     * @param string $deniedIp
     * @return resource
     */
    public function deny(string ...$deniedIp ) : Resource {

        try {

            foreach ($deniedIp as $IP){
                array_push($this->denials, $IP);
            }

            return $this;

        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }

    /**
     * Accept Request Only from specific IPs or all IPs
     *
     * @param string $allowedIp
     * @return Resource
     */
    public function allow( string ...$allowedIp ) : Resource {

        try {

            foreach ($allowedIp as $IP){
                array_push($this->allowed, $IP);
            }

            return $this;

        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }

    /**
     * @return Resource
     */
    public function asView() : Resource {
        try {

            $this->as_view = true;
            return $this;

        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }

    /**
     * @param int $period
     * @return Resource
     */
    public function trial(int $period ) : Resource
    {
        try {

            $this->trial = $period;
            return $this;

        } catch (\Throwable $t) {
            new ErrorTracer($t);

        }
    }

    /**
     * @param array ...$data
     * @return Resource
     */
    public function data(array ...$data) : Resource
    {
        try{

            foreach ( $data as $datum ){
                array_push($this->data, $datum);
            }

            return $this;

        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }
}