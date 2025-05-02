using System;
using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace EduAIAdmin.Models
{
    public class Answer
    {
        public int Id { get; set; }

        [Required]
        [StringLength(1000)]
        public string Content { get; set; } = string.Empty;

        [Required]
        public int QuestionId { get; set; }

        public bool IsCorrect { get; set; }

        public DateTime CreatedAt { get; set; } = DateTime.Now;
        public DateTime? UpdatedAt { get; set; }

        // Navigation properties
        [ForeignKey("QuestionId")]
        public Question Question { get; set; }
    }
} 