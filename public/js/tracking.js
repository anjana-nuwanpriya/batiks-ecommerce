/**
 * Tracking System JavaScript
 * Handles real-time tracking updates and user interactions
 */

class TrackingSystem {
    constructor() {
        this.refreshInterval = null;
        this.autoRefreshEnabled = true;
        this.refreshIntervalTime = 300000; // 5 minutes
        this.init();
    }

    init() {
        this.bindEvents();
        this.startAutoRefresh();
    }

    bindEvents() {
        // Refresh button click
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-action="refresh-tracking"]') ||
                e.target.closest('[data-action="refresh-tracking"]')) {
                e.preventDefault();
                this.refreshTracking();
            }
        });

        // Auto-refresh toggle
        document.addEventListener('change', (e) => {
            if (e.target.matches('#auto-refresh-toggle')) {
                this.toggleAutoRefresh(e.target.checked);
            }
        });

        // Copy waybill number
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-action="copy-waybill"]') ||
                e.target.closest('[data-action="copy-waybill"]')) {
                e.preventDefault();
                this.copyWaybillNumber(e.target);
            }
        });
    }

    async refreshTracking() {
        const waybillNo = this.getWaybillNumber();
        if (!waybillNo) return;

        const timeline = document.getElementById('tracking-timeline');
        const refreshBtn = document.querySelector('[data-action="refresh-tracking"]');

        if (timeline) timeline.classList.add('loading');
        if (refreshBtn) {
            refreshBtn.disabled = true;
            const icon = refreshBtn.querySelector('i');
            if (icon) icon.classList.add('fa-spin');
        }

        try {
            const response = await fetch('/tracking/status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({ waybill_no: waybillNo })
            });

            const data = await response.json();

            if (data.success) {
                this.updateTrackingDisplay(data.data);
                this.showNotification('Tracking information updated successfully', 'success');
            } else {
                this.showNotification('Failed to refresh tracking information', 'error');
            }
        } catch (error) {
            console.error('Error refreshing tracking:', error);
            this.showNotification('Error occurred while refreshing', 'error');
        } finally {
            if (timeline) timeline.classList.remove('loading');
            if (refreshBtn) {
                refreshBtn.disabled = false;
                const icon = refreshBtn.querySelector('i');
                if (icon) icon.classList.remove('fa-spin');
            }
        }
    }

    updateTrackingDisplay(trackingData) {
        const timeline = document.getElementById('tracking-timeline');
        if (!timeline || !Array.isArray(trackingData) || trackingData.length === 0) return;

        // Update timeline
        let timelineHTML = '<div class="tracking-timeline">';

        trackingData.forEach((status, index) => {
            const isActive = index === 0 ? 'active' : '';
            const hasLine = index < trackingData.length - 1;

            // Handle both statusDate and dateTime fields
            let dateTimeHTML = '<small class="text-muted">N/A</small>';
            if (status.statusDate) {
                const date = new Date(status.statusDate);
                dateTimeHTML = `
                    <small class="text-success fw-medium d-block" style="color: #00664b !important;">
                        <i class="las la-clock me-1" style="color: #00664b;"></i>
                        ${date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })}
                    </small>
                    <small class="text-muted">
                        ${date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true })}
                    </small>
                `;
            } else if (status.dateTime) {
                const date = new Date(status.dateTime);
                dateTimeHTML = `
                    <small class="text-success fw-medium d-block" style="color: #00664b !important;">
                        <i class="las la-clock me-1" style="color: #00664b;"></i>
                        ${date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })}
                    </small>
                    <small class="text-muted">
                        ${date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true })}
                    </small>
                `;
            }

            // Build status details
            let statusDetailsHTML = '';

            // Branch name
            if (status.branchName && status.branchName.trim()) {
                statusDetailsHTML += `
                    <div class="mb-2">
                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2" style="background-color: rgba(0, 102, 75, 0.1) !important; color: #00664b !important; border: 1px solid rgba(0, 102, 75, 0.2);">
                            <i class="las la-building me-1" style="color: #00664b;"></i>${status.branchName}
                        </span>
                    </div>
                `;
            }

            // Location
            if (status.location && status.location.trim()) {
                statusDetailsHTML += `
                    <p class="text-muted mb-1">
                        <i class="las la-map-marker-alt me-1 text-danger"></i>
                        <span class="fw-medium">${status.location}</span>
                    </p>
                `;
            }

            // Remarks
            if (status.remarks && status.remarks.trim()) {
                statusDetailsHTML += `
                    <p class="text-muted mb-0">
                        <i class="las la-info-circle me-1 text-info"></i>
                        ${status.remarks}
                    </p>
                `;
            }

            // Description
            if (status.description && status.description.trim()) {
                statusDetailsHTML += `
                    <p class="text-muted mb-0">
                        <i class="las la-file-alt me-1 text-secondary"></i>
                        ${status.description}
                    </p>
                `;
            }

            timelineHTML += `
                <div class="timeline-item ${isActive}">
                    <div class="timeline-marker">
                        <div class="timeline-dot"></div>
                        ${hasLine ? '<div class="timeline-line"></div>' : ''}
                    </div>
                    <div class="timeline-content">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="fw-bold mb-1 text-primary" style="color: #00664b !important;">${status.status || 'Status Update'}</h6>
                            <div class="text-end">
                                ${dateTimeHTML}
                            </div>
                        </div>
                        <div class="status-details">
                            ${statusDetailsHTML}
                        </div>
                    </div>
                </div>
            `;
        });

        timelineHTML += '</div>';
        timeline.innerHTML = timelineHTML;

        // Update current status card if exists
        this.updateCurrentStatusCard(trackingData[0]);
    }

    updateCurrentStatusCard(currentStatus) {
        const statusCard = document.querySelector('.current-status-card');
        if (!statusCard || !currentStatus) return;

        const statusText = statusCard.querySelector('.status-text');
        const statusLocation = statusCard.querySelector('.status-location');
        const statusTime = statusCard.querySelector('.status-time');

        if (statusText) statusText.textContent = currentStatus.status || 'Processing';

        if (statusLocation && currentStatus.location) {
            statusLocation.innerHTML = `<i class="las la-map-marker-alt me-1"></i>${currentStatus.location}`;
        }

        if (statusTime) {
            if (currentStatus.statusDate) {
                const date = new Date(currentStatus.statusDate);
                statusTime.innerHTML = `
                    <span class="text-success fw-medium" style="color: #00664b !important;">${date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })}</span>
                    <small class="text-muted d-block">${date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true })}</small>
                `;
            } else if (currentStatus.dateTime) {
                const date = new Date(currentStatus.dateTime);
                statusTime.innerHTML = `
                    <span class="text-success fw-medium" style="color: #00664b !important;">${date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })}</span>
                    <small class="text-muted d-block">${date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true })}</small>
                `;
            }
        }

        // Update branch name if there's a container for it
        const branchContainer = statusCard.querySelector('.status-branch');
        if (branchContainer && currentStatus.branchName && currentStatus.branchName.trim()) {
            branchContainer.innerHTML = `
                <small class="badge bg-primary bg-opacity-10 text-primary" style="background-color: rgba(0, 102, 75, 0.1) !important; color: #00664b !important; border: 1px solid rgba(0, 102, 75, 0.2);">
                    ${currentStatus.branchName}
                </small>
            `;
        }
    }

    startAutoRefresh() {
        if (!this.autoRefreshEnabled) return;

        this.refreshInterval = setInterval(() => {
            this.refreshTracking();
        }, this.refreshIntervalTime);
    }

    stopAutoRefresh() {
        if (this.refreshInterval) {
            clearInterval(this.refreshInterval);
            this.refreshInterval = null;
        }
    }

    toggleAutoRefresh(enabled) {
        this.autoRefreshEnabled = enabled;

        if (enabled) {
            this.startAutoRefresh();
            this.showNotification('Auto-refresh enabled', 'info');
        } else {
            this.stopAutoRefresh();
            this.showNotification('Auto-refresh disabled', 'info');
        }
    }

    copyWaybillNumber(element) {
        const waybillNo = this.getWaybillNumber();
        if (!waybillNo) return;

        navigator.clipboard.writeText(waybillNo).then(() => {
            this.showNotification('Waybill number copied to clipboard', 'success');

            // Visual feedback
            const originalText = element.innerHTML;
            element.innerHTML = '<i class="las la-check"></i> Copied!';
            element.classList.add('btn-success');

            setTimeout(() => {
                element.innerHTML = originalText;
                element.classList.remove('btn-success');
            }, 2000);
        }).catch(() => {
            this.showNotification('Failed to copy waybill number', 'error');
        });
    }

    getWaybillNumber() {
        // Try to get waybill number from various sources
        const waybillElement = document.querySelector('[data-waybill]');
        if (waybillElement) return waybillElement.dataset.waybill;

        const waybillInput = document.querySelector('input[name="waybill_no"]');
        if (waybillInput) return waybillInput.value;

        // Try to extract from URL or page content
        const urlMatch = window.location.pathname.match(/track-order\/(.+)/);
        if (urlMatch) return urlMatch[1];

        return null;
    }

    showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';

        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(notification);

        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }

    // Utility method to format dates
    formatDate(dateString) {
        if (!dateString) return 'N/A';

        try {
            const date = new Date(dateString);
            return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], {
                hour: '2-digit',
                minute: '2-digit'
            });
        } catch (error) {
            return dateString;
        }
    }

    // Method to check if tracking is available
    isTrackingAvailable() {
        return !!this.getWaybillNumber();
    }
}

// Initialize tracking system when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    if (document.querySelector('#tracking-timeline') ||
        document.querySelector('[data-waybill]') ||
        window.location.pathname.includes('track')) {
        window.trackingSystem = new TrackingSystem();
    }
});

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = TrackingSystem;
}
