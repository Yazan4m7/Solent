@props(['doctorName'])
<style>
    /* Main Container */
    .elegant-workflow-container {
        padding: 24px;
        background-color: #f8f9fa;
        min-height: 100vh;
        font-family: 'Roboto', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    .elegant-jobs-wrapper {
        max-width: 1200px;
        margin: 0 auto;
    }

    .elegant-build-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    /* Build Card */
    .elegant-build-item {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border: 1px solid #e9ecef;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .elegant-build-item:hover {
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
        transform: translateY(-1px);
    }

    /* Build Header */
    .elegant-header-section {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px 24px;
        background: linear-gradient(135deg, #2CA8FF, #1e88e5);
        color: white;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .elegant-header-section:hover {
        background: linear-gradient(135deg, #1e88e5, #2CA8FF);
    }

    .elegant-header-left {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .elegant-checkbox-wrapper {
        position: relative;
    }

    .elegant-checkbox-input {
        width: 20px;
        height: 20px;
        border: 2px solid rgba(255, 255, 255, 0.8);
        border-radius: 4px;
        background: transparent;
        cursor: pointer;
        transition: all 0.2s ease;
        accent-color: white;
    }

    .elegant-checkbox-input:checked {
        background: rgba(255, 255, 255, 0.2);
        border-color: white;
    }

    .elegant-build-name {
        font-size: 24px;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .elegant-header-center {
        flex: 1;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .elegant-summary-info {
        text-align: center;
    }

    .elegant-total-units {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 4px;
    }

    .elegant-creation-date {
        font-size: 14px;
        opacity: 0.9;
        font-weight: 400;
    }

    .elegant-header-right {
        display: flex;
        align-items: center;
    }

    .elegant-expand-icon {
        padding: 8px;
        border-radius: 50%;
        transition: all 0.3s ease;
    }

    .elegant-expand-icon:hover {
        background: rgba(255, 255, 255, 0.1);
    }

    .elegant-expand-icon i {
        transition: transform 0.3s ease;
    }

    .elegant-build-item.elegant-expanded .elegant-expand-icon i {
        transform: rotate(180deg);
    }

    /* Build Details */
    .elegant-details-panel {
        overflow: hidden;
        transition: all 0.3s ease;
        max-height: 0;
    }

    .elegant-build-item.elegant-expanded .elegant-details-panel {
        max-height: 1000px;
    }

    .elegant-cases-container {
        padding: 0;
    }

    /* Case Rows */
    .elegant-case-entry {
        display: grid;
        grid-template-columns: 120px 1fr 1fr 60px;
        align-items: center;
        padding: 16px 24px;
        border-bottom: 1px solid #f1f3f4;
        transition: all 0.2s ease;
    }

    .elegant-case-entry:hover {
        background-color: #f8f9fa;
    }

    .elegant-case-entry:last-child {
        border-bottom: none;
    }

    .elegant-unit-count {
        font-weight: 600;
        color: #2CA8FF;
        font-size: 16px;
    }

    .elegant-patient-info {
        font-weight: 500;
        color: #2c3e50;
        font-size: 15px;
    }

    .elegant-doctor-info {
        color: #6c757d;
        font-size: 14px;
    }

    .elegant-action-area {
        display: flex;
        justify-content: center;
    }

    .elegant-view-button {
        width: 36px;
        height: 36px;
        border: none;
        background: #f8f9fa;
        border-radius: 8px;
        color: #6c757d;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .elegant-view-button:hover {
        background: #2CA8FF;
        color: white;
        transform: scale(1.1);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .elegant-workflow-container {
            padding: 16px;
        }

        .elegant-header-section {
            padding: 16px;
            flex-direction: column;
            gap: 12px;
            text-align: center;
        }

        .elegant-case-entry {
            grid-template-columns: 1fr;
            gap: 8px;
            text-align: center;
            padding: 16px;
        }

        .elegant-unit-count {
            order: 1;
            font-size: 18px;
        }

        .elegant-patient-info {
            order: 2;
        }

        .elegant-doctor-info {
            order: 3;
        }

        .elegant-action-area {
            order: 4;
            margin-top: 12px;
        }
    }

    /* Animation for expand/collapse */
    @keyframes elegantSlideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .elegant-build-item.elegant-expanded .elegant-case-entry {
        animation: elegantSlideDown 0.3s ease forwards;
    }

</style>
<div class="elegant-workflow-container">
    <div class="elegant-jobs-wrapper">
        <div class="elegant-build-list">
            <div class="elegant-build-item">
                <!-- Build Header -->
                <div class="elegant-header-section" onclick="toggleElegantBuildDetails(this)">
                    <div class="elegant-header-left">
                        <div class="elegant-checkbox-wrapper" onclick="event.stopPropagation();">
                            <input type="checkbox" name="jobId[]" value="2" data-group-id="51"
                                   class="elegant-checkbox-input" onclick="event.stopPropagation();" checked disabled>
                            <input type="hidden" name="jobId[]" value="2" class="elegant-hidden-value">
                        </div>
                        <div class="elegant-build-name">R5</div>
                    </div>

                    <div class="elegant-header-center">
                        <div class="elegant-summary-info">
                            <div class="elegant-total-units">10 units</div>
                            <div class="elegant-creation-date">Jun 15, 08:39</div>
                        </div>
                    </div>

                    <div class="elegant-header-right">
                        <div class="elegant-expand-icon">
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                </div>

                <!-- Build Details -->
                <div class="elegant-details-panel">
                    <div class="elegant-cases-container">
                        <!-- Case Row 1 -->
                        <div class="elegant-case-entry">
                            <div class="elegant-unit-count">9 unit(s)</div>
                            <div class="elegant-patient-info">Ms. Bettie Will</div>
                            <div class="elegant-doctor-info">Dr Test</div>
                            <div class="elegant-action-area">
                                <button class="elegant-view-button" onclick="YSH_openSlidePanel(9)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Case Row 2 -->
                        <div class="elegant-case-entry">
                            <div class="elegant-unit-count">1 unit(s)</div>
                            <div class="elegant-patient-info">Sophia Terry</div>
                            <div class="elegant-doctor-info">سنان غيشان</div>
                            <div class="elegant-action-area">
                                <button class="elegant-view-button" onclick="YSH_openSlidePanel(10)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    function toggleElegantBuildDetails(header) {
        const buildCard = header.closest('.elegant-build-item');
        buildCard.classList.toggle('elegant-expanded');
    }

</script>
