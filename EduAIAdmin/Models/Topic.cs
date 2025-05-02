using System;
using System.Collections.Generic;
using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace EduAIAdmin.Models
{
    public class Topic
    {
        public int Id { get; set; }

        [Required]
        [StringLength(100)]
        public string Name { get; set; } = string.Empty;

        [StringLength(500)]
        public string Description { get; set; } = string.Empty;

        [Required]
        public int SubjectId { get; set; }

        public DateTime CreatedAt { get; set; } = DateTime.Now;
        public DateTime? UpdatedAt { get; set; }

        // Navigation properties
        [ForeignKey("SubjectId")]
        public Subject Subject { get; set; }
        public ICollection<Question> Questions { get; set; }
    }
} 