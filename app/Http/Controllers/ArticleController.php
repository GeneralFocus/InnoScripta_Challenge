<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\ArticleService;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\ArticleResource;
use App\Http\Requests\ArticleIndexRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ArticleController extends Controller
{
    public function __construct(
        private readonly ArticleService $service
    ) {
    }

    public function index(ArticleIndexRequest $request): AnonymousResourceCollection
    {
        $articles = $this->service->getArticles(
            $request->getFilters(),
            $request->getPerPage()
        );

        return ArticleResource::collection($articles);
    }

    public function show(int $id): JsonResponse
    {
        $article = $this->service->getArticle($id);

        if (!$article) {
            return $this->error('Article not found', [], 404);
        }

        return $this->success('Article retrieved', new ArticleResource((object) $article));
    }
}
