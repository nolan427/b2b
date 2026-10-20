/**
 * Auto-detect base path for subdirectory installations
 */
(function() {
    // Get the current script's directory
    const scripts = document.getElementsByTagName('script');
    const currentScript = scripts[scripts.length - 1];
    const scriptPath = currentScript.src;
    
    // Extract base path from script location
    let basePath = window.location.pathname;
    const lastSlash = basePath.lastIndexOf('/');
    if (lastSlash > 0) {
        basePath = basePath.substring(0, lastSlash);
    } else {
        basePath = '';
    }
    
    // Store globally
    window.APP_BASE_PATH = basePath || '';
    window.APP_API_BASE = window.APP_BASE_PATH + '/api';
    
    console.log('Base path:', window.APP_BASE_PATH);
})();

/**
 * Helper function to build API URLs
 */
function apiUrl(endpoint) {
    const base = window.APP_API_BASE || '/api';
    return base + '/' + endpoint.replace(/^\//, '');
}
