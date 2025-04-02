<?php

namespace SavvyWebFulfilment\Admin;

class SavvyPluginConfig
{

    private array $fulfilmentProviderOptions;
    private string $pluginName;
    private string $bandName;
    private string $brandLogo;
    private string $savvyBaseApiUrl;

    public function __construct()
    {
        $this->init();
    }

    private function init(): void
    {
        $this->setFulfilmentProviderOptions();
        $this->setBrandName();
        $this->setPluginName();
        $this->setBrandLogo();
        $this->setSavvyApiUrl();
    }

    private function setFulfilmentProviderOptions(): void
    {
        $this->fulfilmentProviderOptions = [
            'manual' => 'Manual',
            'savvyweb' => 'SavvyWeb Fulfilment',
        ];
    }

    private function setBrandName(): void
    {
        $this->bandName = 'SavvyWeb';
    }

    private function setPluginName(): void
    {
        $this->pluginName = $this->bandName . ' Fulfilment';
    }

    private function setBrandLogo(): void
    {
        $this->brandLogo = 'https://savvywebsystem.s3.us-east-2.amazonaws.com/savvy-web-files/SavvyWeb_Logo_WebDevelopment_200x78.png';
    }

    private function setSavvyApiUrl(): void
    {
        $this->savvyBaseApiUrl = 'http://savvyweb.local/api/v1/';
    }


    
    public function getSavvyFulfilmentProviderOptions(): array
    {
        return $this->fulfilmentProviderOptions;
    }

    public function getSavvyBrandName(): string
    {
        return $this->bandName;
    }

    public function getSavvyBrandLogo(): string
    {
        return $this->brandLogo;
    }

    public function getSavvyPluginName(): string
    {
        return $this->pluginName;
    }

    public function getSavvyApiUrl(): string
    {
        return $this->savvyBaseApiUrl;
    }

}