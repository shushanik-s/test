<?php

class UriValidator
{
    public function matches($route, Request $request)
    {
        $path = $request->path() == '/' ? '/' : '/'.$request->path();


    }
}