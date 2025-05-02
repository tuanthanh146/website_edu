/**
 * JavaScript để tương tác với API AI
 */
class AI3DHelper {
    constructor() {
        this.apiEndpoint = '/api/ai';
        this.conversationHistory = [];
    }

    /**
     * Gửi yêu cầu tạo nội dung bằng AI
     * @param {string} prompt - Yêu cầu gửi tới AI
     * @param {object} options - Tùy chọn cấu hình
     * @returns {Promise<object>} Kết quả từ AI
     */
    async generateContent(prompt, options = {}) {
        try {
            const response = await fetch(`${this.apiEndpoint}/generate`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    prompt,
                    options
                })
            });

            const result = await response.json();
            
            if (!result.success) {
                throw new Error(result.message || 'Lỗi không xác định khi gọi AI');
            }
            
            // Lưu lịch sử nếu thành công
            this.conversationHistory.push({
                prompt,
                response: result.data,
                timestamp: new Date().toISOString()
            });
            
            return result.data;
        } catch (error) {
            console.error('Lỗi khi gọi AI:', error);
            throw error;
        }
    }
    
    /**
     * Tạo gợi ý bài học 3D
     * @param {string} subject - Môn học
     * @param {string} topic - Chủ đề
     * @param {string} difficulty - Độ khó
     * @returns {Promise<Array>} Danh sách gợi ý
     */
    async generate3DLessonIdeas(subject, topic, difficulty = 'medium') {
        try {
            const response = await fetch(`${this.apiEndpoint}/generate-3d-lessons`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    subject,
                    topic,
                    difficulty
                })
            });

            const result = await response.json();
            
            if (!result.success) {
                throw new Error(result.message || 'Lỗi không xác định khi tạo gợi ý bài học 3D');
            }
            
            return result.data.parsed_ideas || [];
        } catch (error) {
            console.error('Lỗi khi tạo gợi ý bài học 3D:', error);
            throw error;
        }
    }

    /**
     * Lấy lịch sử trò chuyện từ server
     * @param {number} userId - ID người dùng
     * @param {number} limit - Số lượng kết quả tối đa
     * @returns {Promise<Array>} Lịch sử trò chuyện
     */
    async getConversationHistory(userId, limit = 10) {
        try {
            const response = await fetch(`${this.apiEndpoint}/history?user_id=${userId}&limit=${limit}`);
            const result = await response.json();
            
            if (!result.success) {
                throw new Error(result.message || 'Lỗi không xác định khi lấy lịch sử');
            }
            
            this.conversationHistory = result.data;
            return result.data;
        } catch (error) {
            console.error('Lỗi khi lấy lịch sử trò chuyện:', error);
            return [];
        }
    }
    
    /**
     * Lấy lịch sử trò chuyện cục bộ
     * @returns {Array} Lịch sử trò chuyện cục bộ
     */
    getLocalHistory() {
        return this.conversationHistory;
    }
    
    /**
     * Xóa lịch sử trò chuyện cục bộ
     */
    clearLocalHistory() {
        this.conversationHistory = [];
    }
}

// Khởi tạo instance toàn cục
const ai3DHelper = new AI3DHelper();

// Export để sử dụng ở nơi khác
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ai3DHelper;
} 