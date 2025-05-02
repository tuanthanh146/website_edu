using System;
using System.Collections.Generic;
using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace EduAIAdmin.Models
{
    public class Question
    {
        public int Id { get; set; }

        [Required]
        [StringLength(1000)]
        public string Content { get; set; } = string.Empty;

        [Required]
        public int TopicId { get; set; }

        [StringLength(50)]
        public string Difficulty { get; set; } = string.Empty; // "easy", "medium", "hard"

        public DateTime CreatedAt { get; set; } = DateTime.Now;
        public DateTime? UpdatedAt { get; set; }

        // Navigation properties
        [ForeignKey("TopicId")]
        public Topic Topic { get; set; }
        public ICollection<Answer> Answers { get; set; }
    }
} 