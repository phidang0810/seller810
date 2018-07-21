<?php
/**
 * Created by PhpStorm.
 * User: phidangmtv
 * Date: 4/20/18
 * Time: 11:06 AM
 */

namespace App\Jobs;

use App\Libraries\Clean;
use App\Libraries\Upload;
use App\Repositories\NotificationRepository;
use App\Repositories\PanoramaRepository;
use App\Repositories\SceneLogRepository;
use App\Repositories\SceneRepository;
use App\Repositories\ServerRepository;
use App\Services\XMLPano;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class MakePano implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $scene, $log, $panorama, $xml, $server;

    public function __construct($scene)
    {
        $this->scene = $scene;
        $this->log = new SceneLogRepository();
        $this->panorama = new PanoramaRepository();
        $this->xml = new XMLPano();
        $this->server = new ServerRepository();
    }

    public function handle(SceneRepository $sceneRepo, NotificationRepository $notify)
    {
        $sceneRepo->updateStatusById($this->scene->id, SCENE_IN_PROGRESS);
        $url = $this->scene->img;

        $contents = file_get_contents($url);
        $name = substr($url, strrpos($url, '/') + 1);
        $directory = 'scenes/' . $this->scene->id;
        $imgPath = $directory . '/' . $name;

        \Illuminate\Support\Facades\Storage::put($imgPath, $contents);
        $directory = storage_path('app/' . $directory);
        $imgPath = $directory . '/' . $name;

        $imgTo = $directory . '/images/[c/]l%Al/%Av/l%Al[_c]_%Av_%Ah.jpg';
        $xmlTo = $directory . '/pano.xml';
        $previewTo = $directory . '/images/preview.jpg';
        $toolPath = base_path() . '/krtool/krpanotools';
        $cmd = $toolPath . " makepano -config=templates/vtour-multires.config -tilepath=$imgTo -previewpath=$previewTo -xmlpath=$xmlTo $imgPath";
        exec($cmd . " 2>&1", $output, $return);

        if ($return === 1 || !is_file($directory . '/pano.xml')) {
            $sceneRepo->updateStatusById($this->scene->id, SCENE_FAILED);
            $this->log->createOrUpdate([
                'scene_id' => $this->scene->id,
                'action' => 'Error - Make scene ' . $this->scene->id,
                'data' => json_encode($output)
            ]);

            Clean::pano($directory);
            return;
        }

        $this->log->createOrUpdate([
            'scene_id' => $this->scene->id,
            'action' => 'Make scene ' . $this->scene->id,
            'data' => json_encode($output)
        ]);
        if (is_file($directory . '/pano.xml'))
        $directoryTo = $sceneRepo->getDirectoryByID($this->scene->id);

        $data = Upload::movePanoToS3($directory, $directoryTo);

        $dataUpdate = [
            'data' => json_encode([
                'xml' => $data['xml'],
            ]),
            'thumb' => $data['thumb'],
            'status' => SCENE_READY
        ];
        $sceneRepo->update($this->scene->id, $dataUpdate);

        //update thumb url to scene xml
        $sceneXML = $this->xml->updateSceneAttribute($data['xml'], [
            'thumburl' => $data['thumb'] . '?nocache=' . time(),
            'title' => $this->scene->name,
            'id' => $this->scene->id
        ]);
        $sceneXMLInS3 = substr($data['xml'], strpos($data['xml'], $directoryTo)) ;
        Upload::toS3($sceneXML, $sceneXMLInS3, true);

        //include scene to panorama xml
        $panorama = $this->panorama->getPanoramaById($this->scene->panorama_id);
        $newXML = $this->xml->addScene($panorama->xml_path, [
            'url' => $data['xml'] . '?nocache=' . time()
        ]);

        //include scene to panorama builder xml
        $newBuilderXML = $this->xml->addScene($panorama->xml_builder_path, [
            'url' => $data['xml'] . '?nocache=' . time()
        ]);

        $directoryPano = $this->panorama->getDirectoryByID($panorama->id);
        $panoXMLInS3 = substr($panorama->xml_path, strpos($panorama->xml_path, $directoryPano)) ;
        $builderXMLInS3 = substr($panorama->xml_builder_path, strpos($panorama->xml_builder_path, $directoryPano)) ;
        Upload::toS3($newXML, $panoXMLInS3, true);
        Upload::toS3($newBuilderXML, $builderXMLInS3, true);

        File::deleteDirectory($directoryPano);
        File::deleteDirectory($directory);

        $notify->create([
            'message' => 'The scene ' . $this->scene . ' is ready to use!',
            'user_ids' => [$this->scene->created_by],
            'type' => NOTIFICATION_PRIVATE,
            'status' => NOTIFICATION_NEW
        ]);
    }
}