<?php
// Slovak extension, https://github.com/datenstrom/yellow-extensions/tree/master/languages/slovak
// Copyright (c) 2013-2019 Datenstrom, https://datenstrom.se
// This file may be used and distributed under the terms of the public license.

class YellowSlovak {
    const VERSION = "0.8.4";
    const TYPE = "language";
    public $yellow;         //access to API
    
    // Handle initialisation
    public function onLoad($yellow) {
        $this->yellow = $yellow;
    }
    
    // Handle update
    public function onUpdate($action) {
        $fileName = $this->yellow->system->get("settingDir").$this->yellow->system->get("systemFile");
        if ($action=="install") {
            $this->yellow->system->save($fileName, array("language" => "sk"));
        } elseif ($action=="uninstall" && $this->yellow->system->get("language")=="sk") {
            $language = reset(array_diff($this->yellow->text->getLanguages(), array("sk")));
            $this->yellow->system->save($fileName, array("language" => $language));
        }
    }}