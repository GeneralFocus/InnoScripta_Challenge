<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\ArticleService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\ArticleIndexRequest;
use App\Http\Resources\ArticleResource;

class ArticleController extends Controller
{
    public function __construct(private readonly ArticleService $service)
    {
    }

    public function index(ArticleIndexRequest $request): JsonResponse
    {
        $articles = $this->service->getArticles(
            $request->getFilters(),
            $request->getPerPage()
        );

        return $this->success(
            'Articles retrieved',
            ArticleResource::collection($articles)
        );
    }

    public function show(string $id): JsonResponse
    {
        if (!ctype_digit($id)) {
            return $this->error('Invalid article id', [], 422);
        }

        $article = $this->service->getArticle((int) $id);

        if (!$article) {
            return $this->error('Article not found', [], 404);
        }

        return $this->success('Article retrieved', new ArticleResource($article));

    }
}
