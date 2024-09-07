<?php
/**
 * @author Pavel Mister <pavel.mister@gmail.com>
 */

namespace pavelmister;

use pavelmister\FunctionalCurlInterface;
use Zend\Dom\Query;

class SimpleCurl implements SimpleCurlInterface
{
    /**
     * @var $latestHtml string
     */
    protected $latestHtml = '';

    protected $cookies = [];

    /**
     * @var array
     */
    protected $params = [];

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * @var string
     */
    protected $contentType = '';

    /**
     * @var string
     */
    protected $proxy = '';

    /**
     * @var bool
     */
    protected $autoRedirects = false;

    /**
     * @var array
     */
    protected $responseHeaders = [];

    /**
     * @var string
     */
    protected $error;

    /**
     * @var string
     */
    protected $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.93 Safari/537.36';

    /**
     * @var string
     */
    protected $defaultAccept = 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9';

    /**
     * @var int $readTimeout
     */
    protected $readTimeout = 30;

    /**
     * @var $connectTime int
     */
    protected $connectTime = 0;

    /**
     * @var $connectTime int
     */
    protected $httpCode = 0;


    /**
     * @var $transferTime int
     */
    protected $pretransferTime = 0;

    /**
     * @var $namelookupTime fload
     */
    protected $namelookupTime = 0.0;

    /**
     * @var $responseTime int
     */
    protected $responseTime = 0;

    /**
     * @var $responseDate string
     */
    protected $responseDate = "";

    /**
     * @var string
     */
    protected $clientIp = '';

    /**
     * @var string
     */
    protected $serverIp = '';

    /**
     * @var int
     */
    protected $responseBodyLenght = 0;

    /**
     * @var bool
     */
    protected $enableDefaultHeaders = true;

    /**
     * @var bool
     */
    protected $isMultipartForm = false;

    protected $rawBody = '';

    /**
     * @var bool
     */
    protected $paramsPerRequest = false;

    public function SetParamsPerRequest(bool $value)
    {
        $this->paramsPerRequest = $value;
    }

    public function ClearParams()
    {
        $this->params = [];
    }

    public function GetCurlError()
    {
        return $this->error;
    }

    /**
     * @param $field
     * @param $value
     */
    public function SetParam(string $field, string $value)
    {
        $this->params = array_merge($this->params, [$field => $value]);
    }

    public function EnableRedirects()
    {
        $autoRedirect = true;
    }

    public function DisableRedirects()
    {
        $autoRedirect = false;
    }

    /**
     * @return bool
     */
    protected function AllowRedirects()
    {
        return $this->autoRedirects;
    }

    /**
     * @param $timeout int
     */
    public function SetReadTimeout(int $timeout)
    {
        $this->readTimeout = $timeout;
    }

    /**
     * @return array
     */
    public function GetParams()
    {
        return $this->params;
    }

    /**
     * @param $header string
     * @param $value string
     * @throws \Exception
     */
    public function SetHeader(string $header, string $value)
    {
        if (array_key_exists($header, $this->headers)) {
            throw new \Exception('SetHeader::$name - duplicate');
        }
        $this->headers = array_merge($this->headers, [$header => $value]);
    }

    /**
     * @param $name string
     */
    public function RemoveHeader(string $name)
    {
        if (in_array($name, $this->headers)) {
            unset($this->headers[$name]);
        }
    }

    public function UseMultypartForm()
    {
        return $this->isMultipartForm = true;
    }

    public function HasCurlError()
    {
        if (!empty($this->error))
            return true;

        return false;
    }

    /**
     * @return array
     */
    public function GetHeaders()
    {
        $headers = [];

        if ($this->enableDefaultHeaders)
        {
            if (!array_key_exists('User-Agent', $this->headers)) {
                $headers['User-Agent'] = $this->userAgent;
            }

            if (!array_key_exists('Accept', $this->headers)) {
                $headers['Accept'] = $this->defaultAccept;
            }

            if (!array_key_exists('Accept-Encoding', $this->headers)) {
                //$headers['Accept-Encoding'] = 'gzip, deflate, br';
            }

            if (!array_key_exists('Accept-Language', $this->headers)) {
                $headers['Accept-Language'] = 'en-US,en;q=0.9,ru;q=0.8,cy;q=0.7';
            }

            if (!array_key_exists('sec-ch-ua', $this->headers)) {
                $headers['sec-ch-ua'] = '" Not A;Brand";v="99", "Chromium";v="96", "Google Chrome";v="96"';
            }

            if (!array_key_exists('sec-ch-ua-mobile', $this->headers)) {
                $headers['sec-ch-ua-mobile'] = '?0';
            }

            if (!array_key_exists('sec-ch-ua-platform', $this->headers)) {
                $headers['sec-ch-ua-platform'] = '"Windows"';
            }

            if (!array_key_exists('Content-Type', $this->headers) && $this->isMultipartForm)
            {
                $headers['Content-Type'] = 'multipart/form-data';
            }
        }

        $headers = array_merge($headers, $this->headers);

        $headersCurl = [];


        foreach ($headers as $header => $value)
        {
            $headersCurl[] = $header . ': ' . $value;
        }

        return $headersCurl;
    }

    /**
     * @param $contentType string
     */
    public function SetContentType(string $contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * @param $proxy string
     */
    public function SetProxy(string $proxy)
    {
        $this->proxy = $proxy;
    }

    /**
     * @return string
     */
    public function GetProxy()
    {
        return $this->proxy;
    }

    /**
     * @return int
     */
    public function GetTimeResponse()
    {
        return $this->responseTime;
    }

    /**
     * @return int
     */
    public function GetTimePreTransfer()
    {
        return $this->pretransferTime;
    }

    /**
     * @return int
     */
    public function GetTimeConnect()
    {
        return $this->connectTime;
    }

    /**
     * @return fload
     */
    public function GetTimeLookup()
    {
        return $this->namelookupTime;
    }

    /**
     * @return int
     */
    protected function GetReadTimeout()
    {
        return $this->readTimeout;
    }

    /**
     * @return string
     */
    public function GetResponseHeaderDate()
    {
        return $this->responseDate;
    }

    /**
     * @return string
     */
    public function GetBody()
    {
        return $this->latestHtml;
    }

    /**
     * @param $headers array
     * @return array
     */
    public function SetResponseHeaders($headers)
    {
        $headersOut = [];
        if (empty(trim($headers)))
        {
            return;
        }
        $headers = explode("\n", $headers);
        $headers = array_filter($headers, function($var) {
            return !empty(trim($var));
        });

        foreach ($headers as $header) {
            if (strpos($header, ':') == false)
                continue;
            $header = explode(': ', $header, 2);

            $setCookie = strripos($header[0], 'set-cookie');
            if ($setCookie == 0 && $setCookie !== false) {
                $header[0] = strtolower($header[0]);
            }

            $headersOut[$header[0]] = $header[1];
        }

        $this->responseHeaders = $headersOut;
        return;
    }

    /**
     * @return array
     */
    public function GetResponseHeaders()
    {
        return $this->responseHeaders;
    }

    /**
     * @return string
     */
    public function GetClientIp()
    {
        return $this->clientIp;
    }

    /**
     * @return string
     */
    public function GetServerIp()
    {
        return $this->serverIp;
    }

    /**
     * @return int
     */
    public function GetResponseBodyLenght()
    {
        return $this->responseBodyLenght;
    }

    /**
     * @return int
     */
    public function GetStatusCode()
    {
        return $this->httpCode;
    }

    public function DisableDefaultHeaders()
    {
        $this->enableDefaultHeaders = false;
    }

    public function SetRawBody($body)
    {
        $this->rawBody = $body;
    }

    protected function PrepareCookies($setCookie)
    {
    }

    /**
     * @param $xpath
     * @param null $html
     * @return \Zend\Dom\NodeList
     */
    public function FindByXPath($xpath, $html = null)
    {
        if (empty($html))
            $html = $this->latestHtml;

        $dom = new Query($html);
        $dom->setDocumentHtml(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        $results = $dom->execute($xpath);
        return $results;
    }

    protected $filesUpload = [];

    public function SetFileUpload(string $field, string $filePath)
    {
        $this->filesUpload = array_merge($this->filesUpload, [$field => $filePath]);
    }

    /**
     * @param $method
     * @param $url
     */
    public function request($method, $url)
    {
        $method = strtoupper($method);

        $headers = $this->GetHeaders();
        $curlHanlder = curl_init();
        $requestOptions = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_FOLLOWLOCATION => $this->AllowRedirects(),
            CURLOPT_HEADER    => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_CONNECTTIMEOUT => $this->GetReadTimeout()
        ];


        if ($this->isMultipartForm)
        {
            $requestOptions[CURLOPT_HEADER] = ['Content-Type:multipart/form-data'];
        }

        if (array_key_exists('User-Agent', $headers)) {
            $requestOptions[CURLOPT_USERAGENT] = $headers['User-Agent'];
        }

        if ( ! empty($this->GetProxy())) {
            $requestOptions[CURLOPT_PROXY] = $this->GetProxy();
        }

        switch ($method)
        {
            case "GET":{
                $requestOptions[CURLOPT_CUSTOMREQUEST] = 'GET';
                }
                break;
            case "POST": {
               // var_dump($requestOptions);
                    $requestOptions[CURLOPT_CUSTOMREQUEST] = 'POST';
                    $rawBody = $this->rawBody;

                  //  $requestOptions[CURLOPT_POST]
                    if (! empty($rawBody))
                    {
                        $requestOptions[CURLOPT_POSTFIELDS] = $rawBody;
                    } else if (count($this->GetParams()) > 0) {
                        $requestOptions[CURLOPT_POSTFIELDS] = $this->GetParams();
                    }

                    if (count($this->filesUpload) > 0)
                    {
                        foreach ($this->filesUpload as $field => $path)
                        {
                            $requestOptions[CURLOPT_POSTFIELDS][$field] = new \CURLFile($path, mime_content_type($path), $field);
                        }
                    }
                }
                break;
            case "PUT": {
                    $requestOptions[CURLOPT_CUSTOMREQUEST] = 'PUT';
                    if (! empty($rawBody))
                    {
                        $requestOptions[CURLOPT_POSTFIELDS] = $rawBody;
                    }
                }
                break;
            default:
                $requestOptions[CURLOPT_CUSTOMREQUEST] = $method;
                break;
        }



        curl_setopt_array($curlHanlder, $requestOptions);
        $response = curl_exec($curlHanlder);

        $info = curl_getinfo($curlHanlder);

        $header_size = curl_getinfo($curlHanlder, CURLINFO_HEADER_SIZE);
        $headers = substr($response, 0, $header_size);

        $this->SetResponseHeaders($headers);

        if (array_key_exists('set-cookie', $this->responseHeaders)) {
            //var_dump();
           // $this->cookies = array_push($this->responseHeaders['set-cookie']);
        }

        $response = substr($response, $header_size);

        if (array_key_exists('http_code', $info))
            $this->httpCode = $info['http_code'];

        if (array_key_exists('connect_time', $info))
            $this->connectTime = $info['connect_time'];

        if (array_key_exists('pretransfer_time', $info))
            $this->pretransferTime = $info['pretransfer_time'];

        if (array_key_exists('namelookup_time', $info))
            $this->namelookupTime = $info['namelookup_time'];

        if (array_key_exists('total_time', $info))
            $this->responseTime = $info['total_time'];

        if (array_key_exists('local_ip', $info))
            $this->serverIp = $info['local_ip'];

        if (array_key_exists('primary_ip', $info))
            $this->serverIp = $info['primary_ip'];

        $curlError = curl_error($curlHanlder);
        if (!empty($curlError)) {
            $this->error = $curlError;
        }

        $this->latestHtml = $response;

        if ($this->paramsPerRequest) {
            $this->ClearParams();
        }
        curl_close($curlHanlder);

        return $response;
    }
}