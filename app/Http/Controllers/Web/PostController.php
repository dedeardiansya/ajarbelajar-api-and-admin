<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Model\PostComment;
use App\Model\Post;
use App\Model\PostReview;
use App\Model\PostView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\JsonLd;
use Artesaos\SEOTools\Facades\OpenGraph;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    public function show(Request $request, $slug)
    {
        $posts = Post::where('draf', 0)
            ->where('slug', $slug)
            ->with(['comments' => function($query) use($request) {
                $query->where('approved', 1)->orderBy('created_at', 'desc');
            }])
            ->withCount(['views'=> function($q){
                $q->select(DB::raw('count(distinct(ip))'));
            }]);

        if(!$posts->exists()) return abort(404);

        $post = $posts->first();

        $rating = null;

        if($request->user()) {
            $rating = $post->reviews()->where('user_id', Auth::user()->id);
            $rating = ($rating->exists()) ? $rating->first() : null; 
        }

        $keywords = [];
        foreach($post->tags as $tag) array_push($keywords, $tag->name);

        SEOMeta::setTitle($post->title);
        SEOMeta::setDescription($post->description);
        SEOMeta::addMeta('article:published_time', $post->created_at->toW3CString(), 'property');
        if($post->category) SEOMeta::addMeta('article:section', $post->category->name, 'property');
        SEOMeta::addKeyword($keywords);

        OpenGraph::setDescription($post->description);
        OpenGraph::setTitle($post->title);
        OpenGraph::setUrl(url()->current());
        OpenGraph::addProperty('type', 'article');
        OpenGraph::addProperty('locale', 'id-ID');

        OpenGraph::addImage($post->heroUrl());

        JsonLd::setTitle($post->title);
        JsonLd::setDescription($post->description);
        JsonLd::setType('Article');
        // JsonLd::addImage($post->images->list('url'));

        $post->views()->save(PostView::createViewLog($request));
        return view('web.post.show', ['post' => $post, 'review' => $rating ]);
    }

    public function storeComment(Request $request, $id)
    {
        $data = $request->validate([ 'body' => 'required|string|min:3|max:600' ]);
        $data['user_id'] = $request->user()->id;
        $data['approved'] = 0;
        $post = Post::where('draf', 0)->findOrFail($id);
        $comment = new PostComment($data);
        $post->comments()->save($comment);
        return redirect()->back()->withSuccess('Komentar kamu telah terkirim dan akan segera terbit.');
    }

    public function storeReview(Request $request, $id)
    {
        $post = Post::findOrFail($id);
        $data = $request->validate([
            'body' => 'required|string|min:3|max:600',
            'rating' => 'required|numeric|digits_between:1,5'
        ]);
        $q = $post->reviews()->where('user_id', Auth::user()->id);
        if($q->exists()) {
            $q->first()->update($data);
        } else {
            PostReview::create([
                'user_id' => $request->user()->id,
                'post_id' => $id,
                'body' => $data['body'],
                'rating' => $data['rating'],
            ]);
        }
        return redirect()->back()->withSuccess('Terima kasih atas ulasan kamu.');
    }
}
