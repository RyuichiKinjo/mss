<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Header;
use App\Models\Detail;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    // public function index()
    // {
    //     $datas = DB::table('details')->get();
    //     $datas = $this->formatAll($datas);
    //     return view('home', compact('datas'));
    // }

    // public function search(Request $request)
    public function index(Request $request)
    {
        Log::info("---- index start ----");

        $user = Auth::user();
        Log::info("name:".$user->name);

        // header取得
        $id = Auth::id();
        // $header = DB::table('headers')->where('id', $id)->count();
        $header = DB::table('headers')->where('user_id', $id)->first();
        // Log::info("test3:".$header);
        Log::info("test2:".$id);
        if ($header == null) {
            Log::info("test1");
            // header登録
            Header::create([
                'user_id' => $id,
                'name' => null,
                'initial' => null,
                'sex' => null,
                'birthday' => null,
                'station' => null,
                'cert' => null,
            ]);
        } else {
            Log::info("test2:".$header->name);
        }

        // inputのname属性を指定
        $lang = $request->input('formLang');
        $db = $request->input('formDb');
        $env = $request->input('formEnv');

        // 印刷ボタンの場合は別処理を実行

        $datas = DB::table('details')
            ->when($lang !== null, function ($query) use ($lang) {
                $query->where('lang', 'LIKE', "%$lang%");
            })
            ->when($db !== null, function ($query) use ($db) {
                $query->where('db', 'LIKE', "%$db%");
            })
            ->when($env !== null, function ($query) use ($env) {
                $query->where('env', 'LIKE', "%$env%");
            })
            ->orderByDesc('start')
            ->get();
        // $datas = $this->formatAll($datas);

        // return view('home', compact('datas'))->with('formLang', $lang);
        return view('home')
        ->with('datas', $datas)
        ->with('id', $id)
        ->with('formLang', $lang)
        ->with('formDb', $db)
        ->with('formEnv', $env);
    }

    public function create()
    {
    }

    public function store(Request $request)
    {
        Log::info("---- store start ----");
        $start = $request->input('start');
        $end = $request->input('end');
        $system = $request->input('system');
        $role = $request->input('role');
        $phase = $request->input('phase');
        $lang = $request->input('lang');
        $db = $request->input('db');
        $env = $request->input('env');
        $overview = $request->input('overview');
        Log::info('start:' . $start);
        Log::info('end:' . $end);
        Log::info('system:' . $system);
        Log::info('role:' . $role);
        Log::info('phase:' . $phase);
        Log::info('lang:' . $lang);
        Log::info('db:' . $db);
        Log::info('env:' . $env);
        Log::info('overview:' . $overview);

        Detail::create([
            'user_id' => Auth::id(),
            'start' => $start,
            'end' => $end,
            'system' => $system,
            'role' => $role,
            'phase' => $phase,
            'lang' => $lang,
            'db' => $db,
            'env' => $env,
            'overview' => $overview,
        ]);

        return redirect()->route('mss.index');
    }

    public function upsert(Request $request)
    {
        Log::info("---- upsert start ----");
        $seq = $request->input('seq');
        $start = $request->input('start');
        $end = $request->input('end');
        $system = $request->input('system');
        $role = $request->input('role');
        $phase = $request->input('phase');
        $lang = $request->input('lang');
        $db = $request->input('db');
        $env = $request->input('env');
        $overview = $request->input('overview');
        Log::info('id:' . Auth::id());
        Log::info('start:' . $start);
        Log::info('end:' . $end);
        Log::info('system:' . $system);
        Log::info('role:' . $role);
        Log::info('phase:' . $phase);
        Log::info('lang:' . $lang);
        Log::info('db:' . $db);
        Log::info('env:' . $env);
        Log::info('overview:' . $overview);

        if ($seq) {
            Detail::where('user_id', Auth::id())
            ->where('seq', $seq)
            ->update([
                'start' => $start,
                'end' => $end,
                'system' => $system,
                'role' => $role,
                'phase' => $phase,
                'lang' => $lang,
                'db' => $db,
                'env' => $env,
                'overview' => $overview,
            ]);
        } else {
            Detail::create([
                'user_id' => Auth::id(),
                'start' => $start,
                'end' => $end,
                'system' => $system,
                'role' => $role,
                'phase' => $phase,
                'lang' => $lang,
                'db' => $db,
                'env' => $env,
                'overview' => $overview,
            ]);
        }
        return redirect()->route('mss.index');
    }

    public function show()
    {
    }

    public function edit()
    {
    }

    public function destory(Request $request)
    {
        Log::info("---- destroy start ----");
        $id = $request->input('deleteId');
        Log::info('user_id:' . $id);

        Detail::where('user_id', $id)->delete();
        return redirect()->route('mss.index');
    }

    public function personal()
    {
        Log::info("---- personal start ----");

        Header::where('user_id', Auth::id())
        ->update([
            'name' => 'test',
            'initial' => 'kj',
            'sex' => '1',
            'birthday' => null,
            'station' => 'morinomiya',
            'cert' => 'oracle db gold 11',
        ]);


        return redirect()->route('mss.index');
    }

    private function formatAll($datas)
    {
        foreach ($datas as $data) {
            $data->start = $this->formatYmdToYm($data->start);
            $data->end = $this->formatYmdToYm($data->end);
        }
        return $datas;
    }

    private function formatYmdToYm($value)
    {
        $value = date_create($value);
        return date_format($value, 'Y-m-d');
    }
}
