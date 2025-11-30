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
            'category'  => ['nullable', 'string'],
            'source'    => ['nullable', 'string'],
            'search'    => ['nullable', 'string'],
            'author'    => ['nullable', 'string'],
            'date_from' => ['nullable', 'date'],
            'date_to'   => ['nullable', 'date', 'after_or_equal:date_from'],
            'per_page'  => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function getFilters(): array
    {
        return $this->only([
            'category',
            'source',
            'search',
            'author',
            'date_from',
            'date_to',
        ]);
    }

    public function getPerPage(): int
    {
        return (int)($this->input('per_page', 20));
    }
}
