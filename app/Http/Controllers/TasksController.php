<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Task; //追加しないと使えない！！！！
use Auth; //ここも追加

class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [];
        if (\Auth::check()) { // 認証済みの場合
            // 認証済みユーザを取得
            $user = \Auth::user();
            // ユーザの投稿の一覧を作成日時の降順で取得
            $tasks = $user->tasks()->orderBy('created_at', 'desc')->paginate(10);

            $data = [
                'user' => $user,
                'tasks' => $tasks,
            ];
        }

        // Welcomeビューでそれらを表示
        return view('welcome', $data);
        
        
        /*タスクの一覧を取得
        $tasks = Task::all();
        
        //タスク一覧ビューで取得したものを表示 tasks.index<-welcome
        return view('tasks.index', ['tasks' => $tasks,]);
        上記へ変更*/
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       $task = new Task;
        
        //メニュー作成ビューを表示
        return view('tasks.create', ['task' => $task,]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'status' => 'required|max:10',
            'content' => 'required|max:255',
            ]);

        
        $task = new Task;
        $task->user_id = Auth::id();
        $task->status = $request->status;
        $task->content = $request->content;
        $task->save();
        

        // トップページへリダイレクトさせる
        return redirect('/');
    
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
        //idの値でタスクを検索して取得
        $task = Task::findOrFail($id);
        
        
         // 認証済みユーザ（閲覧者）がその投稿の所有者である場合は、if内を実行
        if (Auth::id() === $task->user_id) {
        //タスク詳細ビューでそれを表示
        return view('tasks.show', ['task' => $task,]);
        }
        
        return redirect('/');
        
        
    
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //idの値でタスクを検索して取得
        $task = Task::findOrFail($id);
        
        // 認証済みユーザ（閲覧者）がその投稿の所有者である場合は、if内を実行
        if (Auth::id() === $task->user_id) {
        
        //タスク編集ビューでそれを表示
        return view('tasks.edit', ['task' => $task,]);
        
        }
        
        //トップページへリダイレクトさせる
        return redirect('/');
   
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // バリデーション
        
        $request->validate([
            'status' => 'required|max:10',
            'content' => 'required|max:255',
            ]);
        //idの値でタスクを検索して取得
        $task = Task::findOrFail($id);
        
        // 認証済みユーザ（閲覧者）がその投稿の所有者である場合は、if内を実行
        if (Auth::id() === $task->user_id) {
        //タスクを更新
        $task->status = $request->status;
        $task->content = $request->content;
        $task->save();
        
        }
        //トップページへリダイレクトさせる
        return redirect('/');
   
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     **/
    public function destroy($id)
    {
        // idの値で投稿を検索して取得
        $task = Task::findOrFail($id);

        // 認証済みユーザ（閲覧者）がその投稿の所有者である場合は、投稿を削除
        if (Auth::id() === $task->user_id) {
            $task->delete();
        }
        //トップページへリダイレクトさせる
        return redirect('/');
   
    }
}