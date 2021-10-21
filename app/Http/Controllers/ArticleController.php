<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Http\Requests\ArticleRequest;

class ArticleController extends Controller
{
    public function __construct()
    {
        // アクションに合わせたpolicyのメソッドで認可されていないユーザーはエラーを投げる
        $this->authorizeResource(Article::class, 'article');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $articles = Article::all();
        $articles = Article::with('attachments')->latest()->Paginate(10);
        
        return view('articles.index', compact('articles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('articles.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $article = new Article();
        $article->fill($request->all());
        
        $article->user_id = $request->user()->id;
        
        $files = $request->file;

        DB::beginTransaction();
        
        try {
            $article->save();
            
            $paths = [];

            foreach($files as $file){
                
                if (!$path = Storage::putFile('articles', $file)){
                    throw new Exception('ファイルの保存に失敗しました');
                }
                $paths[] = $path;

                
                $attachment = new Attachment([
                'article_id' => $article->id,
                'org_name' => $file->getClientOriginalName(),
                'attachment' => basename($path)
            ]);

            $attachment->save();
        }
            DB::commit();
        
        }catch (\Exception $e) {
            if (!empty($path)) {
                Storage::delete($path);
            }

            DB::rollback();
            return back()
                ->withErrors($e->getMessage());

            back()->withErrors(['error' => '保存に失敗しました']);
        }

        return redirect(route('articles.index'))
            ->with(['flash_message' => '登録が完了しました']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function show(Article $article)
    {
        return view('articles.show', compact('article'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function edit(Article $article)
    {
        return view('articles.edit', compact('article'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function update(ArticleRequest $request, Article $article)
    {
        $article->fill($request->all());

        try {
            $article->save();
        } catch (\Exception $e) {
            return back()
                ->withErrors($e->getMessage());
        }
        return redirect()
            ->route('articles.index')
            ->with(['flash_message' => '更新が完了しました']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function destroy(Article $article)
    {
        $path = $article->image_path;
        DB::beginTransaction();
        try {
            $article->delete();
            $article->attachment->delete();
            if(!Storage::delete($path)){
                throw new Exception('ファイルの削除に失敗しました');
            }
        DB::commit();
    } catch (\Exception $e) {
        DB::rollback();
        return back()
            ->withErrors($e->getMessage());
    }
        return redirect()
            ->route('articles.index')
            ->with(['flash_message' => '削除しました']);
    }
}
