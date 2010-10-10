<?php

class TestHelper
{
    public static function setAPIResponse($base, $target, $amount, $result, $updated_at, $options = array())
    {
        if (!isset($options['fail_with'])) {
            $response = '{"result":{"value":'.$result.',"target":"'.$target.'"'.
                        ',"base":"'.$base.'","updated_at":"'.$updated_at.'"},"status":"ok"}';
        } else {
            $response = '{"code":"'.$options['code'].'","message":"'.$options['fail_with'].'", "status":"fail"}';
        }

        runkit_function_redefine('file_get_contents', '', "return '$response';");

        return $response;
    }
}