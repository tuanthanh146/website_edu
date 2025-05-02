using System;
using System.Collections.Generic;
using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace EduAIAdmin.Models
{
    public class Course
    {
        public int Id { get; set; }

        [Required]
        [StringLength(100)]
        public string Name { get; set; }

        [StringLength(500)]
        public string Description { get; set; }

        [Required]
        public int SubjectId { get; set; }

        [StringLength(50)]
        public string Level { get; set; } // "beginner", "intermediate", "advanced"

        [StringLength(100)]
        public string Title { get; set; } = string.Empty;

        public DateTime CreatedAt { get; set; } = DateTime.Now;
        public DateTime? UpdatedAt { get; set; }

        // Navigation properties
        [ForeignKey("SubjectId")]
        public Subject Subject { get; set; }
        public ICollection<Lesson> Lessons { get; set; }
    }
} 