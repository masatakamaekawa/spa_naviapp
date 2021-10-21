@extends('layouts.main')
@section('title', '温泉一覧')
@section('content')
    @include('partial.flash')
    @include('partial.errors')
    <section class="row position-relative" data-masonry='{ "percentPosition": true }'>
            @foreach ($articles as $article)
            <div class="mb-4">
                <article class="card position-relative">
                    <div class="card-img-top">
                        <img src="{{ Storage::url('articles/' . $article->attachments[0]->name) }}" width="100%">
                        <div class="card-title mx-3">
                            <a href="{{ route('articles.show', $article) }}" class="text-decoration-none stretched-link">
                                {{ $article->caption }}
                            </a>
                        </div>
                    </article>
        </div>
        @endforeach
    </section>
    <a href="{{ route('articles.create') }}" class="position-fixed fs-1 bottom-right-30 zindex-sticky">
        <i class="fas fa-camera"></i>
    </a>
@endsection
