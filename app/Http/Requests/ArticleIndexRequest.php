<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category' => ['sometimes', 'string'],
            'source'   => ['sometimes', 'string'],
            'search'   => ['sometimes', 'string'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function getFilters(): array
    {
        return $this->only(['category', 'source', 'search']);
    }

    public function getPerPage(): int
    {
        return (int)($this->input('per_page', 20));
    }
}
