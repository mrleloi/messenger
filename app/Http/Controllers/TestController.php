<?php

namespace App\Http\Controllers;

use App\Contracts\VideoDriver;
use App\Models\Call;
use App\Models\Thread;
use Illuminate\Http\Request;
use RTippin\Janus\Exceptions\JanusApiException;
use RTippin\Janus\Exceptions\JanusPluginException;
use RTippin\Janus\Plugins\VideoRoom;

class TestController extends Controller
{
    private VideoDriver $videoDriver;
    protected VideoRoom $videoRoom;

    public function __construct(VideoDriver $videoDriver, VideoRoom $videoRoom)
    {
        $this->videoDriver = $videoDriver;
        $this->videoRoom = $videoRoom;
    }

    public function index(Request $request) {
        try {
            $thread = Thread::find('9aa78e7a-eaaa-49b2-8751-b32bf174305e')->first();
            $call = Call::find('9ab00934-f765-4844-a5b0-b5ec4bf521de')->first();
            $janus = $this->videoRoom->list();
            dd($janus);
        } catch (JanusApiException|JanusPluginException $e) {
        } catch (\Exception $e) {
            dd($e->getMessage());
            dd($e);

            return false;
        }
//        if (auth('web')->check()) echo 'Logged web!';
//        if (auth('employee')->check()) echo 'Logged employee!';
//        if (auth('admin')->check()) echo 'Logged admin!';
//        $user = messenger()->getProvider();
//        echo 'provider - '. $user;
//        $emp = $request->user();
//        echo 'emp - '. $emp;
//        die();
    }
}
