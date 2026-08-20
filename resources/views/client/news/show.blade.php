@extends('client.layouts.app')

@section('title', $news->title)

@section('content')
<div class="news-detail-wrapper" style="background-color: #ffffff; min-height: 100vh; padding-bottom: 60px;">
    <div class="container py-4">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0" style="font-size: 0.8rem;">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none" style="color: #71717a;">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('client.news.index') }}" class="text-decoration-none" style="color: #71717a;">Tin tức</a></li>
                <li class="breadcrumb-item active" style="color: #27272a;" aria-current="page">{{ Str::limit($news->title, 45) }}</li>
            </ol>
        </nav>

        <div class="row g-4">
            <!-- Left Column: Article Body -->
            <div class="col-lg-8 pe-lg-4">
                <article class="bg-white p-2 p-md-3">
                    <div class="mb-2">
                        <span class="text-uppercase fw-semibold" style="font-size: 0.75rem; color: #71717a; letter-spacing: 0.04em;">{{ $news->type_label }}</span>
                    </div>

                    <!-- Soft Headline -->
                    <h1 class="article-headline fw-semibold mb-3" style="color: #27272a; font-size: 1.6rem; line-height: 1.38; letter-spacing: -0.01em;">
                        {{ $news->title }}
                    </h1>

                    <!-- Article Meta -->
                    <div class="article-meta mb-4 pb-3 border-bottom d-flex align-items-center gap-3" style="font-size: 0.775rem; color: #a1a1aa; border-color: #f4f4f5 !important;">
                        <span>{{ $news->published_at ? $news->published_at->format('d/m/Y H:i') : $news->created_at->format('d/m/Y H:i') }}</span>
                        <span>&bull;</span>
                        <span>{{ number_format($news->view_count) }} lượt xem</span>
                    </div>

                    <!-- Featured Image -->
                    @if($news->featured_image_url)
                        <div class="mb-4">
                            @include('client.partials.cover-image', [
                                'src' => $news->featured_image_url,
                                'alt' => $news->title,
                                'ratio' => '16/9',
                            ])
                        </div>
                    @endif

                    <!-- Sapo / Summary -->
                    @if($news->summary)
                        <div class="article-sapo mb-4 p-3 rounded-2" style="background-color: #fafafa; border-left: 2.5px solid #27272a; font-size: 0.975rem; font-weight: 500; color: #3f3f46; line-height: 1.6;">
                            {{ $news->summary }}
                        </div>
                    @endif

                    <!-- Content Body -->
                    <div class="article-content-body" style="font-size: 1rem; line-height: 1.75; color: #3f3f46; font-weight: 400;">
                        {!! \App\Models\News::rewriteContentImageUrls($news->content) !!}
                    </div>
                </article>
            </div>

            <!-- Right Column: Related Articles Sidebar -->
            <div class="col-lg-4">
                <div class="sidebar position-sticky" style="top: 90px;">
                    <div class="pb-3 border-bottom" style="border-color: #e5e7eb !important;">
                        <div class="mb-3 pb-2 border-bottom" style="border-color: #e5e7eb !important;">
                            <span class="fw-semibold" style="color: #3f3f46; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.03em;">Bài viết liên quan</span>
                        </div>

                        <div class="related-list d-flex flex-column gap-3">
                            @forelse($relatedNews as $item)
                                <article class="related-item">
                                    <a href="{{ route('client.news.show', $item->slug) }}" class="text-decoration-none group-news-link d-flex gap-3 align-items-start">
                                        <div class="overflow-hidden rounded bg-light flex-shrink-0" style="width: 85px; aspect-ratio: 4/3;">
                                            @include('client.partials.cover-image', [
                                                'src' => $item->featured_image_url,
                                                'alt' => $item->title,
                                                'ratio' => '4/3',
                                            ])
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="related-title fw-normal mb-1" style="color: #27272a; font-size: 0.85rem; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                                {{ $item->title }}
                                            </h6>
                                            <div style="color: #a1a1aa; font-size: 0.725rem;">
                                                {{ $item->published_at ? $item->published_at->format('d/m/Y') : $item->created_at->format('d/m/Y') }}
                                            </div>
                                        </div>
                                    </a>
                                </article>
                                @if(!$loop->last)
                                    <div class="border-bottom" style="border-color: #f4f4f5 !important;"></div>
                                @endif
                            @empty
                                <p style="color: #71717a; font-size: 0.8rem;">Không có bài viết liên quan nào.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .group-news-link:hover .related-title { color: #000000 !important; text-decoration: underline; }
    .group-news-link:hover .img-fade { opacity: 0.88; }

    .article-content-body p { margin-bottom: 1.2rem; }
    .article-content-body img { max-width: 100%; height: auto; border-radius: 4px; margin: 14px 0; }
    .article-content-body h2, .article-content-body h3 { color: #27272a; font-weight: 600; margin-top: 1.4rem; margin-bottom: 0.6rem; }
</style>
@endsection
