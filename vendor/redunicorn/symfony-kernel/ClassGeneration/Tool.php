<?php
/**
 * PhpStorm.
 * User: Jay
 * Date: 2018/11/6
 */

namespace RedUnicorn\SymfonyKernel\ClassGeneration;


class Tool
{
    public function getCodeToTemplate($code_template, array $params)
    {
        $code = (string)file_get_contents($code_template);

        $matches = array();

        preg_match_all( '/{{[^{{}}]*}}/', $code, $matches);

        foreach ($matches[0] as $key => $match) {
            $match_key = str_replace('{{', '', $match);
            $match_key = str_replace('}}', '', $match_key);
            $match_key = trim($match_key);

            if(array_key_exists($match_key, $params)){
                $code = str_replace($match, $params[$match_key], $code);
            }else{
                throw new UnicornException('params 找不到 ' . $match_key);
            }
        }

        return $code;
    }
}