using System;
using System.Collections.Generic;
using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace EduAIAdmin.Models
{
    public class Lesson
    {
        public int Id { get; set; }

        [Required]
        [StringLength(100)]
        public string Title { get; set; } = string.Empty;

        [StringLength(2000)]
        public string Content { get; set; } = string.Empty;

        [Required]
        public int CourseId { get; set; }

        public int Order { get; set; }

        public DateTime CreatedAt { get; set; } = DateTime.Now;
        public DateTime? UpdatedAt { get; set; }

        // Navigation properties
        [ForeignKey("CourseId")]
        public Course Course { get; set; }
        public ICollection<StudentProgress> StudentProgresses { get; set; }
    }
} 