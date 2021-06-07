<?php

/*
 The MIT License (MIT)

Copyright (c) 2014 Christian Klisch

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/

namespace Slim\Middleware;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Server\RequestHandlerInterface as RequestHandler;

/**
 * Minify-Middleware is a summary of stackoverflow answers to reduce html traffic
 * by removing whitespaces, tabs, empty lines and comments.
 * */
class Minify
{
    private $shouldMinify;

    public function __construct($shouldMinify = true)
    {
        $this->shouldMinify = !empty($shouldMinify);
    }

    /**
     * minify html content
     *
     * @param string $html
     * @return string
     */
    private function minifyHTML($html)
    {
        $search = ['/(?:(?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:(?<!\:|\\\|\'|\")\/\/.*))/', '/\n/', '/\>[^\S ]+/s', '/[^\S ]+\</s', '/(\s)+/s', '/<!--.*?-->/'];
        $replace = [' ', ' ', '>', '<', '\\1', ''];

        return preg_replace($search, $replace, $html);
    }

    /**
     * @param Request $request
     * @param RequestHandler $handler
     * @return Response
     */
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $response = $handler->handle($request);

        if (!$this->shouldMinify) {
            return $response;
        }

        $oldBody = $response->getBody();

        $minifiedBodyContent = $this->minifyHTML((string)$oldBody);


        $newBody = new \Slim\Psr7\Stream(fopen('php://temp', 'r+'));

        //write the minified html content to the new \Slim\Http\Body instance
        $newBody->write($minifiedBodyContent);

        return $response->withBody($newBody);
    }
}