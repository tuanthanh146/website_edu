using System;
using System.Collections.Generic;
using System.ComponentModel.DataAnnotations;

namespace EduAIAdmin.Models
{
    public class Subject
    {
        public int Id { get; set; }

        [Required]
        [StringLength(100)]
        public string Name { get; set; } = string.Empty;

        [StringLength(500)]
        public string Description { get; set; } = string.Empty;

        public DateTime CreatedAt { get; set; } = DateTime.Now;
        public DateTime? UpdatedAt { get; set; }

        // Navigation properties
        public ICollection<Course> Courses { get; set; }
        public ICollection<Topic> Topics { get; set; }
    }
} 