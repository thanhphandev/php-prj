function formatMessageContent(content) {
    // 1. Format code blocks: Wraps text between triple backticks with <pre> and <code> tags for block-level code
    content = content.replace(/```([^`]+)```/g, (match, code) => {
        return `<pre class="bg-gray-100 p-3 rounded-md my-2 overflow-x-auto"><code>${code}</code></pre>`;
    });
    
    // 2. Format inline code: Wraps text between single backticks with <code> tags for inline code
    content = content.replace(/`([^`]+)`/g, (match, code) => {
        return `<code class="bg-gray-100 px-1 rounded">${code}</code>`;
    });
    
    // 3. Format bold text: Wraps text between double asterisks with <strong> tags for bold
    content = content.replace(/\*\*([^*]+)\*\*/g, (match, boldText) => {
        return `<strong>${boldText}</strong>`;
    });
    
    // 4. Format italic text: Wraps text between single asterisks with <em> tags for italic
    content = content.replace(/\*([^*]+)\*/g, (match, italicText) => {
        return `<em>${italicText}</em>`;
    });
    
    // 5. Convert URLs to links: Matches URLs and wraps them in <a> tags with target="_blank"
    const urlRegex = /(https?:\/\/[^\s]+)/g;
    content = content.replace(urlRegex, (match) => {
        return `<a href="${match}" target="_blank" class="text-blue-600 hover:underline">${match}</a>`;
    });
    
    // 6. Convert newlines to <br> tags for better formatting in HTML (to preserve line breaks)
    content = content.replace(/\n/g, '<br>');
    
    return content;
}

function updateRequestsLeft(remainingRequests) {
    $('#requestLeft').text('Bạn còn ' + remainingRequests + ' lượt');
}