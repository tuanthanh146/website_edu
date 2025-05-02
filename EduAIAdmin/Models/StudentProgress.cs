using System;
using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace EduAIAdmin.Models
{
    public class StudentProgress
    {
        public int Id { get; set; }

        [Required]
        public int StudentId { get; set; }

        [Required]
        public int LessonId { get; set; }

        public bool IsCompleted { get; set; }

        public int Score { get; set; }

        public DateTime CreatedAt { get; set; } = DateTime.Now;
        public DateTime? UpdatedAt { get; set; }

        // Navigation properties
        [ForeignKey("StudentId")]
        public Student Student { get; set; }

        [ForeignKey("LessonId")]
        public Lesson Lesson { get; set; }
    }
} 