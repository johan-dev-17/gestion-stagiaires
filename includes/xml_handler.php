<?php
require_once __DIR__ . '/../config/config.php';

/**
 * Gestionnaire XML générique pour CRUD
 */
class XMLHandler {
    private $file;
    private $rootName;
    private $itemName;

    public function __construct($filename, $rootName, $itemName) {
        $this->file = DATA_PATH . $filename;
        $this->rootName = $rootName;
        $this->itemName = $itemName;
        $this->ensureFile();
    }

    private function ensureFile() {
        if (!file_exists($this->file)) {
            $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><' . $this->rootName . '/>');
            $this->save($xml);
        }
    }

    public function load() {
        return simplexml_load_file($this->file);
    }

    public function save($xml) {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml->asXML());
        $dom->save($this->file);
    }

    public function all() {
        $xml = $this->load();
        $items = [];
        foreach ($xml->{$this->itemName} as $item) {
            $items[] = $this->xmlToArray($item);
        }
        return $items;
    }

    public function find($id) {
        $xml = $this->load();
        foreach ($xml->{$this->itemName} as $item) {
            if ((string)$item->id === (string)$id) {
                return $this->xmlToArray($item);
            }
        }
        return null;
    }

    public function add($data) {
        $xml = $this->load();
        $newId = $this->nextId($xml);
        $item = $xml->addChild($this->itemName);
        $item->addChild('id', $newId);
        foreach ($data as $key => $value) {
            if ($key === 'id') continue;
            $item->addChild($key, htmlspecialchars((string)$value, ENT_XML1, 'UTF-8'));
        }
        $this->save($xml);
        return $newId;
    }

    public function update($id, $data) {
        $xml = $this->load();
        foreach ($xml->{$this->itemName} as $item) {
            if ((string)$item->id === (string)$id) {
                foreach ($data as $key => $value) {
                    if ($key === 'id') continue;
                    if (isset($item->{$key})) {
                        $item->{$key} = htmlspecialchars((string)$value, ENT_XML1, 'UTF-8');
                    } else {
                        $item->addChild($key, htmlspecialchars((string)$value, ENT_XML1, 'UTF-8'));
                    }
                }
                $this->save($xml);
                return true;
            }
        }
        return false;
    }

    public function delete($id) {
        $xml = $this->load();
        $dom = dom_import_simplexml($xml);
        foreach ($xml->{$this->itemName} as $item) {
            if ((string)$item->id === (string)$id) {
                $itemDom = dom_import_simplexml($item);
                $itemDom->parentNode->removeChild($itemDom);
                $this->save(simplexml_import_dom($dom));
                return true;
            }
        }
        return false;
    }

    private function nextId($xml) {
        $max = 0;
        foreach ($xml->{$this->itemName} as $item) {
            $id = (int)$item->id;
            if ($id > $max) $max = $id;
        }
        return $max + 1;
    }

    private function xmlToArray($xml) {
        $result = [];
        foreach ($xml->children() as $child) {
            $result[$child->getName()] = (string)$child;
        }
        return $result;
    }

    public function getFilePath() {
        return $this->file;
    }
}
