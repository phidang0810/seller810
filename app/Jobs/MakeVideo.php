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
use App\Models\Video;
use App\Repositories\NotificationRepository;
use App\Repositories\VideoLogRepository;
use App\Repositories\VideoRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class MakeVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $video, $log;

    public function __construct($video)
    {
        $this->video = $video;
        $this->log = new VideoLogRepository();
    }

    public function handle(VideoRepository $videoRepo, NotificationRepository $notify)
    {
        $videoRepo->updateStatusById($this->video->id, VIDEO_IN_PROGRESS);

        $url = $this->video->origin;

        $contents = file_get_contents($url);
        $name = substr($url, strrpos($url, '/') + 1);
        $directory = 'videos/' . $this->video->id;
        $video = $directory  . '/' . $name;
        \Illuminate\Support\Facades\Storage::put($video, $contents);

        $pathInfo = pathinfo($video);
        $fileName = $pathInfo['filename'];
        $directory = storage_path('app/' . $directory);
        $video = $directory  . '/' . $name;
        $previewPhoto = $directory . '/preview.jpg';
        $appPath = base_path() . '/ffmpeg/ffmpeg';
        $tabletResolution = Video::$resolution['desktop'];
        $tabletPath = $directory . '/desktop/' . $fileName . '-' . $tabletResolution;
        if ( !is_dir($directory . '/desktop')) {
            File::makeDirectory($directory . '/desktop');
        }
        $cmd = "$appPath -y -i $video -vf scale=" .'"' .$tabletResolution.':-1"'." -ss 00:00:01 -vframes 1 $previewPhoto $tabletPath.mp4 $tabletPath.webm";
        exec($cmd . " 2>&1", $output, $return);
        if ($return === 1 || !is_file($previewPhoto)) {
            $videoRepo->updateStatusById($this->video->id, VIDEO_FAILED);
            $this->log->createOrUpdate([
                'video_id' => $this->video->id,
                'action' => 'Error - Make video ' . $this->video->id,
                'data' => json_encode($output)
            ]);

            Clean::video($directory);
            return;
        }

        $directoryTo = $videoRepo->getDirectoryByID($this->video->id);

        $data = Upload::moveVideoToS3($directory, $directoryTo);

        $dataUpdate = [
            'data' => json_encode($data['data']),
            'preview' => $data['preview'],
            'status' => VIDEO_READY
        ];

        $videoRepo->update($this->video->id, $dataUpdate);
        $notify->create([
            'message' => 'The video ' . $this->video . ' is ready to use!',
            'user_ids' => [$this->video->created_by],
            'type' => NOTIFICATION_PRIVATE,
            'status' => NOTIFICATION_NEW
        ]);
        File::deleteDirectory($directory);
    }
}