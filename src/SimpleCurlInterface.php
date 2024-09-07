<?php

namespace pavelmister\SimpleCurl;

interface SimpleCurlInterface
{
    /**
     * @param $field string
     * @param $value string
     */
    public function SetParam(string $field, string $value);

    /**
     * @return array
     */
    public function GetParams();

    public function EnableRedirects();
    public function DisableRedirects();

    /**
     * @param $name
     * @param $value
     */
    public function SetHeader(string $name, string $value);

    /**
     * @param $name
     */
    public function RemoveHeader(string $name);

    /**
     * @return array
     */
    public function GetHeaders();

    /**
     * @param $contentType string
     */
    public function SetContentType(string $contentType);

    /**
     * @param $value string
     */
    public function SetProxy(string $value);

    /**
     * @return string
     */
    public function GetProxy();

    /**
     * @return int
     */
    public function GetTimeResponse();

    /**
     * @return int
     */
    public function GetTimePreTransfer();

    /**
     * @return int
     */
    public function GetTimeConnect();

    /**
     * @return int
     */
    public function GetTimeLookup();

    /**
     * @return array
     */
    public function GetResponseHeaders();

    /**
     * @param array $headers
     */
    public function SetResponseHeaders(array $headers);

    /**
     * @return string
     */
    public function GetClientIp();

    /**
     * @return string
     */
    public function GetServerIp();

    /*
     * @return string
     */
    public function GetBody();

    /*
     * @return empty
     */
    public function SetParamsPerRequest(bool $value);

    public function SetFileUpload(string $field, string $filePath);

    public function ClearParams();

    /**
     * @return int
     */
    public function GetResponseBodyLenght();

    public function DisableDefaultHeaders();

    public function UseMultypartForm();

    /**
     * @param string $method
     * @param string $url
     * @return mixed
     */
    public function request(string $method, string $url);
}