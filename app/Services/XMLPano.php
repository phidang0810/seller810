<?php

namespace App\Services;

class XMLPano
{
    protected $xml, $path;
    public function addHotSpotToScene($path, $input)
    {
        $xml = simplexml_load_file($path);
        $node = $xml->xpath('scene');
        if (!isset($node[0])) {
            return false;
        }

        $icon = '';

        if ($input['social'] === 'facebook') {
            $icon = "[i style='font-size:1.4em;color:#2e4485' class='fab fa-facebook'][/i]";
        }

        if ($input['social'] === 'twitter') {
            $icon = "[i style='font-size:1.4em;color:#2e4485' class='fab fa-twitter-square'][/i]";
        }

        if ($input['social'] === 'instagram') {
            $icon = "[i style='font-size:1.4em;color:#e0218a' class='fab fa-instagram'][/i]";
        }

        if ($input['social'] === 'linkedin') {
            $icon = "[i style='font-size:1.4em;color:#2e4485' class='fab fa-linkedin'][/i]";
        }

        $HSName = 'hs' . $input['id'];
        $hotspot = $node[0]->addChild("hotspot", "");
        $hotspot->addAttribute("name", $HSName);
        $hotspot->addAttribute("mtype", "icon");
        $hotspot->addAttribute("url", $input['icon']);
        $hotspot->addAttribute("scale", "0.5");
        $hotspot->addAttribute("dataUrl", $input['link']);
        $hotspot->addAttribute("dataAvatar", $input['avatar']);
        $hotspot->addAttribute("dataName", $input['name']);
        $hotspot->addAttribute("dataIcon", $icon);
        $hotspot->addAttribute("style", "dragableHotspot");
        $hotspot->addAttribute("onclick", "focusHS()");
        $hotspot->addAttribute("onover", "overParentTag()");
        $hotspot->addAttribute("onout", "outParentTag()");
        $hotspot->addAttribute("ath", $input['position']['x']);
        $hotspot->addAttribute("atv", $input['position']['y']);
        $hotspot->addAttribute("keep", "false");
        $hotspot->addAttribute("scene", $input["scene_id"]);
        $savePath = storage_path('app/' . time() . '.xml');
        $xml->asXML($savePath);

        return $savePath;
    }

    public function flush($scene)
    {
        $path = $this->getScenePath($scene);
        $xml = simplexml_load_file($path);
        foreach ($xml->xpath('hotspot') as $child) {
            unset($child[0]);
        }

        return $xml->asXML($path);
    }

    public function loadXML($scene)
    {
        $path = $this->getScenePath($scene);
        $xml = simplexml_load_file($path);
        return $xml;
    }

    public function getHotSpotByScene($scene)
    {
        $XML = $this->loadXML($scene);
        $XMLData = json_encode($XML);
        $data = json_decode($XMLData, TRUE);

        return $data['hotspot'];
    }

    public function getScenePath($scene)
    {
        return public_path() . '/uploads/webgl/' . $scene . '/hotspots.xml';
    }

    public function addScene($path, $attributes = [])
    {
        $xml = simplexml_load_file($path);

        $node = $xml->addChild('include', '');
        $node->addAttribute('scene', true);
        foreach ($attributes as $key => $value) {
            $node->addAttribute($key, $value);
        }

        $savePath = storage_path('app/' . microtime() . '.xml');
        $xml->asXML($savePath);

        return $savePath;
    }

    public function setMainScene($path)
    {
        return $this->updateSceneAttribute($path, ['main' => true]);
    }

    public function updateSceneAttribute($path, $attributes = [])
    {
        $xml = simplexml_load_file($path);
        $node = $xml->xpath('scene');
        if (isset($node[0])) {
            foreach ($attributes as $key => $value) {
                if (isset($node[0]->attributes()[$key])) {
                    $node[0]->attributes()->{$key} = $value;
                } else {
                    $node[0]->addAttribute($key, $value);
                }
            }
        }
        $savePath = storage_path('app/' . time() . '.xml');
        $xml->asXML($savePath);

        return $savePath;
    }

    public function createOrUpdateHS(array $hotSpots = [])
    {
        if (is_null($this->xml)) {
            $this->xml = simplexml_load_file($this->path);
        }

        $scene = $this->xml->xpath('scene');
        foreach ($hotSpots as $hotSpot) {
            unset($this->xml->xpath('scene/hotspot[@name="' . $hotSpot['name'] . '"]')[0][0]);

            $node = $scene[0]->addChild('hotspot', '');
            foreach ($hotSpot as $attr => $value) {
                if ($attr != 'points') {
                    $node->addAttribute($attr, $value);
                }
            }

            if (key_exists('points', $hotSpot)) {
                foreach ($hotSpot['points'] as $point) {
                    $pointNode = $node->addChild('point', '');
                    foreach ($point as $key => $val) {
                        $pointNode->addAttribute($key, $val);
                    }
                }
            }
        }
        return $this;
    }

    public function deleteHS(array $hotSpots = [])
    {
        if (is_null($this->xml)) {
            $this->xml = simplexml_load_file($this->path);
        }
        foreach ($hotSpots as $hotSpot) {
            unset($this->xml->xpath('scene/hotspot[@name="' . $hotSpot['name'] . '"]')[0][0]);
        }
        return $this;
    }

    public function updateView(array $attr = [])
    {
        if (is_null($this->xml)) {
            $this->xml = simplexml_load_file($this->path);
        }

        $view = $this->xml->xpath('scene/view');
        foreach ($attr as $key => $value) {
            if (!in_array($key, ['hlookat', 'vlookat', 'fov', 'fovmin', 'fovmax'])) continue;

            if (isset($view[0]->attributes()[$key])) {
                $view[0]->attributes()->{$key} = $value;
            } else {
                $view[0]->addAttribute($key, $value);
            }

        }
        return $this;
    }

    public function save($path = null)
    {
        if (!$path) {
            $path = storage_path('app/' . time() . '.xml');
        }

        $this->xml->asXML($path);
        return $path;
    }

    public function setPath($url)
    {
        $this->path = $url;
    }

    public function getPath()
    {
        return $this->path;
    }
}
