function formatMessageContent(content) {
    if (!content || typeof content !== 'string') return '';

    try {
        content = formatTables(content);
        content = formatLists(content);
        content = formatHeadings(content);
        content = formatBlockquotes(content);
        content = formatHorizontalRules(content);
        content = formatInlineStyles(content);
        content = formatLinks(content);

        // Không gọi escapeHtml toàn bộ ở đây
        return content;
    } catch (error) {
        console.error('Error formatting content:', error);
        // Nếu lỗi, ta escape toàn bộ content (chỉ trường hợp lỗi)
        return escapeHtml(content);
    }
}

function escapeHtml(html) {
    return html
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}


function formatTables(content) {
    const tableRegex = /^\|(.+)\|\s*\n\|([-:\s|]+)\n((?:\|.*\|\s*\n?)*)/gm;

    return content.replace(tableRegex, (match, headerRow, separatorRow, bodyRows) => {
        try {
            const headers = headerRow.split('|').map(h => h.trim()).filter(Boolean);
            const alignments = separatorRow.split('|').map(cell => {
                const c = cell.trim();
                if (c.startsWith(':') && c.endsWith(':')) return 'center';
                if (c.endsWith(':')) return 'right';
                return 'left';
            }).filter((_, i) => i < headers.length);

            const rows = bodyRows.trim().split('\n').map(row => {
                return row.split('|').map(cell => cell.trim()).filter(Boolean);
            });

            let html = '<div class="overflow-x-auto my-6">\n';
            html += '<table class="min-w-full border border-gray-200 divide-y divide-gray-200">\n';

            // Thead
            html += '<thead class="bg-gray-50">\n<tr>\n';
            headers.forEach((header, i) => {
                const alignClass = `text-${alignments[i] || 'left'}`;
                html += `<th class="px-6 py-3 ${alignClass} text-xs font-medium text-gray-500 uppercase tracking-wider">${escapeHtml(header)}</th>\n`;
            });
            html += '</tr>\n</thead>\n';

            // Tbody
            html += '<tbody class="bg-white divide-y divide-gray-200">\n';
            rows.forEach((row, idx) => {
                const bgClass = idx % 2 === 0 ? 'bg-white' : 'bg-gray-50';
                html += `<tr class="${bgClass}">\n`;

                // Cẩn thận nếu row ít hơn header
                for (let i = 0; i < headers.length; i++) {
                    const cell = row[i] || '';
                    const alignClass = `text-${alignments[i] || 'left'}`;
                    html += `<td class="px-6 py-4 whitespace-nowrap ${alignClass} text-sm text-gray-900">${escapeHtml(cell)}</td>\n`;
                }

                html += '</tr>\n';
            });
            html += '</tbody>\n</table>\n</div>';

            return html;
        } catch (error) {
            console.error('Error formatting table:', error);
            return match;
        }
    });
}

/**
 * Formats markdown lists into HTML lists with nested support
 * @param {string} content - Content containing markdown lists
 * @returns {string} - Content with HTML lists
 */
function formatLists(content) {
    const processList = (lines, indentLevel = 0) => {
        let html = '';
        let currentListType = null;
        let currentList = [];

        for (let i = 0; i < lines.length; i++) {
            const line = lines[i];
            const indentMatch = line.match(/^(\s*)/);
            const currentIndent = indentMatch[0].length / 2;

            if (currentIndent < indentLevel) {
                break;
            }

            const ulMatch = line.match(/^\s*[-*+]\s+(.+)/);
            const olMatch = line.match(/^\s*\d+\.\s+(.+)/);

            if (ulMatch || olMatch) {
                const itemText = (ulMatch || olMatch)[1];
                const listType = ulMatch ? 'ul' : 'ol';

                if (currentListType && currentListType !== listType) {
                    html += closeList(currentListType, currentList);
                    currentList = [];
                }

                currentListType = listType;
                currentList.push({ text: itemText, indent: currentIndent });

                // Look for nested lists
                const nestedLines = [];
                while (i + 1 < lines.length && lines[i + 1].match(/^\s+/)) {
                    nestedLines.push(lines[++i]);
                }

                if (nestedLines.length) {
                    currentList[currentList.length - 1].nested = processList(nestedLines, currentIndent + 1);
                }
            }
        }

        if (currentList.length) {
            html += closeList(currentListType, currentList);
        }

        return html;
    };

    const closeList = (type, items) => {
        const className = type === 'ul' ? 'list-disc' : 'list-decimal';
        let html = `<${type} class="${className} pl-6 my-4 space-y-2">\n`;
        items.forEach(item => {
            html += `<li class="text-gray-900">${escapeHtml(item.text)}${item.nested || ''}</li>\n`;
        });
        return html + `</${type}>\n`;
    };

    return content.replace(/^((?:\s*(?:[-*+]|\d+\.)\s+[^\n]+\n)+)/gm, match => {
        const lines = match.trim().split('\n');
        return processList(lines);
    });
}

/**
 * Formats markdown headings
 * @param {string} content - Content with markdown headings
 * @returns {string} - Content with HTML headings
 */
function formatHeadings(content) {
    return content.replace(/^(#{1,6})\s+(.+)$/gm, (match, hashes, text) => {
        const level = hashes.length;
        const sizes = ['text-2xl', 'text-xl', 'text-lg', 'text-base', 'text-sm', 'text-xs'];
        const weights = ['font-bold', 'font-bold', 'font-semibold', 'font-semibold', 'font-medium', 'font-medium'];
        return `<h${level} class="${sizes[level - 1]} ${weights[level - 1]} my-4 text-gray-900">${escapeHtml(text)}</h${level}>`;
    });
}

/**
 * Formats markdown blockquotes
 * @param {string} content - Content with markdown blockquotes
 * @returns {string} - Content with HTML blockquotes
 */
function formatBlockquotes(content) {
    return content.replace(/^>\s+(.+)$/gm, (match, text) => {
        return `<blockquote class="border-l-4 border-gray-300 pl-4 py-2 my-4 italic text-gray-600">${escapeHtml(text)}</blockquote>`;
    });
}

/**
 * Formats horizontal rules
 * @param {string} content - Content with markdown horizontal rules
 * @returns {string} - Content with HTML horizontal rules
 */
function formatHorizontalRules(content) {
    return content.replace(/^\s*([-*_]{3,})\s*$/gm, '<hr class="my-6 border-t border-gray-200">');
}

/**
 * Formats inline markdown styles (bold, italic, strikethrough, inline code)
 * @param {string} content - Content with inline markdown
 * @returns {string} - Content with HTML inline styles
 */
function formatInlineStyles(content) {
    return content
        .replace(/\*\*([^*]+?)\*\*/g, '<strong class="font-bold">$1</strong>')
        .replace(/\*([^*]+?)\*/g, '<em class="italic">$1</em>')
        .replace(/~~([^~]+?)~~/g, '<del class="line-through">$1</del>')
        .replace(/`([^`\n]+?)`/g, (match, code) => `<code class="bg-gray-100 px-1 rounded text-sm font-mono">${escapeHtml(code)}</code>`);
}

/**
 * Formats markdown links
 * @param {string} content - Content with markdown links
 * @returns {string} - Content with HTML links
 */
function formatLinks(content) {
    return content
        .replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline">$1</a>')
        .replace(/(?<![\[>])((?:https?:\/\/|www\.)[^\s<]+)/g, '<a href="$1" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline">$1</a>');
}

/**
 * Copies code to clipboard
 * @param {HTMLElement} button - The clicked button element
 */
function copyToClipboard(button) {
    try {
        const code = button.closest('.relative').querySelector('code').textContent;
        navigator.clipboard.writeText(code).then(() => {
            const originalText = button.textContent;
            button.textContent = 'Copied!';
            button.classList.add('bg-green-500', 'text-white');
            setTimeout(() => {
                button.textContent = originalText;
                button.classList.remove('bg-green-500', 'text-white');
            }, 2000);
        });
    } catch (error) {
        console.error('Failed to copy code:', error);
    }
}

function updateRequestsLeft(remainingRequests) {
    $('#requestLeft').text('Bạn còn ' + remainingRequests + ' lượt');
}