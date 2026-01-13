 <!-- Product Information -->
 <div class="product-info">
    <div class="product-name">{{ $review->product ? $review->product->name : 'Product Not Found' }}</div>
    <small class="text-muted">Product being reviewed</small>
</div>

<!-- Reviewer Information -->
<div class="reviewer-info">
    <div class="reviewer-avatar">
        {{ $review->user ? strtoupper(substr($review->user->name, 0, 2)) : 'NA' }}
    </div>
    <div>
        <div class="reviewer-name">{{ $review->user ? $review->user->name : 'User Not Found' }}</div>
        <small class="text-muted">Reviewer</small>
    </div>
</div>

<!-- Rating Section -->
<div class="rating-section">
    <div class="stars">
        @for ($i = 1; $i <= 5; $i++)
            <i class="fas fa-star star {{ $i <= $review->rating ? '' : 'empty' }}"></i>
        @endfor
    </div>
    <div class="rating-badge">
        {{ $review->rating }}/5 Stars
    </div>
</div>

<!-- Comment Section -->
<div class="comment-section">
    <div class="comment-label">Review Comment</div>
    <div class="comment-text">
        {{ $review->comment }}
    </div>
</div>

<!-- Review Meta Information -->
<div class="review-meta">
    <div class="review-date">
        <i class="far fa-calendar-alt"></i>
        Submitted on {{ $review->created_at}}
    </div>
    <div class="admin-actions">
        @if($review->is_approve)
        <span class="status-badge status-pending">Pending</span>
        @else
        <span class="status-badge status-approved">Approved</span>
        @endif
    </div>
</div>


<style>
    .product-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #007bff;
        }

        .product-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: #495057;
            margin-bottom: 5px;
        }

        .reviewer-info {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }

        .reviewer-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: #007bff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            font-weight: 600;
            color: white;
        }

        .reviewer-name {
            font-size: 1rem;
            font-weight: 500;
            color: #495057;
            margin: 0;
        }

        .rating-section {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .stars {
            display: flex;
            gap: 3px;
        }

        .star {
            color: #ffc107;
            font-size: 1.1rem;
        }

        .star.empty {
            color: #e9ecef;
        }

        .rating-badge {
            background: #ffc107;
            color: #212529;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .comment-section {
            margin-bottom: 20px;
        }

        .comment-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 10px;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .comment-text {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #28a745;
            line-height: 1.6;
            color: #495057;
            margin: 0;
            font-size: 0.95rem;
        }

        .review-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 15px;
            border-top: 1px solid #e9ecef;
        }

        .review-date {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #6c757d;
            font-size: 0.9rem;
        }

        .admin-actions {
            display: flex;
            gap: 10px;
        }

        .btn-sm {
            padding: 5px 12px;
            font-size: 0.8rem;
        }

        .status-badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-approved {
            background: #d4edda;
            color: #155724;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }

        @media (max-width: 576px) {
            .modal-dialog {
                margin: 10px;
            }

            .review-meta {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }

            .admin-actions {
                width: 100%;
                justify-content: space-between;
            }
        }
</style>
