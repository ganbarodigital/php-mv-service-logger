<?php

/**
 * Copyright (c) 2016-present Ganbaro Digital Ltd
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the names of the copyright holders nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  Libraries
 * @package   ServiceLogger
 * @author    Stuart Herbert <stuherbert@ganbarodigital.com>
 * @copyright 2016-present Ganbaro Digital Ltd www.ganbarodigital.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://code.ganbarodigital.com/php-mv-service-logger
 */

namespace GanbaroDigital\ServiceLogger\V1;

use Error;
use Exception;
use Monolog\Logger;
use GanbaroDigital\ExceptionHelpers\V1\BaseExceptions\ParameterisedException;
use GanbaroDigital\HttpStatus\Interfaces\HttpException;

/**
 * a convenience wrapper around Monolog's logger
 */
class ServiceLogger extends Logger
{
   /**
     * @param Exception $exception
     *        log the exception that has been thrown
     * @param string $logLevel
     *        which log level are we logging this to?
     */
    public function logException(Exception $exception, $logLevel = Logger::ERROR)
    {
        // is this one of our enhanced exceptions?
        //
        // these exceptions contain a wealth of collected data, and are
        // designed to make it a buck-tonne easier to diagnose runtime
        // errors
        if ($exception instanceof ParameterisedException) {
            $context = [
                'exceptionData' => $exception->getMessageData()
            ];

            if ($exception instanceof HttpException) {
                $context['httpStatus'] = $exception->getHttpStatus()->getStatusCode();
            }
        }
        else {
            // could be anything
            // log the whole thing ... this might get messy!
            $context = [ 'exception' => $exception ];
        }

        // call our underlying logger
        $this->addRecord($logLevel, get_class($exception) . ': ' . $exception->getMessage(), $context);
    }

    /**
      * @param Error $error
      *        log the PHP 7+ error that has been thrown
      * @param string $logLevel
      *        which log level are we logging this to?
      */
     public function logError(Error $error, $logLevel = Logger::ERROR)
     {
         $context = [ 'error' => $error ];

         // call our underlying logger
         $this->addRecord($logLevel, get_class($error) . ': ' . $error->getMessage(), $context);
     }
}
