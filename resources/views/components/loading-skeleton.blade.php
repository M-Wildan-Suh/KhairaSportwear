<div class="skeleton-card">
    <div class="skeleton skeleton-img mb-3" style="height: 200px; border-radius: 12px;"></div>
    <div class="skeleton skeleton-text mb-2" style="height: 20px; width: 70%;"></div>
    <div class="skeleton skeleton-text mb-2" style="height: 16px; width: 100%;"></div>
    <div class="skeleton skeleton-text mb-2" style="height: 16px; width: 80%;"></div>
    <div class="d-flex justify-content-between mt-3">
        <div class="skeleton skeleton-button" style="height: 38px; width: 45%;"></div>
        <div class="skeleton skeleton-button" style="height: 38px; width: 45%;"></div>
    </div>
</div>

<style>
.skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

.skeleton-card {
    background: white;
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
}

@keyframes loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}
</style>