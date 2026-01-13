@extends('frontend.layouts.app')
@section('content')

<x-page-banner
    :backgroundImage="asset('assets/page_banner.jpg')"
    :breadcrumbs="$breadcrumbs"
/>


<section class="blog-section py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10 col-xl-8">
                <article class="blog-post bg-white p-4 p-md-5 shadow-sm rounded-3" itemscope itemtype="http://schema.org/BlogPosting">
                    <meta itemprop="datePublished" content="{{ $blog->created_at->toIso8601String() }}">
                    <meta itemprop="dateModified" content="{{ $blog->updated_at->toIso8601String() }}">
                    <meta itemprop="author" content="{{ config('app.name') }}">

                    {{-- Header --}}
                    <div class="blog-post__header mb-4">
                        <h1 class="blog-post__title h2 fw-bold mb-3" itemprop="headline">{!! $blog->title !!}</h1>
                        <div class="blog-post__meta text-muted small">
                            <span class="blog-post__date">
                                <i class="las la-calendar me-1"></i>
                                {{ $blog->created_at->format('M d, Y') }}
                            </span>
                        </div>
                    </div>

                    {{-- Thumbnail --}}
                    @if($blog->thumbnail)
                    <div class="blog-post__image mb-4">
                        <img src="{{ $blog->thumbnail }}"
                            alt="{{ $blog->title }}"
                            class="img-fluid rounded-3 w-100"
                            itemprop="image">
                    </div>
                    @endif

                    {{-- Content --}}
                    <div class="blog-post__content" itemprop="articleBody">
                        {!! $blog->content !!}
                    </div>

                    {{-- Tags --}}
                    @if($blog->tags)
                    <div class="blog-post__tags mt-5">
                        @foreach(explode(',', $blog->tags) as $tag)
                        <span class="badge bg-secondary bg-opacity-10 text-dark me-1">{{ trim($tag) }}</span>
                        @endforeach
                    </div>
                    @endif

                    {{-- Share --}}
                    <div class="blog-post__share mt-5 border-top pt-4">
                        <h5 class="mb-3">Share this article:</h5>
                        <div class="d-flex gap-2">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                               target="_blank"
                               class="btn btn-outline-primary btn-sm">
                                <i class="lab la-facebook-f me-1"></i> Facebook
                            </a>
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($blog->title) }}"
                               target="_blank"
                               class="btn btn-outline-info btn-sm">
                                <i class="lab la-twitter me-1"></i> Twitter
                            </a>
                            <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(url()->current()) }}"
                               target="_blank"
                               class="btn btn-outline-secondary btn-sm">
                                <i class="lab la-linkedin-in me-1"></i> LinkedIn
                            </a>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </div>
</section>





</section>


@endsection
