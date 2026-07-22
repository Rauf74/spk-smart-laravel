export function initCommonUI() {
    // Hide page loader on load if present
    const loader = document.getElementById('pageLoader');
    if (loader) {
        loader.style.display = 'none';
    }
}
