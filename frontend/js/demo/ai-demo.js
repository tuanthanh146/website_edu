/**
 * Demo chức năng AI cho EduAI
 */
document.addEventListener('DOMContentLoaded', function() {
    // Kiểm tra nếu trang demo tồn tại
    const aiDemoContainer = document.getElementById('ai-demo-container');
    if (!aiDemoContainer) return;

    const elements = {
        // Chat elements
        chatForm: document.getElementById('ai-chat-form'),
        promptInput: document.getElementById('ai-prompt-input'),
        chatHistory: document.getElementById('ai-chat-history'),
        loadingIndicator: document.getElementById('ai-loading-indicator'),
        
        // 3D Lesson generator elements
        lessonForm: document.getElementById('lesson-gen-form'),
        subjectInput: document.getElementById('subject-input'),
        topicInput: document.getElementById('topic-input'),
        difficultySelect: document.getElementById('difficulty-select'),
        lessonResults: document.getElementById('lesson-results'),
        lessonLoading: document.getElementById('lesson-loading')
    };

    // Khởi tạo sự kiện cho chat form
    if (elements.chatForm) {
        elements.chatForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const prompt = elements.promptInput.value.trim();
            
            if (!prompt) return;
            
            // Hiển thị prompt của người dùng
            appendChatMessage('user', prompt);
            elements.promptInput.value = '';
            
            // Hiển thị loading
            showElement(elements.loadingIndicator);
            
            // Gọi API
            ai3DHelper.generateContent(prompt)
                .then(response => {
                    // Hiển thị phản hồi từ AI
                    appendChatMessage('ai', response);
                })
                .catch(error => {
                    // Hiển thị lỗi
                    appendChatMessage('error', 'Lỗi: ' + error.message);
                })
                .finally(() => {
                    // Ẩn loading
                    hideElement(elements.loadingIndicator);
                });
        });
    }
    
    // Khởi tạo sự kiện cho form tạo bài học 3D
    if (elements.lessonForm) {
        elements.lessonForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const subject = elements.subjectInput.value.trim();
            const topic = elements.topicInput.value.trim();
            const difficulty = elements.difficultySelect.value;
            
            if (!subject || !topic) {
                alert('Vui lòng nhập đầy đủ môn học và chủ đề!');
                return;
            }
            
            // Hiển thị loading
            showElement(elements.lessonLoading);
            clearElement(elements.lessonResults);
            
            // Gọi API
            ai3DHelper.generate3DLessonIdeas(subject, topic, difficulty)
                .then(ideas => {
                    // Hiển thị kết quả
                    displayLessonIdeas(ideas);
                })
                .catch(error => {
                    // Hiển thị lỗi
                    elements.lessonResults.innerHTML = `
                        <div class="alert alert-danger">
                            Lỗi: ${error.message}
                        </div>
                    `;
                })
                .finally(() => {
                    // Ẩn loading
                    hideElement(elements.lessonLoading);
                });
        });
    }
    
    /**
     * Thêm tin nhắn vào lịch sử chat
     */
    function appendChatMessage(type, content) {
        if (!elements.chatHistory) return;
        
        const messageEl = document.createElement('div');
        messageEl.className = `chat-message ${type}-message`;
        
        if (type === 'user') {
            messageEl.innerHTML = `
                <div class="message-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="message-content">
                    <p>${escapeHtml(content)}</p>
                </div>
            `;
        } else if (type === 'ai') {
            messageEl.innerHTML = `
                <div class="message-avatar">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="message-content">
                    ${formatAIResponse(content)}
                </div>
            `;
        } else {
            messageEl.innerHTML = `
                <div class="message-content error">
                    <p>${escapeHtml(content)}</p>
                </div>
            `;
        }
        
        elements.chatHistory.appendChild(messageEl);
        
        // Cuộn xuống dưới
        elements.chatHistory.scrollTop = elements.chatHistory.scrollHeight;
    }
    
    /**
     * Hiển thị danh sách ý tưởng bài học 3D
     */
    function displayLessonIdeas(ideas) {
        if (!elements.lessonResults) return;
        
        if (!ideas || ideas.length === 0) {
            elements.lessonResults.innerHTML = `
                <div class="alert alert-info">
                    Không có ý tưởng nào được tạo ra. Vui lòng thử lại với chủ đề khác.
                </div>
            `;
            return;
        }
        
        let html = '<div class="row">';
        
        ideas.forEach((idea, index) => {
            html += `
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">${escapeHtml(idea.title)}</h5>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><strong>Mô tả:</strong> ${escapeHtml(idea.description)}</p>
                            
                            <h6>Mô hình 3D cần thiết:</h6>
                            <ul>
                                ${idea.required_models.map(model => `<li>${escapeHtml(model)}</li>`).join('')}
                            </ul>
                            
                            <h6>Hoạt động tương tác:</h6>
                            <ul>
                                ${idea.activities.map(activity => `<li>${escapeHtml(activity)}</li>`).join('')}
                            </ul>
                            
                            <h6>Mục tiêu học tập:</h6>
                            <ul>
                                ${idea.learning_objectives.map(objective => `<li>${escapeHtml(objective)}</li>`).join('')}
                            </ul>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-sm btn-success save-idea" data-idea-index="${index}">
                                <i class="fas fa-save"></i> Lưu ý tưởng
                            </button>
                            <button class="btn btn-sm btn-info create-3d" data-idea-index="${index}">
                                <i class="fas fa-cube"></i> Tạo mô hình 3D
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        elements.lessonResults.innerHTML = html;
        
        // Thêm sự kiện cho các nút
        document.querySelectorAll('.save-idea').forEach(button => {
            button.addEventListener('click', function() {
                const index = parseInt(this.getAttribute('data-idea-index'));
                saveIdea(ideas[index]);
            });
        });
        
        document.querySelectorAll('.create-3d').forEach(button => {
            button.addEventListener('click', function() {
                const index = parseInt(this.getAttribute('data-idea-index'));
                redirect3DCreator(ideas[index]);
            });
        });
    }
    
    /**
     * Lưu ý tưởng vào local storage
     */
    function saveIdea(idea) {
        try {
            // Lấy danh sách ý tưởng đã lưu
            let savedIdeas = JSON.parse(localStorage.getItem('saved_lesson_ideas') || '[]');
            
            // Thêm ý tưởng mới vào danh sách
            idea.saved_at = new Date().toISOString();
            savedIdeas.push(idea);
            
            // Lưu lại vào local storage
            localStorage.setItem('saved_lesson_ideas', JSON.stringify(savedIdeas));
            
            alert('Đã lưu ý tưởng thành công!');
        } catch (error) {
            console.error('Lỗi khi lưu ý tưởng:', error);
            alert('Có lỗi xảy ra khi lưu ý tưởng. Vui lòng thử lại!');
        }
    }
    
    /**
     * Chuyển hướng đến trang tạo 3D với thông tin ý tưởng
     */
    function redirect3DCreator(idea) {
        try {
            // Lưu thông tin ý tưởng vào session storage
            sessionStorage.setItem('current_lesson_idea', JSON.stringify(idea));
            
            // Chuyển hướng đến trang tạo 3D
            window.location.href = '/creator/3d?from=ai-suggestion';
        } catch (error) {
            console.error('Lỗi khi chuyển hướng:', error);
            alert('Có lỗi xảy ra. Vui lòng thử lại!');
        }
    }
    
    /**
     * Định dạng phản hồi AI với Markdown đơn giản
     */
    function formatAIResponse(text) {
        // Thay thế xuống dòng với thẻ <br>
        text = text.replace(/\n/g, '<br>');
        
        // Định dạng tiêu đề
        text = text.replace(/#{1,6}\s+(.*?)(?:<br>|$)/g, '<strong>$1</strong><br>');
        
        // Định dạng danh sách có thứ tự
        text = text.replace(/(\d+\.\s+)(.*?)(?:<br>|$)/g, '<span class="list-item">$1$2</span><br>');
        
        // Định dạng danh sách không thứ tự
        text = text.replace(/([-*]\s+)(.*?)(?:<br>|$)/g, '<span class="list-item">$1$2</span><br>');
        
        // Định dạng đoạn code
        text = text.replace(/```(.*?)```/gs, function(match, code) {
            return `<pre class="code-block">${escapeHtml(code.trim())}</pre>`;
        });
        
        // Định dạng inline code
        text = text.replace(/`([^`]+)`/g, '<code>$1</code>');
        
        return text;
    }
    
    /**
     * Escape HTML để tránh XSS
     */
    function escapeHtml(text) {
        if (!text) return '';
        return text
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }
    
    /**
     * Hiển thị phần tử
     */
    function showElement(element) {
        if (element) element.style.display = 'block';
    }
    
    /**
     * Ẩn phần tử
     */
    function hideElement(element) {
        if (element) element.style.display = 'none';
    }
    
    /**
     * Xóa nội dung phần tử
     */
    function clearElement(element) {
        if (element) element.innerHTML = '';
    }
}); 