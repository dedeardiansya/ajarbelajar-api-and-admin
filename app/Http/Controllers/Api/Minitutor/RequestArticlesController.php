<?php

namespace App\Http\Controllers\Api\Minitutor;

use App\Helpers\EditorjsHelper;
use App\Helpers\HeroHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\RequestArticleResource;
use App\Models\Category;
use App\Models\Image;
use App\Models\RequestArticle;
use Illuminate\Http\Request;

class RequestArticlesController extends Controller
{
    public function index(Request $request)
    {
        $minitutor = $request->user()->minitutor;
        $articles = $minitutor->requestArticles()->with(['hero', 'category'])->orderBy('updated_at', 'desc')->get();
        return response()->json(RequestArticleResource::collection($articles), 200);
    }

    public function show(Request $request, $id)
    {
        $minitutor = $request->user()->minitutor;
        $article = $minitutor->requestArticles()->with(['hero', 'category'])->findOrFail($id);
        return response()->json(RequestArticleResource::make($article), 200);
    }

    public function store(Request $request)
    {
        $minitutor = $request->user()->minitutor;
        $data = $request->validate([
            'title' => 'required|string|min:6|max:250',
            'description' => 'nullable|string',
        ]);
        $article = new RequestArticle($data);
        $minitutor->requestArticles()->save($article);
        return response()->json(['id' => $article->id], 200);
    }

    public function update(Request $request, $id)
    {
        $minitutor = $request->user()->minitutor;
        $article = $minitutor->requestArticles()->findOrFail($id);
        $data = $request->validate([
            'title' => 'required|string|min:6|max:250',
            'description' => 'nullable',
            'category' => 'nullable|string',
            'body' => 'nullable',
        ]);
        if (isset($data['category'])) {
            $data['category_id'] = Category::getCategoryOrCreate($data['category'])->id;
            unset($data['category']);
        } else {
            $data['category_id'] = null;
        }
        $article->update($data);
        if (isset($data['body'])) {
            EditorjsHelper::cleanImage($data['body'], $article->images);
        }

        $this->timestamps = false;
        if (!$request->input('requested') && $article->requested_at) {
            $article->requested_at = null;
            $article->save();
        } elseif ($request->input('requested') && !$article->requested_at) {
            $article->requested_at = now();
            $article->save();
        }
        $this->timestamps = true;
        return response()->json(RequestArticleResource::make($article), 200);
    }

    public function updateHero(Request $request, $id)
    {
        $minitutor = $request->user()->minitutor;
        $article = $minitutor->requestArticles()->findOrFail($id);
        $data = $request->validate(['hero' => 'nullable|image|max:4000']);
        $name = HeroHelper::generate($data['hero'], $article->hero ? $article->hero->name : null);
        $hero = $article->hero;
        if ($hero) {
            $hero->update([
                'type' => 'hero',
                'name' => $name,
                'original_name' => $data['hero']->getClientOriginalName(),
            ]);
        } else {
            $article->hero()->save(new Image([
                'type' => 'hero',
                'name' => $name,
                'original_name' => $data['hero']->getClientOriginalName(),
            ]));
            $article->load('hero');
        }
        $article->touch();
        return response()->json(HeroHelper::getUrl($name), 200);
    }

    public function uploadImage(Request $request, $id)
    {
        $minitutor = $request->user()->minitutor;
        $article = $minitutor->requestArticles()->findOrFail($id);
        $data = $request->validate(['file' => 'required|image|max:4000']);
        $upload = EditorjsHelper::uploadImage($data['file']);
        $article->images()->save(new Image([
            'name' => $upload['name'],
            'type' => 'image',
            'original_name' => $data['file']->getClientOriginalName(),
        ]));
        // response for editorjs image uploader
        return response()->json(['success' => 1, 'file' => $upload]);
    }

    public function destroy(Request $request, $id)
    {
        $minitutor = $request->user()->minitutor;
        $article = $minitutor->requestArticles()->findOrFail($id);
        foreach ($article->images as $image) {
            EditorjsHelper::deleteImage($image->name);
            $image->delete();
        }
        HeroHelper::destroy($article->hero ? $article->hero->name : null);
        $article->delete();
        return response()->noContent();
    }
}
