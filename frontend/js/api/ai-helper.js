/**
 * EduAI Helper - Quản lý tương tác với API AI 
 */

const ai3DHelper = (function() {
    // API Endpoints
    const API_ENDPOINTS = {
        generateContent: '/api/ai/generate',
        generateLessonIdeas: '/api/ai/generate-lesson-ideas'
    };

    // CSRF Token từ meta tag
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    /**
     * Gửi yêu cầu tới API
     * @param {string} endpoint - Đường dẫn API
     * @param {Object} data - Dữ liệu gửi đi
     * @returns {Promise<any>} - Promise chứa kết quả
     */
    const fetchAPI = async (endpoint, data) => {
        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(data)
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Lỗi kết nối đến máy chủ');
            }

            return await response.json();
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    };

    /**
     * Mô phỏng phản hồi từ AI (dùng tạm trong giai đoạn demo)
     * @param {string} prompt - Câu hỏi của người dùng
     * @returns {Promise<string>} - Phản hồi
     */
    const mockAIResponse = async (prompt) => {
        // Giả lập độ trễ
        await new Promise(resolve => setTimeout(resolve, 1000));

        // Một số câu trả lời mẫu dựa trên từ khóa
        if (prompt.toLowerCase().includes('3d')) {
            return "Mô hình 3D trong giáo dục giúp học sinh hiểu rõ hơn về các khái niệm trừu tượng qua hình ảnh trực quan. Công nghệ 3D cho phép học sinh xoay, phóng to, thu nhỏ và tương tác với các mô hình, tạo nên trải nghiệm học tập sâu hơn so với sách giáo khoa truyền thống.";
        } else if (prompt.toLowerCase().includes('stem')) {
            return "STEM là viết tắt của Science (Khoa học), Technology (Công nghệ), Engineering (Kỹ thuật) và Mathematics (Toán học). Giáo dục STEM tích hợp bốn lĩnh vực này vào một mô hình học tập liên ngành. Mô hình 3D là công cụ tuyệt vời cho giáo dục STEM vì giúp hiển thị các nguyên lý khoa học phức tạp theo cách trực quan và tương tác.";
        } else if (prompt.toLowerCase().includes('vật lý') || prompt.toLowerCase().includes('physics')) {
            return "Giảng dạy Vật lý với mô hình 3D rất hiệu quả. Bạn có thể sử dụng mô hình 3D để minh họa:\n- Chuyển động của các vật thể\n- Trường điện từ\n- Cấu trúc nguyên tử\n- Sóng và dao động\n- Các nguyên lý cơ học\n\nMô hình 3D giúp học sinh hiểu rõ hơn về các khái niệm trừu tượng và thấy được ứng dụng thực tế của lý thuyết.";
        } else if (prompt.toLowerCase().includes('hóa học') || prompt.toLowerCase().includes('chemistry')) {
            return "Mô hình 3D trong dạy Hóa học giúp học sinh hiểu rõ về:\n- Cấu trúc phân tử\n- Liên kết hóa học\n- Phản ứng hóa học\n- Cấu trúc tinh thể\n- Đồng phân hình học\n\nViệc có thể xoay, phóng to và quan sát các phân tử từ nhiều góc độ giúp học sinh hiểu rõ về không gian ba chiều và tương tác giữa các nguyên tử.";
        } else {
            return "Cảm ơn câu hỏi của bạn. Tôi có thể giúp bạn tìm hiểu về cách sử dụng mô hình 3D trong giáo dục, gợi ý bài giảng, hoặc giải thích các khái niệm liên quan đến STEM. Bạn có thể hỏi cụ thể hơn về môn học hoặc chủ đề bạn quan tâm không?";
        }
    };

    /**
     * Mô phỏng tạo ý tưởng bài học 3D (dùng tạm trong giai đoạn demo)
     * @param {string} subject - Môn học 
     * @param {string} topic - Chủ đề
     * @param {string} difficulty - Độ khó
     * @returns {Promise<Array>} - Danh sách ý tưởng
     */
    const mockLessonIdeas = async (subject, topic, difficulty) => {
        // Giả lập độ trễ
        await new Promise(resolve => setTimeout(resolve, 2000));

        // Ý tưởng mẫu cho Vật lý
        if (subject.toLowerCase() === 'vật lý') {
            return [
                {
                    title: `Khám phá ${topic} qua mô hình 3D tương tác`,
                    description: `Bài học sử dụng mô hình 3D để minh họa các nguyên lý cơ bản của ${topic}, giúp học sinh hiểu sâu hơn qua trải nghiệm trực quan và tương tác.`,
                    activities: [
                        `Quan sát và tương tác với mô hình 3D về ${topic}`,
                        'Thí nghiệm ảo để kiểm chứng lý thuyết',
                        'Thảo luận nhóm về kết quả quan sát',
                        'Tạo báo cáo phân tích dựa trên dữ liệu thu thập'
                    ]
                },
                {
                    title: `Thí nghiệm ảo: Khám phá quy luật ${topic}`,
                    description: `Học sinh thực hiện thí nghiệm ảo với mô hình 3D để khám phá và hiểu rõ các quy luật của ${topic} mà không cần thiết bị phức tạp.`,
                    activities: [
                        'Giới thiệu lý thuyết cơ bản qua video',
                        `Thực hiện thí nghiệm ảo về ${topic} theo nhóm`,
                        'Thu thập và phân tích dữ liệu',
                        'Trình bày kết quả và kết luận khoa học'
                    ]
                }
            ];
        }
        // Ý tưởng mẫu cho Hóa học
        else if (subject.toLowerCase() === 'hóa học') {
            return [
                {
                    title: `Khám phá cấu trúc phân tử trong ${topic}`,
                    description: `Sử dụng mô hình 3D để minh họa cấu trúc phân tử và liên kết hóa học trong ${topic}, giúp học sinh hiểu rõ về không gian ba chiều của phân tử.`,
                    activities: [
                        'Quan sát mô hình 3D của các phân tử',
                        'So sánh cấu trúc không gian của các phân tử khác nhau',
                        'Phân tích ảnh hưởng của cấu trúc đến tính chất',
                        'Xây dựng mô hình phân tử đơn giản'
                    ]
                },
                {
                    title: `Mô phỏng phản ứng hóa học: ${topic}`,
                    description: `Sử dụng công nghệ 3D để mô phỏng quá trình phản ứng hóa học ở cấp độ phân tử, giúp học sinh hiểu rõ cơ chế phản ứng.`,
                    activities: [
                        'Quan sát mô phỏng 3D của quá trình phản ứng',
                        'Thay đổi các điều kiện phản ứng và quan sát kết quả',
                        'Viết phương trình hóa học dựa trên quan sát',
                        'Thảo luận về ứng dụng thực tế của phản ứng'
                    ]
                }
            ];
        }
        // Ý tưởng mẫu cho Sinh học
        else if (subject.toLowerCase() === 'sinh học') {
            return [
                {
                    title: `Khám phá cấu trúc 3D trong ${topic}`,
                    description: `Sử dụng mô hình 3D để minh họa cấu trúc phức tạp của tế bào, cơ quan hoặc hệ thống sinh học, giúp học sinh hiểu rõ về mối quan hệ giữa cấu trúc và chức năng.`,
                    activities: [
                        'Quan sát và tương tác với mô hình 3D',
                        'So sánh cấu trúc của các hệ thống sinh học khác nhau',
                        'Mổ xẻ ảo các cơ quan hoặc cấu trúc sinh học',
                        'Tạo poster giải thích mối liên hệ giữa cấu trúc và chức năng'
                    ]
                },
                {
                    title: `Mô phỏng quá trình sinh học: ${topic}`,
                    description: `Sử dụng công nghệ 3D để mô phỏng các quá trình sinh học phức tạp, giúp học sinh hiểu rõ diễn biến theo thời gian.`,
                    activities: [
                        'Quan sát mô phỏng 3D của quá trình sinh học',
                        'Điều khiển tốc độ mô phỏng để quan sát chi tiết',
                        'Xác định các giai đoạn quan trọng của quá trình',
                        'Thảo luận về ảnh hưởng của các yếu tố môi trường'
                    ]
                }
            ];
        }
        // Ý tưởng mẫu mặc định cho các môn khác
        else {
            return [
                {
                    title: `Khám phá ${topic} qua mô hình 3D`,
                    description: `Bài học sử dụng công nghệ 3D để minh họa các khái niệm ${subject} phức tạp, giúp học sinh hiểu sâu hơn qua trải nghiệm trực quan.`,
                    activities: [
                        `Quan sát và tương tác với mô hình 3D về ${topic}`,
                        'Thực hiện các hoạt động khám phá theo nhóm',
                        'Thảo luận về các khái niệm chính',
                        'Tạo dự án nhỏ áp dụng kiến thức đã học'
                    ]
                },
                {
                    title: `Dự án tương tác: ${topic} trong thế giới thực`,
                    description: `Học sinh sử dụng mô hình 3D để khám phá ứng dụng thực tế của ${topic} trong đời sống và khoa học.`,
                    activities: [
                        'Nghiên cứu các ứng dụng thực tế',
                        'Làm việc với mô hình 3D để hiểu nguyên lý hoạt động',
                        'Đề xuất cải tiến hoặc ứng dụng mới',
                        'Trình bày kết quả dưới dạng triển lãm ảo'
                    ]
                }
            ];
        }
    };

    // Public API
    return {
        /**
         * Tạo nội dung từ AI dựa trên prompt
         * @param {string} prompt - Câu hỏi người dùng
         * @returns {Promise<string>} - Phản hồi
         */
        generateContent: async function(prompt) {
            try {
                // Phiên bản production sẽ gọi API thật
                // const response = await fetchAPI(API_ENDPOINTS.generateContent, { prompt });
                // return response.content;

                // Phiên bản demo sử dụng dữ liệu mẫu
                return await mockAIResponse(prompt);
            } catch (error) {
                console.error('Generate Content Error:', error);
                throw error;
            }
        },

        /**
         * Tạo ý tưởng bài học 3D
         * @param {string} subject - Môn học
         * @param {string} topic - Chủ đề
         * @param {string} difficulty - Độ khó
         * @returns {Promise<Array>} - Danh sách ý tưởng
         */
        generate3DLessonIdeas: async function(subject, topic, difficulty) {
            try {
                // Phiên bản production sẽ gọi API thật
                // const response = await fetchAPI(API_ENDPOINTS.generateLessonIdeas, {
                //     subject,
                //     topic,
                //     difficulty
                // });
                // return response.ideas;

                // Phiên bản demo sử dụng dữ liệu mẫu
                return await mockLessonIdeas(subject, topic, difficulty);
            } catch (error) {
                console.error('Generate Lesson Ideas Error:', error);
                throw error;
            }
        }
    };
})(); 