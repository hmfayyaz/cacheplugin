// custom-script.js

console.log("here");
// Function to capture the entire page content
function capturePageContent() {
    var pageContent = document.documentElement.outerHTML; // Capture entire HTML content
    sendToServer(pageContent);
}

// Function to send the captured content to the server using AJAX
function sendToServer(content) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', scriptData.ajax_url); // AJAX endpoint
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status === 200) {
            console.log('Content sent successfully.');
        } else {
            console.error('Failed to send content.');
        }
    };
    xhr.send('action=save_page_content&content=' + encodeURIComponent(content) + '&security=' + encodeURIComponent(scriptData.nonce)); // Send content to server
}

// Capture the page content when the page is fully loaded
window.onload = function() {
    capturePageContent();
};
